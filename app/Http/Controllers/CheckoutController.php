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

        return view('users.checkout', compact(
            'cartItems', 'total', 'totalShipping', 'totalExGst', 'totalGst'
        ));
    }

    // public function store(Request $request)
    // {
    //     $lookupEmail = $request->email ?? Auth::user()?->email;
    //     $existingUser = User::where('email', $lookupEmail)->first();

    //     $request->validate([
    //         'name'           => 'required|string|max:255',
    //         'email'          => Auth::check() ? 'nullable|email|max:255' : 'required|email|max:255',
    //         'mobile'         => [
    //             'required', 'string', 'max:15',
    //             Rule::unique('users', 'mobile')->ignore($existingUser?->id),
    //         ],
    //         'state'          => 'required|exists:states,name',
    //         'zipcode'        => ['required', 'regex:/^[1-9][0-9]{5}$/'],
    //         'city'           => ['required', 'string', 'regex:/^[a-zA-Z\s]+$/', 'max:50'],
    //         'locality'       => 'required|string|max:255',
    //         'address'        => 'required|string|max:255',
    //         'payment_method' => ['required', 'in:cod,prepaid'],
    //     ]);

    //     try {
    //         // ── Load cart items ──
    //         if (Auth::check()) {
    //             $cartItems = Cart::where('user_id', Auth::id())
    //                 ->with(['product', 'productAttribute'])
    //                 ->get();
    //         } else {
    //             $cartItems = collect(session()->get('cart', []))->map(fn($item) => (object) $item);
    //         }

    //         if ($cartItems->isEmpty()) {
    //             return redirect()->route('cart.view')->with('error', 'Your cart is empty.');
    //         }

    //         // ── Validate stock for every item before touching the DB ──
    //         foreach ($cartItems as $item) {
    //             $atrId = $item->product_attribute_id ?? null;

    //             if ($atrId) {
    //                 $atr = ProductAttribute::find($atrId);
    //                 if (!$atr || $atr->status !== 'enable') {
    //                     return back()->with('error', 'A selected variant is no longer available. Please review your cart.');
    //                 }
    //                 if ($atr->stock < $item->quantity) {
    //                     return back()->with('error', 'Insufficient stock for variant "' . $atr->size . '" of ' . ($item->product->name ?? $item->name ?? ''));
    //                 }
    //             } else {
    //                 $product = Product::find($item->product_id);
    //                 if (!$product) {
    //                     return back()->with('error', 'A product in your cart is no longer available.');
    //                 }
    //                 if ($product->stock < $item->quantity) {
    //                     return back()->with('error', 'Insufficient stock for ' . $product->name);
    //                 }
    //             }
    //         }

    //         DB::beginTransaction();

    //         $email = $request->email ?? Auth::user()->email;

    //         // ── Upsert user ──
    //         $userData = [
    //             'uuid'     => str()->uuid()->toString(),
    //             'name'     => $request->name,
    //             'email'    => $email,
    //             'mobile'   => $request->mobile,
    //             'address'  => $request->address,
    //             'locality' => $request->locality,
    //             'city'     => $request->city,
    //             'state'    => $request->state,
    //             'zipcode'  => $request->zipcode,
    //         ];

    //         $mobileConflict = User::where('mobile', $request->mobile)
    //             ->where('email', '!=', $email)
    //             ->exists();

    //         if ($mobileConflict) {
    //             return back()->withInput()->withErrors([
    //                 'mobile' => 'This mobile number is linked to another account.'
    //             ]);
    //         }

    //         if (!Auth::check()) {
    //             $user = User::updateOrCreate(['email' => $email], array_merge($userData, [
    //                 'password'       => bcrypt($request->mobile),
    //                 'local_password' => $request->mobile,
    //                 'ip_address'     => $request->ip(),
    //             ]));
    //             Auth::login($user);
    //         } else {
    //             $user = User::updateOrCreate(['email' => $email], $userData);
    //         }

    //         if (!$user) {
    //             DB::rollBack();
    //             return back()->with('error', 'Something went wrong creating your account.');
    //         }

    //         // ── Recalculate shipping with final address ──
    //         $totalShipping = 0;
    //         foreach ($cartItems as $item) {
    //             $product = Product::find($item->product_id);
    //             if (!$product) continue;

    //             $atrId        = $item->product_attribute_id ?? null;
    //             $sellingPrice = $atrId
    //                 ? (ProductAttribute::find($atrId)?->selling_price ?? $product->selling)
    //                 : $product->selling;

    //             $shippingCharge = calculateShippingCharge(
    //                 $user->country_id ?? null,
    //                 $user->state_id   ?? null,
    //                 $product->gram_weight * $item->quantity,
    //                 $sellingPrice * $item->quantity
    //             );

    //             // Update shipping in cart/session
    //             if (Auth::check()) {
    //                 Cart::where('user_id', Auth::id())
    //                     ->where('product_id', $item->product_id)
    //                     ->where('product_attribute_id', $atrId)
    //                     ->update(['shipping_charge' => $shippingCharge]);
    //             } else {
    //                 $cartKey  = $item->product_id . ($atrId ? '_atr_' . $atrId : '');
    //                 $cartData = session()->get('cart', []);
    //                 if (isset($cartData[$cartKey])) {
    //                     $cartData[$cartKey]['shipping_charge'] = $shippingCharge;
    //                     session()->put('cart', $cartData);
    //                 }
    //             }

    //             $totalShipping += $shippingCharge;
    //         }

    //         $total = $cartItems->sum(fn($i) => $i->quantity * $i->price);

    //         // ── Create order ──
    //         $order = Order::create([
    //             'uuid'            => str()->uuid()->toString(),
    //             'user_id'         => Auth::id(),
    //             'name'            => $request->name,
    //             'email'           => $email,
    //             'mobile'          => $request->mobile,
    //             'address'         => $request->address,
    //             'locality'        => $request->locality,
    //             'city'            => $request->city,
    //             'state'           => $request->state,
    //             'zipcode'         => $request->zipcode,
    //             'total'           => $total,
    //             'shipping_charge' => $totalShipping,
    //             'status'          => 'pending',
    //             'payment_method'  => $request->payment_method,
    //             'ip_address'      => $request->ip(),
    //         ]);

    //         // ── Create order items + decrement stock ──
    //         foreach ($cartItems as $item) {
    //             $atrId = $item->product_attribute_id ?? null;

    //             OrderItem::create([
    //                 'order_id'             => $order->id,
    //                 'product_id'           => $item->product_id,
    //                 'product_attribute_id' => $atrId,
    //                 'quantity'             => $item->quantity,
    //                 'price'                => $item->price,
    //             ]);

    //             // if ($atrId) {
    //             //     ProductAttribute::where('id', $atrId)->decrement('stock', $item->quantity);
    //             // } else {
    //             //     Product::where('id', $item->product_id)->decrement('stock', $item->quantity);
    //             // }
    //         }

    //         // ── Clear cart ──
    //         Auth::check()
    //             ? Cart::where('user_id', Auth::id())->delete()
    //             : session()->forget('cart');

    //         DB::commit();

    //         $emails = [
    //             $order->email,
    //             'order@everwearindustries.com'
    //         ];

    //         // ── Send invoice email ──
    //         try {
    //             Mail::to($emails)->send(new OrderInvoiceMail($order->load('items.product')));
    //         } catch (\Throwable $e) {
    //             \Log::error('Order invoice mail failed: ' . $e->getMessage());
    //         }

    //         return redirect()->route('order.confirmation', $order)
    //             ->with('success', 'Order placed successfully!');

    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         return back()->with('error', 'Checkout processing failed. Please try again.' . $e->getMessage());
    //     }
    // }

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
            // ── Load cart items ────────────────────────────────────────────────────
            if (Auth::check()) {
                $cartItems = Cart::where('user_id', Auth::id())->with(['product', 'productAttribute'])->get();
            } else {
                $cartItems = collect(session()->get('cart', []))->map(fn($item) => (object) $item);
            }

            if ($cartItems->isEmpty()) {
                return redirect()->route('cart.view')->with('error', 'Your cart is empty.');
            }

            // ── Validate stock for every item before touching the DB ──────────────
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

            // ── Upsert user ────────────────────────────────────────────────────────
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
                return back()->withInput()->withErrors([
                    'mobile' => 'This mobile number is linked to another account.'
                ]);
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

            // ── Recalculate shipping with final address ────────────────────────────
            $totalShipping    = 0.0;
            $totalShippingGst = 0.0;

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

                // Update shipping in cart / session
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

            // Shipping GST at 18 % (same formula as admin update)
            $totalShippingGst = $totalShipping > 0 ? round($totalShipping * 18 / 100, 2) : 0.0;

            // ── Compute per-item GST figures (mirrors admin computeItemTotals) ─────
            //
            $subtotalInclGst = 0.0;
            $subtotalExGst   = 0.0;
            $taxableValue    = 0.0;
            $taxAmount       = 0.0;
            $itemsTotal      = 0.0;

            $computedItems = [];   // carry per-item figures for OrderItem inserts below

            foreach ($cartItems as $item) {
                $product = Product::find($item->product_id);
                $gstRate = $product ? (float) ($product->gst ?? 0) : 0.0;
                $price   = (float) $item->price;
                $qty     = (int)   $item->quantity;

                $priceExGst     = $gstRate > 0 ? round($price / (1 + $gstRate / 100), 6) : $price;
                $lineTotalExGst = round($priceExGst * $qty, 4);

                // No discounts at checkout — taxable = full ex-GST line total
                $taxableLineTotal    = $lineTotalExGst;
                $taxablePricePerUnit = $qty > 0 ? round($taxableLineTotal / $qty, 4) : 0.0;
                $itemTax             = round($taxableLineTotal * ($gstRate / 100), 2);
                $lineTotal           = round($taxableLineTotal + $itemTax, 2);

                $subtotalInclGst += round($price * $qty, 4);
                $subtotalExGst   += round($priceExGst * $qty, 4);
                $taxableValue    += $taxableLineTotal;
                $taxAmount       += $itemTax;
                $itemsTotal      += $lineTotal;

                $computedItems[] = [
                    'item'          => $item,
                    'gst_rate'      => $gstRate,
                    'taxable_price' => $taxablePricePerUnit,
                    'tax_amount'    => $itemTax,
                    'line_total'    => $lineTotal,
                ];
            }

            // Round order-level aggregates (mirrors admin update rounding)
            $subtotalInclGst = round($subtotalInclGst, 2);
            $subtotalExGst   = round($subtotalExGst,   2);
            $taxableValue    = round($taxableValue,     2);
            $taxAmount       = round($taxAmount,        2);
            $itemsTotal      = round($itemsTotal,       2);

            // Grand total = Σ line_totals + shipping (incl-GST) + shipping GST
            $grandTotal = round($itemsTotal + $totalShipping + $totalShippingGst, 2);

            // ── Create order ───────────────────────────────────────────────────────
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
                // ── Item subtotals (before discount) ──
                'subtotal_incl_gst'   => $subtotalInclGst,   // incl-GST, before discount
                'subtotal_ex_gst'     => $subtotalExGst,      // ex-GST,   before discount
                // ── No coupon / manual discounts at checkout ──
                'coupon_discount'     => 0.00,
                'manual_discount'     => 0.00,
                // ── After discount (same as subtotal here) ──
                'taxable_value'       => $taxableValue,
                'tax_amount'          => $taxAmount,
                // ── Shipping ──
                'shipping_charge'     => $totalShipping,
                'shipping_charge_gst' => $totalShippingGst,
                // ── Grand total ──
                'total'               => $grandTotal,
                'status'              => 'pending',
                'payment_method'      => $request->payment_method,
            ]);

            // ── Create order items with full GST breakdown ─────────────────────────
            foreach ($computedItems as $computed) {
                $item  = $computed['item'];
                $atrId = $item->product_attribute_id ?? null;

                OrderItem::create([
                    'order_id'               => $order->id,
                    'product_id'             => $item->product_id,
                    'product_attribute_id'   => $atrId,
                    'quantity'               => $item->quantity,
                    'price'                  => $item->price,
                    // ── No discounts at checkout ──
                    'coupon_id'              => null,
                    'coupon_discount'        => 0.00,
                    'manual_discount_type'   => null,
                    'manual_discount_value'  => 0.00,
                    'manual_discount_amount' => 0.00,
                    'manual_discount_note'   => null,
                    'discount_amount'        => 0.00,
                    // ── GST figures ──
                    'taxable_price'          => $computed['taxable_price'],
                    'tax_rate'               => $computed['gst_rate'],
                    'tax_amount'             => $computed['tax_amount'],
                    'line_total'             => $computed['line_total'],
                ]);

                // if ($atrId) {
                //     ProductAttribute::where('id', $atrId)->decrement('stock', $item->quantity);
                // } else {
                //     Product::where('id', $item->product_id)->decrement('stock', $item->quantity);
                // }
            }

            // ── Clear cart ─────────────────────────────────────────────────────────
            Auth::check()
                ? Cart::where('user_id', Auth::id())->delete()
                : session()->forget('cart');

            DB::commit();

            // ── Send invoice email ─────────────────────────────────────────────────
            try {
                Mail::to([$order->email, 'order@everwearindustries.com'])
                    ->send(new OrderInvoiceMail($order->load('items.product')));
            } catch (\Throwable $e) {
                \Log::error('Order invoice mail failed: ' . $e->getMessage());
            }

            return redirect()->route('order.confirmation', $order)
                ->with('success', 'Order placed successfully!');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Checkout processing failed. Please try again.' . $e->getMessage());
        }
    }

    /**
     * GET /checkout/coupons
     * Returns coupons that are valid AND applicable to at least one cart product.
     */
    public function availableCoupons(Request $request)
    {
        // Load cart products
        if (Auth::check()) {
            $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
        } else {
            $cartItems = collect(session()->get('cart', []))->map(fn($i) => (object) $i);
        }

        $productIds = $cartItems->map(fn($i) => $i->product_id)->unique()->values()->toArray();
        $products   = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $now = now();

        $coupons = Coupon::where('is_active', true)
            ->where(fn($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', $now))
            ->where(fn($q) => $q->whereNull('max_uses')->orWhereRaw('used_count < max_uses'))
            ->get()
            ->filter(function (Coupon $coupon) use ($products) {
                // Keep coupon if it applies to at least one product in cart
                foreach ($products as $product) {
                    if ($coupon->isApplicableToProduct($product)) {
                        return true;
                    }
                }
                return false;
            })
            ->map(fn(Coupon $c) => [
                'code'        => $c->code,
                'description' => $c->description,
                'type'        => $c->discount_type,
                'value'       => $c->discount_value,
                'max_discount'=> $c->max_discount_amount,
                'min_order'   => $c->min_order_amount,
                'expires_at'  => $c->expires_at?->format('d M Y'),
            ])
            ->values();

        return response()->json($coupons);
    }

    /**
     * POST /checkout/apply-coupon
     * Validates a coupon code and returns per-item discount preview.
     */
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:50',
        ]);

        $code   = strtoupper(trim($request->coupon_code));
        $coupon = Coupon::where('code', $code)->first();

        if (! $coupon || ! $coupon->isValid()) {
            return response()->json(['error' => "Coupon '{$code}' is invalid or expired."], 422);
        }

        // Load cart items
        if (Auth::check()) {
            $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
        } else {
            $cartItems = collect(session()->get('cart', []))->map(fn($i) => (object) $i);
        }

        $breakdown    = [];
        $totalDiscount = 0.0;

        foreach ($cartItems as $item) {
            $product = Product::find($item->product_id);
            if (! $product) continue;

            if (! $coupon->isApplicableToProduct($product)) {
                $breakdown[] = [
                    'product_id'   => $item->product_id,
                    'product_name' => $product->name,
                    'applicable'   => false,
                    'discount'     => 0,
                ];
                continue;
            }

            $gstRate        = (float) ($product->gst ?? 0);
            $priceExGst     = $gstRate > 0
                ? round($item->price / (1 + $gstRate / 100), 6)
                : (float) $item->price;
            $lineTotalExGst = round($priceExGst * $item->quantity, 4);
            $discount       = $coupon->calculateDiscount($lineTotalExGst);

            $totalDiscount += $discount;

            $breakdown[] = [
                'product_id'   => $item->product_id,
                'product_name' => $product->name,
                'applicable'   => true,
                'discount'     => round($discount, 2),
            ];
        }

        if ($totalDiscount <= 0) {
            return response()->json([
                'error' => 'This coupon is not applicable to any product in your cart.',
            ], 422);
        }

        return response()->json([
            'code'           => $coupon->code,
            'description'    => $coupon->description,
            'total_discount' => round($totalDiscount, 2),
            'breakdown'      => $breakdown,
        ]);
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