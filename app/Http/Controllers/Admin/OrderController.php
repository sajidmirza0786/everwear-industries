<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\OrderItem;
use App\Models\AccessLog;
use App\Models\ProductAttribute;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\OrderInvoiceMail;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    // ══════════════════════════════════════════════════════════════════════════
    //  INDEX
    // ══════════════════════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        $query = Order::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('uuid', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($paymentMethod = $request->input('payment_method')) {
            $query->where('payment_method', $paymentMethod);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(30);

        return view('admin.orders.index', compact('orders'));
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  SHOW
    // ══════════════════════════════════════════════════════════════════════════

    public function show(Order $order)
    {
        $order->load(['items.product', 'items.productAttribute', 'items.coupon', 'user']);

        $order_logs = AccessLog::where('model_type', 'Order')
            ->where('model_id', $order->id)
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'order_page');

        $item_logs = AccessLog::where('model_type', 'OrderItem')
            ->where('tracking_id', $order->id)
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'item_page');

        return view('admin.orders.show', compact('order', 'order_logs', 'item_logs'));
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  EDIT
    // ══════════════════════════════════════════════════════════════════════════

    public function edit(Order $order)
    {
        $order->load(['items.product', 'items.productAttribute', 'items.coupon']);
        return view('admin.orders.edit', compact('order'));
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  UPDATE
    //
    //  Per-item discount flow (Indian GST §15):
    //
    //    price            → incl-GST unit price (as stored / entered)
    //    priceExGst       = price / (1 + gstRate/100)
    //    lineTotalExGst   = priceExGst × qty
    //
    //    couponDiscountEx → coupon calculates on ex-GST line total directly
    //    manualDiscountEx → manual flat/percent applied on ex-GST line total directly
    //
    //    taxableLineTotal = lineTotalExGst − couponDiscountEx − manualDiscountEx
    //    taxablePricePerUnit = taxableLineTotal / qty        (stored to 4dp)
    //    tax_amount       = taxableLineTotal × gstRate/100
    //    line_total       = taxableLineTotal + tax_amount
    //
    //  Order-level totals = Σ of all item line figures + shipping.
    // ══════════════════════════════════════════════════════════════════════════

    public function update(Request $request, Order $order)
    {
        $validatedData = $request->validate([
            // ── Core order fields ──────────────────────────────────────────────
            'status'          => ['required', 'string', 'in:completed,cancelled,in-transit'],
            'payment_method'  => ['required', 'string', 'in:prepaid,cod'],
            'name'            => ['required', 'string', 'max:255'],
            'mobile'          => ['required', 'string', 'max:20'],
            'state'           => ['nullable', 'string', 'max:255'],
            'city'            => ['nullable', 'string', 'max:255'],
            'zipcode'         => ['nullable', 'string', 'max:25'],
            'locality'        => ['nullable', 'string', 'max:255'],
            'address'         => ['required', 'string', 'max:500'],
            'shipping_charge' => ['nullable', 'numeric', 'min:0'],

            // ── Existing items ─────────────────────────────────────────────────
            'items'                         => ['nullable', 'array'],
            'items.*.id'                    => ['required', 'integer', 'exists:order_items,id'],
            'items.*.quantity'              => ['required', 'integer', 'min:1'],
            'items.*.price'                 => ['required', 'numeric', 'min:0'],
            'items.*._delete'               => ['nullable', 'boolean'],
            'items.*.coupon_code'           => ['nullable', 'string', 'max:50'],
            'items.*.manual_discount_type'  => ['nullable', 'in:flat,percent'],
            'items.*.manual_discount_value' => ['nullable', 'numeric', 'min:0'],
            'items.*.manual_discount_note'  => ['nullable', 'string', 'max:255'],

            // ── New items ──────────────────────────────────────────────────────
            'new_items'                               => ['nullable', 'array'],
            'new_items.*.product_id'                  => ['required', 'integer', 'exists:products,id'],
            'new_items.*.product_attribute_id'        => ['nullable', 'integer', 'exists:product_attributes,id'],
            'new_items.*.quantity'                    => ['required', 'integer', 'min:1'],
            'new_items.*.price'                       => ['required', 'numeric', 'min:0'],
            'new_items.*.coupon_code'                 => ['nullable', 'string', 'max:50'],
            'new_items.*.manual_discount_type'        => ['nullable', 'in:flat,percent'],
            'new_items.*.manual_discount_value'       => ['nullable', 'numeric', 'min:0'],
            'new_items.*.manual_discount_note'        => ['nullable', 'string', 'max:255'],
        ]);

        DB::beginTransaction();

        try {
            // ── 1. Handle existing items (update / delete) ─────────────────────
            if (! empty($validatedData['items'])) {
                foreach ($validatedData['items'] as $itemData) {
                    /** @var OrderItem $item */
                    $item = $order->items()->findOrFail($itemData['id']);

                    if (! empty($itemData['_delete'])) {
                        if ($item->coupon_id) {
                            $this->detachItemCoupon($item);
                        }
                        accessLog('delete', 'OrderItem', $item, auth()->id(), 'OrderItem', $item->id, $order->id);
                        $item->delete();
                        continue;
                    }

                    $gstRate = (float) ($item->product?->gst ?? 0);

                    // Resolve coupon — returns ex-GST discount amount
                    [$couponId, $couponDiscountExGst, $coupon] = $this->resolveItemCoupon(
                        $itemData['coupon_code'] ?? null,
                        $itemData['price'],
                        $itemData['quantity'],
                        $gstRate,
                        $item->coupon_id
                    );

                    // Compute per-item figures
                    $computed = $this->computeItemTotals(
                        price:               $itemData['price'],
                        quantity:            $itemData['quantity'],
                        gstRate:             $gstRate,
                        couponDiscountExGst: $couponDiscountExGst,
                        manualType:          $itemData['manual_discount_type']  ?? null,
                        manualValue:         (float) ($itemData['manual_discount_value'] ?? 0),
                    );

                    $payload = [
                        'quantity'               => $itemData['quantity'],
                        'price'                  => $itemData['price'],
                        'coupon_id'              => $couponId,
                        'coupon_discount'        => $computed['coupon_discount_ex'],   // stored ex-GST
                        'manual_discount_type'   => $itemData['manual_discount_type']  ?? null,
                        'manual_discount_value'  => (float) ($itemData['manual_discount_value'] ?? 0),
                        'manual_discount_amount' => $computed['manual_discount_ex'],   // stored ex-GST
                        'manual_discount_note'   => $itemData['manual_discount_note']  ?? null,
                        'discount_amount'        => $computed['total_discount_ex'],
                        'taxable_price'          => $computed['taxable_price'],
                        'tax_rate'               => $computed['gst_rate'],
                        'tax_amount'             => $computed['tax_amount'],
                        'line_total'             => $computed['line_total'],
                    ];

                    $formattedDiff = compareValues($item, $payload);
                    $item->update($payload);

                    $this->syncItemCouponPivot($item, $coupon, $computed['coupon_discount_ex'], $itemData['coupon_code'] ?? null);

                    if (! empty($formattedDiff)) {
                        accessLog('update', 'OrderItem', $formattedDiff, auth()->id(), 'OrderItem', $item->id, $order->id);
                    }
                }
            }

            // ── 2. Add new items ───────────────────────────────────────────────
            if (! empty($validatedData['new_items'])) {
                foreach ($validatedData['new_items'] as $newItemData) {
                    // Variant guard
                    $hasEnabledAttributes = ProductAttribute::where('product_id', $newItemData['product_id'])
                        ->where('status', 'enable')
                        ->exists();

                    if ($hasEnabledAttributes && empty($newItemData['product_attribute_id'])) {
                        throw ValidationException::withMessages([
                            'new_items' => 'This product has sizes/variants — please select one before adding it.',
                        ]);
                    }

                    if (! empty($newItemData['product_attribute_id'])) {
                        $belongs = ProductAttribute::where('id', $newItemData['product_attribute_id'])
                            ->where('product_id', $newItemData['product_id'])
                            ->exists();

                        if (! $belongs) {
                            throw ValidationException::withMessages([
                                'new_items' => 'The selected size does not belong to the chosen product.',
                            ]);
                        }
                    }

                    $product = Product::findOrFail($newItemData['product_id']);
                    $gstRate = (float) ($product->gst ?? 0);

                    // Resolve coupon — returns ex-GST discount amount
                    [$couponId, $couponDiscountExGst, $coupon] = $this->resolveItemCoupon(
                        $newItemData['coupon_code'] ?? null,
                        $newItemData['price'],
                        $newItemData['quantity'],
                        $gstRate,
                        null
                    );

                    $computed = $this->computeItemTotals(
                        price:               $newItemData['price'],
                        quantity:            $newItemData['quantity'],
                        gstRate:             $gstRate,
                        couponDiscountExGst: $couponDiscountExGst,
                        manualType:          $newItemData['manual_discount_type']  ?? null,
                        manualValue:         (float) ($newItemData['manual_discount_value'] ?? 0),
                    );

                    $newItemPayload = [
                        'order_id'               => $order->id,
                        'product_id'             => $newItemData['product_id'],
                        'product_attribute_id'   => $newItemData['product_attribute_id'] ?? null,
                        'quantity'               => $newItemData['quantity'],
                        'price'                  => $newItemData['price'],
                        'coupon_id'              => $couponId,
                        'coupon_discount'        => $computed['coupon_discount_ex'],   // stored ex-GST
                        'manual_discount_type'   => $newItemData['manual_discount_type']  ?? null,
                        'manual_discount_value'  => (float) ($newItemData['manual_discount_value'] ?? 0),
                        'manual_discount_amount' => $computed['manual_discount_ex'],   // stored ex-GST
                        'manual_discount_note'   => $newItemData['manual_discount_note']  ?? null,
                        'discount_amount'        => $computed['total_discount_ex'],
                        'taxable_price'          => $computed['taxable_price'],
                        'tax_rate'               => $computed['gst_rate'],
                        'tax_amount'             => $computed['tax_amount'],
                        'line_total'             => $computed['line_total'],
                    ];

                    $orderItem = OrderItem::create($newItemPayload);

                    $this->syncItemCouponPivot($orderItem, $coupon, $computed['coupon_discount_ex'], $newItemData['coupon_code'] ?? null);

                    accessLog('create', 'OrderItem', $orderItem, auth()->id(), 'OrderItem', $orderItem->id, $order->id);
                }
            }

            // ── 3. Recompute order-level aggregates from saved item rows ───────
            //
            //  subtotalInclGst  = Σ price × qty                (before any discount, incl-GST)
            //  subtotalExGst    = Σ (price / 1+gst%) × qty     (before any discount, ex-GST)
            //  gstInSubtotal    = subtotalInclGst − subtotalExGst
            //  totalCouponEx    = Σ coupon_discount             (ex-GST)
            //  totalManualEx    = Σ manual_discount_amount      (ex-GST)
            //  taxableValue     = Σ taxable_price × qty         (ex-GST after discounts)
            //  gstAmount        = Σ tax_amount
            //  itemsTotal       = Σ line_total                  (taxableValue + gstAmount)
            //  grandTotal       = itemsTotal + shippingCharge   (shipping entered incl-GST)
            // ──────────────────────────────────────────────────────────────────

            $order->refresh();

            $subtotalInclGst = 0.0;
            $subtotalExGst   = 0.0;
            $totalCouponEx   = 0.0;
            $totalManualEx   = 0.0;
            $taxableValue    = 0.0;
            $gstAmount       = 0.0;
            $itemsTotal      = 0.0;

            foreach ($order->items as $item) {
                $priceExGst = $item->tax_rate > 0
                    ? round($item->price / (1 + $item->tax_rate / 100), 4)
                    : (float) $item->price;

                $subtotalInclGst += round((float) $item->price * $item->quantity, 4);
                $subtotalExGst   += round($priceExGst * $item->quantity, 4);
                $totalCouponEx   += (float) $item->coupon_discount;        // already ex-GST
                $totalManualEx   += (float) $item->manual_discount_amount; // already ex-GST
                $taxableValue    += round((float) $item->taxable_price * $item->quantity, 4);
                $gstAmount       += (float) $item->tax_amount;
                $itemsTotal      += (float) $item->line_total;
            }

            $subtotalInclGst = round($subtotalInclGst, 2);
            $subtotalExGst   = round($subtotalExGst,   2);
            $gstInSubtotal   = round($subtotalInclGst - $subtotalExGst, 2);
            $totalCouponEx   = round($totalCouponEx,   2);
            $totalManualEx   = round($totalManualEx,   2);
            $taxableValue    = round($taxableValue,    2);
            $gstAmount       = round($gstAmount,       2);
            $itemsTotal      = round($itemsTotal,      2);

            // Shipping — assumed entered as incl-GST (what customer pays)
            // GST portion = charge × 18/118
            $shippingCharge = (float) ($validatedData['shipping_charge'] ?? 0);
            $shippingGst = $shippingCharge > 0 ? round($shippingCharge * 18 / 100, 2) : 0.0;

            // Grand total = all item line totals (already incl item-GST) + shipping (incl-GST)
            $grandTotal = round($itemsTotal + $shippingCharge + $shippingGst, 2);

            // ── 4. Build diff & persist order ──────────────────────────────────
            $orderPayload = [
                'status'              => $validatedData['status'],
                'payment_method'      => $validatedData['payment_method'],
                'name'                => $validatedData['name'],
                'mobile'              => $validatedData['mobile'],
                'state'               => $validatedData['state']    ?? $order->state,
                'city'                => $validatedData['city']     ?? $order->city,
                'zipcode'             => $validatedData['zipcode']  ?? $order->zipcode,
                'locality'            => $validatedData['locality'] ?? $order->locality,
                'address'             => $validatedData['address'],
                // ── Item subtotals (before discount) ──
                'subtotal_incl_gst'   => $subtotalInclGst,  // ₹18,000  (incl-GST, before discount)
                'subtotal_ex_gst'     => $subtotalExGst,    // ₹16,363.64 (ex-GST, before discount)
                // 'gst_in_subtotal'     => $gstInSubtotal,    // ₹1,636.36
                // ── Discounts (ex-GST) ──
                'coupon_discount'     => $totalCouponEx,    // ₹4,140
                'manual_discount'     => $totalManualEx,    // ₹1,260
                // ── After discount ──
                'taxable_value'       => $taxableValue,     // ₹10,963.64
                'tax_amount'          => $gstAmount,        // ₹1,096.36
                // ── Shipping ──
                'shipping_charge'     => $shippingCharge,   // ₹1,000 (incl-GST)
                'shipping_charge_gst' => $shippingGst,      // ₹180
                // ── Grand total ──
                'total'               => $grandTotal,       // ₹13,060 (items + shipping)
            ];

            $formattedDescription = compareValues($order, $orderPayload);
            $order->update($orderPayload);

            if (! empty($formattedDescription)) {
                accessLog('update', 'Order', $formattedDescription, auth()->id(), 'Order', $order->id);
            }

            DB::commit();

            try {
                Mail::to([$order->email, 'cypwebtechs@gmail.com'])
                    ->send(new OrderInvoiceMail($order->load('items.product')));
            } catch (\Throwable $e) {
                \Log::error('Order invoice mail failed: ' . $e->getMessage());
            }

            return redirect()->route('admin.orders.show', $order)
                             ->with('success', 'Order #' . $order->uuid . ' updated successfully!');

        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()
                             ->withErrors($e->errors())
                             ->withInput()
                             ->with('error', 'Please correct the errors in the form.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Failed to update order. ' . $e->getMessage());
        }
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  PRIVATE HELPERS
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Resolve a coupon code for a single order item.
     *
     * Coupon discount is calculated on the ex-GST line total (GST §15 compliant).
     *
     * Returns [couponId, couponDiscountExGst, $coupon|null].
     */
    private function resolveItemCoupon(
        ?string $code,
        float   $priceInclGst,
        int     $quantity,
        float   $gstRate,
        ?int    $existingCouponId
    ): array {
        if (empty($code)) {
            // Coupon removed — decrement usage if one was previously applied
            if ($existingCouponId) {
                Coupon::where('id', $existingCouponId)->decrement('used_count');
            }
            return [null, 0.0, null];
        }

        $coupon = Coupon::where('code', strtoupper($code))->first();

        if (! $coupon || ! $coupon->isValid()) {
            throw ValidationException::withMessages([
                'items' => "Coupon '{$code}' is invalid or expired.",
            ]);
        }

        // ✅ Calculate coupon discount on ex-GST line total directly
        $priceExGst         = $gstRate > 0 ? $priceInclGst / (1 + $gstRate / 100) : $priceInclGst;
        $lineTotalExGst     = round($priceExGst * $quantity, 4);
        $couponDiscountExGst = $coupon->calculateDiscount($lineTotalExGst);

        if ($couponDiscountExGst <= 0) {
            throw ValidationException::withMessages([
                'items' => "Coupon '{$code}' is not applicable for this item's value.",
            ]);
        }

        return [$coupon->id, $couponDiscountExGst, $coupon];
    }

    /**
     * Sync the coupon_order_item pivot and the coupon used_count.
     */
    private function syncItemCouponPivot(
        OrderItem $item,
        ?Coupon   $coupon,
        float     $discountExGst,
        ?string   $submittedCode
    ): void {
        $previousCouponId = $item->getOriginal('coupon_id') ?? $item->coupon_id;

        if ($coupon === null) {
            // used_count already decremented in resolveItemCoupon
            DB::table('coupon_order_item')->where('order_item_id', $item->id)->delete();
            return;
        }

        $isNewCoupon = $previousCouponId !== $coupon->id;

        if ($isNewCoupon) {
            if ($previousCouponId) {
                Coupon::where('id', $previousCouponId)->decrement('used_count');
                DB::table('coupon_order_item')
                    ->where('coupon_id', $previousCouponId)
                    ->where('order_item_id', $item->id)
                    ->delete();
            }
            $coupon->increment('used_count');
        }

        DB::table('coupon_order_item')->updateOrInsert(
            ['coupon_id' => $coupon->id, 'order_item_id' => $item->id],
            [
                'discount_applied' => $discountExGst,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]
        );
    }

    /**
     * Remove a coupon from an item being deleted.
     */
    private function detachItemCoupon(OrderItem $item): void
    {
        Coupon::where('id', $item->coupon_id)->decrement('used_count');
        DB::table('coupon_order_item')
            ->where('coupon_id', $item->coupon_id)
            ->where('order_item_id', $item->id)
            ->delete();
    }

    /**
     * Compute all monetary figures for a single order-item line.
     *
     * Flow:
     *   priceExGst       = price / (1 + gstRate/100)
     *   lineTotalExGst   = priceExGst × qty
     *
     *   couponDiscountEx → passed in (already ex-GST from resolveItemCoupon)
     *   manualDiscountEx → flat or percent applied directly on lineTotalExGst
     *
     *   taxableLineTotal = lineTotalExGst − couponDiscountEx − manualDiscountEx
     *   taxablePrice/unit= taxableLineTotal / qty              (4dp, the GST base per unit)
     *   tax_amount       = taxableLineTotal × gstRate/100
     *   line_total       = taxableLineTotal + tax_amount
     *
     * @return array{
     *   gst_rate: float,
     *   coupon_discount_ex: float,
     *   manual_discount_ex: float,
     *   total_discount_ex: float,
     *   taxable_price: float,
     *   tax_amount: float,
     *   line_total: float,
     * }
     */
    private function computeItemTotals(
        float   $price,
        int     $quantity,
        float   $gstRate,
        float   $couponDiscountExGst,
        ?string $manualType,
        float   $manualValue
    ): array {
        $priceExGst      = $gstRate > 0 ? round($price / (1 + $gstRate / 100), 6) : $price;
        $lineTotalExGst  = round($priceExGst * $quantity, 4);

        // ✅ Manual discount applied directly on ex-GST line total
        $manualDiscountExGst = 0.0;
        if ($manualType === 'flat') {
            $manualDiscountExGst = min((float) $manualValue, $lineTotalExGst);
        } elseif ($manualType === 'percent' && $manualValue > 0) {
            $manualDiscountExGst = round($lineTotalExGst * $manualValue / 100, 4);
        }

        // ✅ taxable = subtotalExGST − couponDiscountEx − manualDiscountEx
        $totalDiscountEx     = round($couponDiscountExGst + $manualDiscountExGst, 4);
        $taxableLineTotal    = round(max(0.0, $lineTotalExGst - $totalDiscountEx), 4);
        $taxablePricePerUnit = $quantity > 0 ? round($taxableLineTotal / $quantity, 4) : 0.0;

        $taxAmount = round($taxableLineTotal * ($gstRate / 100), 2);
        $lineTotal = round($taxableLineTotal + $taxAmount, 2);

        return [
            'gst_rate'          => $gstRate,
            'coupon_discount_ex' => round($couponDiscountExGst, 2),
            'manual_discount_ex' => round($manualDiscountExGst, 2),
            'total_discount_ex'  => round($totalDiscountEx, 2),
            'taxable_price'      => $taxablePricePerUnit,   // 4dp intentional
            'tax_amount'         => $taxAmount,
            'line_total'         => $lineTotal,
        ];
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  AJAX — product search (for "Add New Item" modal)
    // ══════════════════════════════════════════════════════════════════════════

    public function productSearch(Request $request)
    {
        $q = $request->get('q', '');

        $products = Product::query()
            ->where('status', 'enable')
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('code', 'like', "%{$q}%");
            })
            ->select('id', 'name', 'code', 'selling', 'mrp', 'stock', 'gst')
            ->limit(15)
            ->get()
            ->map(function ($product) {
                $product->attributes = ProductAttribute::where('product_id', $product->id)
                    ->where('status', 'enable')
                    ->select('id', 'size', 'mrp', 'selling_price', 'stock')
                    ->orderBy('size')
                    ->get();
                return $product;
            });

        return response()->json($products);
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  PDF
    // ══════════════════════════════════════════════════════════════════════════

    public function pdf(Order $order): \Illuminate\Http\Response
    {
        $order->load(['items.product', 'items.productAttribute', 'items.coupon']);

        $pdf = Pdf::loadView('pdfs.order', ['order' => $order])
            ->setPaper('a4', 'portrait');

        $filename = 'invoice-' . str_pad($order->id, 5, '0', STR_PAD_LEFT) . '.pdf';

        return $pdf->download($filename);
    }
}