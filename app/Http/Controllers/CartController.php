<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // ── Shared helper: resolve product, attribute, stock, price from request ──
    private function resolveItem(Request $request): array|string
    {
        $atr     = null;
        $product = null;

        if (!empty($request->product_attribute_id)) {
            $atr     = ProductAttribute::with('product')->findOrFail($request->product_attribute_id);
            $product = $atr->product;

            if ($product->id !== (int) $request->product_id) {
                return 'Invalid product variant selected.';
            }
            if ($atr->status !== 'enable') {
                return 'This variant is currently unavailable.';
            }

            $availableStock = $atr->stock;
            $sellingPrice   = $atr->selling_price;
        } else {
            $product = Product::findOrFail($request->product_id);

            if ($product->attributes()->where('status', 'enable')->exists()) {
                return 'Please select a size/variant before adding to cart.';
            }

            $availableStock = $product->stock;
            $sellingPrice   = $product->selling;
        }

        return compact('product', 'atr', 'availableStock', 'sellingPrice');
    }

    // ── Shared helper: calculate shipping ──
    private function shipping(Product $product, $sellingPrice, int $qty): float
    {
        $countryId = Auth::check() ? Auth::user()->country_id ?? null : null;
        $stateId   = Auth::check() ? Auth::user()->state_id   ?? null : null;
        return calculateShippingCharge(
            $countryId, $stateId,
            $product->gram_weight * $qty,
            $sellingPrice * $qty
        );
    }

    // ── Shared session cart key ──
    private function cartKey(int $productId, ?int $atrId): string
    {
        return $productId . ($atrId ? '_atr_' . $atrId : '');
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id'           => 'required|exists:products,id',
            'quantity'             => 'required|integer|min:1',
            'product_attribute_id' => 'nullable|exists:product_attributes,id',
        ]);

        $resolved = $this->resolveItem($request);

        if (is_string($resolved)) {
            return back()->with('error', $resolved);
        }

        ['product' => $product, 'atr' => $atr, 'availableStock' => $availableStock, 'sellingPrice' => $sellingPrice]
            = $resolved;

        if ($availableStock < $request->quantity) {
            return back()->with('error', 'Insufficient stock available for ' . $product->name);
        }

        if (Auth::check()) {
            $cartItem = Cart::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->where('product_attribute_id', $atr?->id)
                ->first();

            if ($cartItem) {
                $newQty = $cartItem->quantity + $request->quantity;
                if ($newQty > $availableStock) {
                    return back()->with('error', 'Cannot add more than available stock for ' . $product->name);
                }
                $cartItem->quantity        = $newQty;
                $cartItem->price           = $sellingPrice;
                $cartItem->shipping_charge = $this->shipping($product, $sellingPrice, $newQty);
                $cartItem->save();
            } else {
                Cart::create([
                    'user_id'              => Auth::id(),
                    'product_id'           => $product->id,
                    'product_attribute_id' => $atr?->id,
                    'quantity'             => $request->quantity,
                    'price'                => $sellingPrice,
                    'shipping_charge'      => $this->shipping($product, $sellingPrice, $request->quantity),
                ]);
            }
        } else {
            $cart    = session()->get('cart', []);
            $cartKey = $this->cartKey($product->id, $atr?->id);

            if (isset($cart[$cartKey])) {
                $newQty = $cart[$cartKey]['quantity'] + $request->quantity;
                if ($newQty > $availableStock) {
                    return back()->with('error', 'Cannot add more than available stock for ' . $product->name);
                }
                $cart[$cartKey]['quantity']        = $newQty;
                $cart[$cartKey]['price']           = $sellingPrice;
                $cart[$cartKey]['shipping_charge'] = $this->shipping($product, $sellingPrice, $newQty);
            } else {
                $variantLabel   = $atr ? ' – ' . $atr->size : '';
                $cart[$cartKey] = [
                    'product_id'           => $product->id,
                    'product_attribute_id' => $atr?->id,
                    'name'                 => $product->name . $variantLabel,
                    'quantity'             => $request->quantity,
                    'price'                => $sellingPrice,
                    'image'                => $product->image,
                    'shipping_charge'      => $this->shipping($product, $sellingPrice, $request->quantity),
                ];
            }

            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    public function view()
    {
        $cartItems     = [];
        $totalExGst    = 0;  // sum of ex-GST subtotals
        $totalGst      = 0;  // sum of GST amounts
        $total         = 0;  // sum of selling-price subtotals (ex-GST + GST)
        $totalShipping = 0;

        if (Auth::check()) {
            $cartItems = Cart::where('user_id', Auth::id())
                ->with(['product', 'productAttribute'])
                ->get()
                ->map(function ($item) {
                    $item->variant_label = $item->productAttribute
                        ? $item->productAttribute->size
                        : null;
                    return $item;
                });

            foreach ($cartItems as $item) {
                $product      = $item->product;
                $qty          = $item->quantity;
                $sellingPrice = $item->price;               // GST-inclusive unit price

                // Derive ex-GST price from the actual cart price + product GST rate.
                // Formula: ex_gst = price / (1 + gst_rate / 100)
                // This is correct even when $item->price differs from product->selling
                // (e.g. variant pricing, promotional prices, etc.)
                $gstRate      = (float) ($product->gst ?? 0);
                $exGstUnit    = $gstRate > 0
                    ? round($sellingPrice / (1 + $gstRate / 100), 2)
                    : $sellingPrice;
                $gstUnit      = round($sellingPrice - $exGstUnit, 2);

                $item->subtotal_ex_gst = round($exGstUnit * $qty, 2);
                $item->gst_amount      = round($gstUnit    * $qty, 2);
                $item->gst_rate        = $gstRate;

                $totalExGst    += $item->subtotal_ex_gst;
                $totalGst      += $item->gst_amount;
                $total         += $sellingPrice * $qty;
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
                $exGstUnit    = $gstRate > 0
                    ? round($sellingPrice / (1 + $gstRate / 100), 2)
                    : $sellingPrice;
                $gstUnit      = round($sellingPrice - $exGstUnit, 2);

                $obj->subtotal_ex_gst = round($exGstUnit * $qty, 2);
                $obj->gst_amount      = round($gstUnit    * $qty, 2);
                $obj->gst_rate        = $gstRate;

                $cartItems[]    = $obj;
                $totalExGst    += $obj->subtotal_ex_gst;
                $totalGst      += $obj->gst_amount;
                $total         += $sellingPrice * $qty;
                $totalShipping += $item['shipping_charge'];
            }
        }

        return view('users.cart', compact(
            'cartItems', 'total', 'totalShipping', 'totalExGst', 'totalGst'
        ));
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id'           => 'required|exists:products,id',
            'quantity'             => 'required|integer|min:1',
            'product_attribute_id' => 'nullable|exists:product_attributes,id',
        ]);

        $atr     = null;
        $product = null;

        if (!empty($request->product_attribute_id)) {
            $atr            = ProductAttribute::with('product')->findOrFail($request->product_attribute_id);
            $product        = $atr->product;
            $availableStock = $atr->stock;
            $sellingPrice   = $atr->selling_price;
        } else {
            $product        = Product::findOrFail($request->product_id);
            $availableStock = $product->stock;
            $sellingPrice   = $product->selling;
        }

        if ($availableStock < $request->quantity) {
            return back()->with('error', 'Insufficient stock available for ' . $product->name);
        }

        $shippingCharge = $this->shipping($product, $sellingPrice, $request->quantity);

        if (Auth::check()) {
            $cartItem = Cart::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->where('product_attribute_id', $atr?->id)
                ->first();

            if ($cartItem) {
                $cartItem->quantity        = $request->quantity;
                $cartItem->price           = $sellingPrice;
                $cartItem->shipping_charge = $shippingCharge;
                $cartItem->save();
            }
        } else {
            $cart    = session()->get('cart', []);
            $cartKey = $this->cartKey($product->id, $atr?->id);

            if (isset($cart[$cartKey])) {
                $cart[$cartKey]['quantity']        = $request->quantity;
                $cart[$cartKey]['price']           = $sellingPrice;
                $cart[$cartKey]['shipping_charge'] = $shippingCharge;
                session()->put('cart', $cart);
            }
        }

        return back()->with('success', 'Cart updated successfully!');
    }

    public function remove(Request $request)
    {
        $request->validate([
            'product_id'           => 'required|exists:products,id',
            'product_attribute_id' => 'nullable|exists:product_attributes,id',
        ]);

        if (Auth::check()) {
            Cart::where('user_id', Auth::id())
                ->where('product_id', $request->product_id)
                ->where('product_attribute_id', $request->product_attribute_id ?? null)
                ->delete();
        } else {
            $cart    = session()->get('cart', []);
            $cartKey = $this->cartKey(
                $request->product_id,
                $request->product_attribute_id ?? null
            );

            if (isset($cart[$cartKey])) {
                unset($cart[$cartKey]);
                session()->put('cart', $cart);
            }
        }

        return back()->with('success', 'Product removed from cart!');
    }

    public static function mergeCart()
    {
        if (!Auth::check() || !session()->has('cart')) return;

        $sessionCart = session()->get('cart', []);
        $messages    = [];
        $countryId   = Auth::user()->country_id ?? null;
        $stateId     = Auth::user()->state_id   ?? null;

        foreach ($sessionCart as $item) {
            $product = Product::find($item['product_id']);
            if (!$product) {
                $messages[] = "Product {$item['name']} is no longer available.";
                continue;
            }

            $atr            = null;
            $availableStock = $product->stock;
            $sellingPrice   = $product->selling;

            if (!empty($item['product_attribute_id'])) {
                $atr = ProductAttribute::find($item['product_attribute_id']);
                if (!$atr || $atr->status !== 'enable') {
                    $messages[] = "A variant of {$product->name} is no longer available.";
                    continue;
                }
                $availableStock = $atr->stock;
                $sellingPrice   = $atr->selling_price;
            }

            if ($availableStock < $item['quantity']) {
                $messages[] = "Insufficient stock for {$item['name']}. Only {$availableStock} available.";
                continue;
            }

            $shippingCharge = calculateShippingCharge(
                $countryId, $stateId,
                $product->gram_weight * $item['quantity'],
                $sellingPrice * $item['quantity']
            );

            $cartItem = Cart::where('user_id', Auth::id())
                ->where('product_id', $item['product_id'])
                ->where('product_attribute_id', $atr?->id)
                ->first();

            if ($cartItem) {
                $newQty = $cartItem->quantity + $item['quantity'];
                if ($newQty > $availableStock) {
                    $messages[] = "Cannot merge {$item['name']}. Only {$availableStock} available.";
                    continue;
                }
                $cartItem->quantity        = $newQty;
                $cartItem->price           = $sellingPrice;
                $cartItem->shipping_charge = calculateShippingCharge(
                    $countryId, $stateId,
                    $product->gram_weight * $newQty,
                    $sellingPrice * $newQty
                );
                $cartItem->save();
            } else {
                Cart::create([
                    'user_id'              => Auth::id(),
                    'product_id'           => $item['product_id'],
                    'product_attribute_id' => $atr?->id,
                    'quantity'             => $item['quantity'],
                    'price'                => $sellingPrice,
                    'shipping_charge'      => $shippingCharge,
                ]);
            }
        }

        session()->forget('cart');

        if (!empty($messages)) {
            session()->flash('warnings', $messages);
        }

        session()->flash('success', 'Your cart has been merged successfully!');
    }
}