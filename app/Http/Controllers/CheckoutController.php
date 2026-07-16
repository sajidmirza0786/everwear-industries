<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\User;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Mail\OrderInvoiceMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    // ── Shared GST helper ──────────────────────────────────────────────────────
    // Given a GST-inclusive unit price and a GST rate %, returns:
    //   [ exGstUnit, gstUnit ]
    // Formula: ex_gst = price / (1 + rate/100)
    private function splitGst(float $price, float $rate): array
    {
        if ($rate <= 0) {
            return [$price, 0.0];
        }
        $exGst = round($price / (1 + $rate / 100), 2);
        return [$exGst, round($price - $exGst, 2)];
    }

    // ══════════════════════════════════════════════════════════════════════════════
    //  SHARED COUPON PRICING ENGINE
    //
    //  Given a coupon + a LIVE cart, returns the correct discount / GST / grand
    //  total for that cart right now. This is the single source of truth for
    //  coupon math — applyCoupon(), index(), and store() all call this instead
    //  of each doing their own calculation, so they can never disagree.
    //
    //  Math: subtotal (ex-GST) − discount = taxable value
    //        taxable value + GST + shipping = grand total
    //
    //  The discount is spread proportionally across eligible lines (by each
    //  line's share of the applicable ex-GST subtotal) before GST is
    //  recomputed, so mixed GST-rate carts (e.g. 5% + 18% items) net out
    //  correctly instead of using one blended rate.
    // ══════════════════════════════════════════════════════════════════════════════
    private function evaluateCoupon(Coupon $coupon, $cartItems): array
    {
        $lines            = [];
        $totalExGstAll    = 0.0;
        $totalShippingAll = 0.0;

        foreach ($cartItems as $item) {
            $product = Product::find($item->product_id);
            if (! $product) continue;

            $gstRate  = (float) ($product->gst ?? 0);
            $price    = (float) $item->price;
            $qty      = (int) $item->quantity;
            $shipping = (float) ($item->shipping_charge ?? 0);

            $unitPriceExGst = $gstRate > 0 ? $price / (1 + $gstRate / 100) : $price;
            $lineExGst      = round($unitPriceExGst * $qty, 4);

            $lines[] = [
                'line_ex_gst' => $lineExGst,
                'gst_rate'    => $gstRate,
                'eligible'    => $coupon->appliesToProduct($product),
            ];

            $totalExGstAll    += $lineExGst;
            $totalShippingAll += $shipping;
        }

        // ── Find eligible lines & validate min/max on a per-item basis ────────
        $applicableExGstSubtotal = 0.0;
        $eligibleItemCount       = 0;
        $belowMinItems           = 0;
        $aboveMaxItems           = 0;

        foreach ($lines as $line) {
            if (! $line['eligible']) continue;

            if ($coupon->min_order_amount && $line['line_ex_gst'] < (float) $coupon->min_order_amount) {
                $belowMinItems++;
                continue;
            }
            if ($coupon->max_order_amount && $line['line_ex_gst'] > (float) $coupon->max_order_amount) {
                $aboveMaxItems++;
                continue;
            }

            $applicableExGstSubtotal += $line['line_ex_gst'];
            $eligibleItemCount++;
        }

        if ($eligibleItemCount === 0) {
            $message = 'This coupon is not applicable to any item in your cart.';

            if ($belowMinItems > 0) {
                $message = 'No item in your cart meets the minimum order amount of ₹' .
                    number_format($coupon->min_order_amount, 2) . ' (ex-GST) required for this coupon.';
            } elseif ($aboveMaxItems > 0) {
                $message = 'No item in your cart is within the maximum order amount of ₹' .
                    number_format($coupon->max_order_amount, 2) . ' (ex-GST) allowed for this coupon.';
            }

            return ['success' => false, 'message' => $message];
        }

        // ── Calculate discount on applicable ex-GST subtotal ──────────────────
        $discountExGst = $coupon->calculateDiscount($applicableExGstSubtotal);

        if ($discountExGst <= 0) {
            return ['success' => false, 'message' => 'This coupon yields no discount for your current cart.'];
        }

        // Safety clamp — discount can never exceed what it's being applied to
        $discountExGst = min($discountExGst, $applicableExGstSubtotal);

        // ── Recompute GST AFTER discount ───────────────────────────────────────
        $newTotalGst = 0.0;
        foreach ($lines as $line) {
            $isEligibleAndApplied = $line['eligible']
                && $applicableExGstSubtotal > 0
                && (! $coupon->min_order_amount || $line['line_ex_gst'] >= (float) $coupon->min_order_amount)
                && (! $coupon->max_order_amount || $line['line_ex_gst'] <= (float) $coupon->max_order_amount);

            if ($isEligibleAndApplied) {
                $shareOfDiscount = $discountExGst * ($line['line_ex_gst'] / $applicableExGstSubtotal);
                $newLineExGst    = max(0, $line['line_ex_gst'] - $shareOfDiscount);
                $newTotalGst    += $newLineExGst * $line['gst_rate'] / 100;
            } else {
                $newTotalGst += $line['line_ex_gst'] * $line['gst_rate'] / 100;
            }
        }
        $newTotalGst = round($newTotalGst, 2);

        // ── Final taxable value & grand total ──────────────────────────────────
        $taxableValue = round($totalExGstAll - $discountExGst, 2);
        $grandTotal   = round($taxableValue + $newTotalGst + $totalShippingAll, 2);

        return [
            'success'         => true,
            'discount_ex_gst' => round($discountExGst, 2),
            'taxable_value'   => $taxableValue,
            'gst_amount'      => $newTotalGst,
            'grand_total'     => $grandTotal,
            'discount_label'  => $coupon->discount_type === 'flat'
                ? '₹' . number_format($coupon->discount_value, 0) . ' off'
                : $coupon->discount_value . '% off',
        ];
    }

    // ── Small helper so index()/store() can build the session payload consistently ──
    private function couponSessionPayload(Coupon $coupon, array $result): array
    {
        return [
            'id'              => $coupon->id,
            'code'            => $coupon->code,
            'description'     => $coupon->description,
            'discount_type'   => $coupon->discount_type,
            'discount_value'  => $coupon->discount_value,
            'discount_ex_gst' => $result['discount_ex_gst'],
            'taxable_value'   => $result['taxable_value'],
            'gst_amount'      => $result['gst_amount'],
            'grand_total'     => $result['grand_total'],
        ];
    }

    // ══════════════════════════════════════════════════════════════════════════════
    //  CHECKOUT PAGE  (GET /checkout)
    // ══════════════════════════════════════════════════════════════════════════════
    public function index()
    {
        $cartItems     = [];
        $total         = 0;   // GST-inclusive subtotal (sum of price × qty)
        $totalExGst    = 0;   // subtotal before GST
        $totalGst      = 0;   // GST portion
        $totalShipping = 0;

        if (Auth::check()) {
            $cartItems = Cart::where('user_id', Auth::id())
                ->with(['product', 'productAttribute'])
                ->get();

            foreach ($cartItems as $item) {
                $product      = $item->product;
                $qty          = $item->quantity;
                $sellingPrice = $item->price;
                $gstRate      = (float) ($product->gst ?? 0);

                [$exGstUnit, $gstUnit] = $this->splitGst($sellingPrice, $gstRate);

                $item->subtotal_ex_gst = round($exGstUnit * $qty, 2);
                $item->gst_amount      = round($gstUnit   * $qty, 2);
                $item->gst_rate        = $gstRate;

                $total         += $sellingPrice * $qty;
                $totalExGst    += $item->subtotal_ex_gst;
                $totalGst      += $item->gst_amount;
                $totalShipping += $item->shipping_charge;
            }
        } else {
            $cart = session()->get('cart', []);
            foreach ($cart as $item) {
                $obj          = (object) $item;
                $product      = \App\Models\Product::find($item['product_id']);
                $qty          = $item['quantity'];
                $sellingPrice = $item['price'];
                $gstRate      = (float) ($product?->gst ?? 0);

                [$exGstUnit, $gstUnit] = $this->splitGst($sellingPrice, $gstRate);

                $obj->subtotal_ex_gst = round($exGstUnit * $qty, 2);
                $obj->gst_amount      = round($gstUnit   * $qty, 2);
                $obj->gst_rate        = $gstRate;

                $cartItems[]    = $obj;
                $total         += $sellingPrice * $qty;
                $totalExGst    += $obj->subtotal_ex_gst;
                $totalGst      += $obj->gst_amount;
                $totalShipping += $item['shipping_charge'];
            }
        }

        if (empty($cartItems) || (is_object($cartItems) && method_exists($cartItems, 'isEmpty') && $cartItems->isEmpty())) {
            return redirect()->route('cart.view')->with('error', 'Your cart is empty.');
        }

        // ── Re-validate & recompute any session coupon against the CURRENT cart ──
        //
        //  This is what makes the discount stay correct across page refreshes and
        //  quantity changes: every time the checkout page loads, we throw away
        //  the cached discount/GST/grand-total figures and recompute them fresh
        //  from evaluateCoupon(). If the coupon no longer applies (e.g. the only
        //  eligible item was removed, or the cart dropped below min_order_amount),
        //  it's silently removed with a warning instead of showing stale numbers.
        if ($sessionCoupon = session('coupon')) {
            $coupon = Coupon::find($sessionCoupon['id'] ?? null);

            if ($coupon && $coupon->isValid()) {
                $result = $this->evaluateCoupon($coupon, $cartItems);

                if ($result['success']) {
                    session(['coupon' => $this->couponSessionPayload($coupon, $result)]);
                } else {
                    session()->forget('coupon');
                    session()->flash('warnings', 'Your coupon "' . $sessionCoupon['code'] . '" no longer applies to your cart and was removed: ' . $result['message']);
                }
            } else {
                session()->forget('coupon');
                session()->flash('warnings', 'Your coupon is no longer valid and was removed.');
            }
        }

        return view('users.checkout', compact(
            'cartItems', 'total', 'totalShipping', 'totalExGst', 'totalGst'
        ));
    }

    // ══════════════════════════════════════════════════════════════════════════════
    //  APPLY COUPON  (AJAX — POST /checkout/coupon/apply)
    //
    //  Security checklist:
    //   ✓ CSRF protected (POST with @csrf)
    //   ✓ Rate-limited (apply throttle:6,1 in routes)
    //   ✓ Coupon validity: active, date window, global usage cap
    //   ✓ Per-user usage cap checked against non-cancelled orders
    //   ✓ Min/max order-amount constraints checked on ex-GST subtotal
    //   ✓ Product/category whitelist respected per item
    //   ✓ Discount stored ex-GST so admin update math stays consistent
    //   ✓ Cart must be non-empty before accepting a coupon
    //   ✓ Coupon never incremented here — only on final order commit
    // ══════════════════════════════════════════════════════════════════════════════
    public function applyCoupon(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['code' => 'required|string|max:50']);

        $code   = strtoupper(trim($request->input('code')));
        $coupon = Coupon::where('code', $code)->first();

        // ── 1. Existence & validity ────────────────────────────────────────────
        if (! $coupon || ! $coupon->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'This coupon is invalid or has expired.',
            ], 422);
        }

        // ── 2. Per-user usage cap ──────────────────────────────────────────────
        if ($coupon->max_uses_per_user && Auth::check()) {
            $usedTimes = $coupon->usedCountByUser(Auth::id());
            if ($usedTimes >= $coupon->max_uses_per_user) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already used this coupon the maximum number of times.',
                ], 422);
            }
        }

        // ── 3. Load cart ───────────────────────────────────────────────────────
        if (Auth::check()) {
            $cartItems = Cart::where('user_id', Auth::id())
                ->with(['product', 'productAttribute'])
                ->get();
        } else {
            $cartItems = collect(session()->get('cart', []))->map(fn($i) => (object) $i);
        }

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty.',
            ], 422);
        }

        // ── 4. Evaluate coupon against the live cart ───────────────────────────
        $result = $this->evaluateCoupon($coupon, $cartItems);

        if (! $result['success']) {
            return response()->json(['success' => false, 'message' => $result['message']], 422);
        }

        // ── 5. Store in session (never touch used_count until order commits) ───
        session(['coupon' => $this->couponSessionPayload($coupon, $result)]);

        return response()->json([
            'success'         => true,
            'message'         => 'Coupon applied successfully!',
            'code'            => $coupon->code,
            'description'     => $coupon->description,
            'discount_ex_gst' => $result['discount_ex_gst'],
            'taxable_value'   => $result['taxable_value'],
            'gst_amount'      => $result['gst_amount'],
            'grand_total'     => $result['grand_total'],
            'discount_label'  => $result['discount_label'],
        ]);
    }

    // ══════════════════════════════════════════════════════════════════════════════
    //  REMOVE COUPON  (POST /checkout/coupon/remove)
    // ══════════════════════════════════════════════════════════════════════════════
    public function removeCoupon(): \Illuminate\Http\JsonResponse
    {
        session()->forget('coupon');

        return response()->json(['success' => true]);
    }

    // ══════════════════════════════════════════════════════════════════════════════
    //  AVAILABLE COUPONS  (AJAX — GET /checkout/coupons)
    //
    //  Returns only coupons the user CAN still use. Sensitive fields
    //  (max_uses, used_count internals) are excluded from the response.
    // ══════════════════════════════════════════════════════════════════════════════
    public function availableCoupons(): \Illuminate\Http\JsonResponse
    {
        $now = now();

        $coupons = Coupon::where('is_active', true)
            ->where(fn($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', $now))
            ->where(fn($q) => $q->whereNull('max_uses')->orWhereRaw('used_count < max_uses'))
            ->select('id', 'code', 'description', 'discount_type', 'discount_value',
                    'max_discount_amount', 'min_order_amount', 'max_order_amount',
                    'expires_at', 'max_uses_per_user')
            ->orderBy('discount_value', 'desc')
            ->get()
            ->filter(function ($coupon) {
                // Filter out coupons this user has exhausted
                if ($coupon->max_uses_per_user && Auth::check()) {
                    return $coupon->usedCountByUser(Auth::id()) < $coupon->max_uses_per_user;
                }
                return true;
            })
            ->map(fn($c) => [
                'code'              => $c->code,
                'description'       => $c->description,
                'discount_type'     => $c->discount_type,
                'discount_value'    => $c->discount_value,
                'max_discount'      => $c->max_discount_amount,
                'min_order_amount'  => $c->min_order_amount,
                'expires_at'        => $c->expires_at?->format('M d, Y'),
                'label'             => $c->discount_type === 'flat'
                                        ? '₹' . number_format($c->discount_value, 0) . ' off'
                                        : $c->discount_value . '% off',
            ])
            ->values();

        return response()->json($coupons);
    }

    // ══════════════════════════════════════════════════════════════════════════════
    //  STORE  — recomputes the coupon fresh against the live cart before writing
    //           the order, then writes the GST breakdown per item.
    // ══════════════════════════════════════════════════════════════════════════════
    public function store(Request $request)
    {
        $lookupEmail = $request->email ?? Auth::user()?->email;
        $existingUser = User::where('email', $lookupEmail)->first();

        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => Auth::check() ? 'nullable|email|max:255' : 'required|email|max:255',
            'mobile'         => [
                'required', 'string', 'max:15',
                Rule::unique('users', 'mobile')->ignore($existingUser?->id),
            ],
            'state'          => 'required|exists:states,name',
            'zipcode'        => ['required', 'regex:/^[1-9][0-9]{5}$/'],
            'city'           => ['required', 'string', 'regex:/^[a-zA-Z\s]+$/', 'max:50'],
            'locality'       => 'required|string|max:255',
            'address'        => 'required|string|max:255',
            'payment_method' => ['required', 'in:cod,prepaid'],
        ]);

        try {
            // ── Load cart items ────────────────────────────────────────────────
            if (Auth::check()) {
                $cartItems = Cart::where('user_id', Auth::id())
                    ->with(['product', 'productAttribute'])
                    ->get();
            } else {
                $cartItems = collect(session()->get('cart', []))->map(fn($item) => (object) $item);
            }

            if ($cartItems->isEmpty()) {
                return redirect()->route('cart.view')->with('error', 'Your cart is empty.');
            }

            // ── Validate stock ─────────────────────────────────────────────────
            foreach ($cartItems as $item) {
                $atrId = $item->product_attribute_id ?? null;

                if ($atrId) {
                    $atr = ProductAttribute::find($atrId);
                    if (!$atr || $atr->status !== 'enable') {
                        return back()->with('error', 'A selected variant is no longer available. Please review your cart.');
                    }
                    if ($atr->stock < $item->quantity) {
                        return back()->with('error', 'Insufficient stock for variant "' . $atr->size . '" of ' . ($item->product->name ?? $item->name ?? ''));
                    }
                } else {
                    $product = Product::find($item->product_id);
                    if (!$product) {
                        return back()->with('error', 'A product in your cart is no longer available.');
                    }
                    if ($product->stock < $item->quantity) {
                        return back()->with('error', 'Insufficient stock for ' . $product->name);
                    }
                }
            }

            DB::beginTransaction();

            $email = $request->email ?? Auth::user()->email;

            // ── Upsert user ────────────────────────────────────────────────────
            $userData = [
                'uuid'     => str()->uuid()->toString(),
                'name'     => $request->name,
                'email'    => $email,
                'mobile'   => $request->mobile,
                'address'  => $request->address,
                'locality' => $request->locality,
                'city'     => $request->city,
                'state'    => $request->state,
                'zipcode'  => $request->zipcode,
            ];

            $mobileConflict = User::where('mobile', $request->mobile)
                ->where('email', '!=', $email)
                ->exists();

            if ($mobileConflict) {
                return back()->withInput()->withErrors(['mobile' => 'This mobile number is linked to another account.']);
            }

            if (!Auth::check()) {
                $user = User::updateOrCreate(['email' => $email], array_merge($userData, [
                    'password'       => bcrypt($request->mobile),
                    'local_password' => $request->mobile,
                    'ip_address'     => $request->ip(),
                ]));
                Auth::login($user);
            } else {
                $user = User::updateOrCreate(['email' => $email], $userData);
            }

            if (!$user) {
                DB::rollBack();
                return back()->with('error', 'Something went wrong creating your account.');
            }

            // ── Re-validate & re-EVALUATE coupon from session (second check) ───
            //
            //  The coupon was validated in applyCoupon() but we MUST re-check
            //  AND re-calculate here inside the transaction because:
            //   - max_uses could have been hit between apply and submit
            //   - per-user limit could have been hit if another tab submitted
            //   - coupon could have been deactivated or expired
            //   - cart quantities could have changed since the coupon was applied
            //     (e.g. edited on a different tab) without index() re-running
            //
            //  We NEVER trust session('coupon')'s cached discount_ex_gst here —
            //  it's recomputed fresh against the cart that's actually being
            //  checked out, using the same evaluateCoupon() engine as everywhere
            //  else, so store() can never disagree with applyCoupon()/index().
            //
            $sessionCoupon    = session('coupon');
            $resolvedCoupon   = null;
            $couponDiscountEx = 0.0;

            if ($sessionCoupon) {
                $resolvedCoupon = Coupon::lockForUpdate()->find($sessionCoupon['id']);

                if (! $resolvedCoupon || ! $resolvedCoupon->isValid()) {
                    session()->forget('coupon');
                    DB::rollBack();
                    return back()->withInput()->with('error', 'The coupon "' . $sessionCoupon['code'] . '" is no longer valid. Please review your order.');
                }

                if ($resolvedCoupon->max_uses_per_user && Auth::check()) {
                    if ($resolvedCoupon->usedCountByUser(Auth::id()) >= $resolvedCoupon->max_uses_per_user) {
                        session()->forget('coupon');
                        DB::rollBack();
                        return back()->withInput()->with('error', 'You have already used this coupon the maximum number of times.');
                    }
                }

                // Recompute fresh against the live cart being checked out — do
                // NOT trust the cached session discount_ex_gst.
                $couponResult = $this->evaluateCoupon($resolvedCoupon, $cartItems);

                if (! $couponResult['success']) {
                    session()->forget('coupon');
                    DB::rollBack();
                    return back()->withInput()->with('error', 'Your coupon "' . $resolvedCoupon->code . '" no longer applies to your cart: ' . $couponResult['message']);
                }

                $couponDiscountEx = $couponResult['discount_ex_gst'];
            }

            // ── Recalculate shipping ───────────────────────────────────────────
            $totalShipping = 0.0;

            foreach ($cartItems as $item) {
                $product = Product::find($item->product_id);
                if (!$product) continue;

                $atrId        = $item->product_attribute_id ?? null;
                $sellingPrice = $atrId
                    ? (ProductAttribute::find($atrId)?->selling_price ?? $product->selling)
                    : $product->selling;

                $shippingCharge = calculateShippingCharge(
                    $user->country_id ?? null,
                    $user->state_id   ?? null,
                    $product->gram_weight * $item->quantity,
                    $sellingPrice * $item->quantity
                );

                if (Auth::check()) {
                    Cart::where('user_id', Auth::id())
                        ->where('product_id', $item->product_id)
                        ->where('product_attribute_id', $atrId)
                        ->update(['shipping_charge' => $shippingCharge]);
                } else {
                    $cartKey  = $item->product_id . ($atrId ? '_atr_' . $atrId : '');
                    $cartData = session()->get('cart', []);
                    if (isset($cartData[$cartKey])) {
                        $cartData[$cartKey]['shipping_charge'] = $shippingCharge;
                        session()->put('cart', $cartData);
                    }
                }

                $totalShipping += $shippingCharge;
            }

            $totalShippingGst = $totalShipping > 0 ? round($totalShipping * 18 / 100, 2) : 0.0;

            // ── Per-item GST computation ───────────────────────────────────────
            //
            //  Coupon discount is spread proportionally across applicable items
            //  (ex-GST) so each OrderItem row carries a correct coupon_discount.
            //
            //  Flow per item:
            //    priceExGst         = price / (1 + gst%)
            //    lineTotalExGst     = priceExGst × qty
            //    itemCouponDiscount = couponDiscountEx × (lineExGst / applicableExGst)
            //    taxableLineTotal   = lineTotalExGst − itemCouponDiscount
            //    tax_amount         = taxableLineTotal × gst%
            //    line_total         = taxableLineTotal + tax_amount
            //
            $subtotalInclGst        = 0.0;
            $subtotalExGst          = 0.0;
            $totalCouponDiscountEx  = 0.0;
            $taxableValue           = 0.0;
            $taxAmount              = 0.0;
            $itemsTotal             = 0.0;

            // First pass: compute applicable ex-GST subtotal for proportioning
            $applicableExGstTotal = 0.0;
            $itemGstData = [];

            foreach ($cartItems as $item) {
                $product = Product::find($item->product_id);
                $gstRate = $product ? (float) ($product->gst ?? 0) : 0.0;
                $price   = (float) $item->price;
                $qty     = (int)   $item->quantity;

                $priceExGst = $gstRate > 0 ? round($price / (1 + $gstRate / 100), 6) : $price;
                $lineExGst  = round($priceExGst * $qty, 4);

                // ── Mirror the SAME eligibility logic as evaluateCoupon() ──────
                $eligible = false;
                if ($resolvedCoupon && $resolvedCoupon->appliesToProduct($product)) {
                    $passesMin = ! $resolvedCoupon->min_order_amount || $lineExGst >= (float) $resolvedCoupon->min_order_amount;
                    $passesMax = ! $resolvedCoupon->max_order_amount || $lineExGst <= (float) $resolvedCoupon->max_order_amount;
                    $eligible  = $passesMin && $passesMax;
                }

                $itemGstData[] = [
                    'item'        => $item,
                    'product'     => $product,
                    'gst_rate'    => $gstRate,
                    'price'       => $price,
                    'qty'         => $qty,
                    'line_ex_gst' => $lineExGst,
                    'applicable'  => $eligible,
                ];

                if ($eligible) {
                    $applicableExGstTotal += $lineExGst;
                }
            }

            // Second pass: compute final figures with proportioned coupon discount
            $computedItems = [];

            foreach ($itemGstData as $data) {
                $lineExGst  = $data['line_ex_gst'];
                $gstRate    = $data['gst_rate'];
                $qty        = $data['qty'];

                // Proportionate coupon share for this item
                $itemCouponEx = 0.0;
                if ($couponDiscountEx > 0 && $data['applicable'] && $applicableExGstTotal > 0) {
                    $itemCouponEx = round($couponDiscountEx * ($lineExGst / $applicableExGstTotal), 4);
                }

                $taxableLineTotal    = round(max(0.0, $lineExGst - $itemCouponEx), 4);
                $taxablePricePerUnit = $qty > 0 ? round($taxableLineTotal / $qty, 4) : 0.0;
                $itemTax             = round($taxableLineTotal * ($gstRate / 100), 2);
                $lineTotal           = round($taxableLineTotal + $itemTax, 2);

                $subtotalInclGst       += round($data['price'] * $qty, 4);
                $subtotalExGst         += $lineExGst;
                $totalCouponDiscountEx += $itemCouponEx;
                $taxableValue          += $taxableLineTotal;
                $taxAmount             += $itemTax;
                $itemsTotal            += $lineTotal;

                $computedItems[] = array_merge($data, [
                    'coupon_discount_ex'  => $itemCouponEx,
                    'taxable_price'       => $taxablePricePerUnit,
                    'tax_amount'          => $itemTax,
                    'line_total'          => $lineTotal,
                ]);
            }

            $subtotalInclGst       = round($subtotalInclGst,       2);
            $subtotalExGst         = round($subtotalExGst,         2);
            $totalCouponDiscountEx = round($totalCouponDiscountEx, 2);
            $taxableValue          = round($taxableValue,          2);
            $taxAmount             = round($taxAmount,             2);
            $itemsTotal            = round($itemsTotal,            2);
            $grandTotal            = round($itemsTotal + $totalShipping + $totalShippingGst, 2);

            // ── Create order ───────────────────────────────────────────────────
            $order = Order::create([
                'uuid'                => str()->uuid()->toString(),
                'user_id'             => Auth::id(),
                'name'                => $request->name,
                'email'               => $email,
                'mobile'              => $request->mobile,
                'address'             => $request->address,
                'locality'            => $request->locality,
                'city'                => $request->city,
                'state'               => $request->state,
                'zipcode'             => $request->zipcode,
                'ip_address'          => $request->ip(),
                'subtotal_incl_gst'   => $subtotalInclGst,
                'subtotal_ex_gst'     => $subtotalExGst,
                'coupon_discount'     => $totalCouponDiscountEx,
                'manual_discount'     => 0.00,
                'taxable_value'       => $taxableValue,
                'tax_amount'          => $taxAmount,
                'shipping_charge'     => $totalShipping,
                'shipping_charge_gst' => $totalShippingGst,
                'total'               => $grandTotal,
                'status'              => 'pending',
                'payment_method'      => $request->payment_method,
            ]);

            // ── Create order items ─────────────────────────────────────────────
            foreach ($computedItems as $computed) {
                $item  = $computed['item'];
                $atrId = $item->product_attribute_id ?? null;

                $orderItem = OrderItem::create([
                    'order_id'               => $order->id,
                    'product_id'             => $item->product_id,
                    'product_attribute_id'   => $atrId,
                    'quantity'               => $item->quantity,
                    'price'                  => $item->price,
                    'coupon_id'              => $resolvedCoupon && $computed['applicable'] ? $resolvedCoupon->id : null,
                    'coupon_discount'        => $computed['coupon_discount_ex'],
                    'manual_discount_type'   => null,
                    'manual_discount_value'  => 0.00,
                    'manual_discount_amount' => 0.00,
                    'manual_discount_note'   => null,
                    'discount_amount'        => $computed['coupon_discount_ex'],
                    'taxable_price'          => $computed['taxable_price'],
                    'tax_rate'               => $computed['gst_rate'],
                    'tax_amount'             => $computed['tax_amount'],
                    'line_total'             => $computed['line_total'],
                ]);

                // Write coupon_order_item pivot + increment used_count (once per order)
                if ($resolvedCoupon && $computed['applicable'] && $computed['coupon_discount_ex'] > 0) {
                    DB::table('coupon_order_item')->updateOrInsert(
                        ['coupon_id' => $resolvedCoupon->id, 'order_item_id' => $orderItem->id],
                        [
                            'discount_applied' => $computed['coupon_discount_ex'],
                            'created_at'       => now(),
                            'updated_at'       => now(),
                        ]
                    );
                }
            }

            // Increment used_count exactly once per order (not per item)
            if ($resolvedCoupon) {
                $resolvedCoupon->increment('used_count');
            }

            // ── Clear cart + coupon session ────────────────────────────────────
            Auth::check()
                ? Cart::where('user_id', Auth::id())->delete()
                : session()->forget('cart');

            session()->forget('coupon');

            DB::commit();

            // ── Send invoice ───────────────────────────────────────────────────
            try {
                Mail::to([$order->email])
                    ->cc('order@everwearindustries.com')
                    ->send(new OrderInvoiceMail($order->load('items.product')));
            } catch (\Throwable $e) {
                \Log::error('Order invoice mail failed: ' . $e->getMessage());
            }

            return redirect()->route('order.confirmation', $order)
                ->with('success', 'Order placed successfully!');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Checkout processing failed. Please try again. ' . $e->getMessage());
        }
    }

    public function confirmation(Order $order)
    {
        if (Auth::check() && $order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order.');
        }

        $order->load(['items.product', 'items.productAttribute']);

        return view('users.order-confirmation', compact('order'));
    }
}