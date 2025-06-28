<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        
        if ($product->stock < $request->quantity) {
            return back()->with('error', 'Insufficient stock available for ' . $product->name);
        }

        // Calculate shipping charge
        $totalWeight = $product->gram_weight * $request->quantity;
        $orderAmount = $product->selling * $request->quantity;
        $countryId = Auth::check() ? Auth::user()->country_id ?? null : null; // Default to India (ID 1)
        $stateId = Auth::check() ? Auth::user()->state_id ?? null : null;
        $shippingCharge = calculateShippingCharge($countryId, $stateId, $totalWeight, $orderAmount);

        if (Auth::check()) {
            $cartItem = Cart::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->first();

            if ($cartItem) {
                $cartItem->quantity += $request->quantity;
                $cartItem->shipping_charge = calculateShippingCharge(
                    $countryId,
                    $stateId,
                    $product->gram_weight * $cartItem->quantity,
                    $product->selling * $cartItem->quantity
                );
                if ($cartItem->quantity > $product->stock) {
                    return back()->with('error', 'Cannot add more than available stock for ' . $product->name);
                }
                $cartItem->save();
            } else {
                Cart::create([
                    'user_id' => Auth::id(),
                    'product_id' => $product->id,
                    'quantity' => $request->quantity,
                    'price' => $product->selling,
                    'shipping_charge' => $shippingCharge,
                ]);
            }
        } else {
            $cart = session()->get('cart', []);
            $productId = $product->id;

            if (isset($cart[$productId])) {
                $cart[$productId]['quantity'] += $request->quantity;
                $cart[$productId]['shipping_charge'] = calculateShippingCharge(
                    $countryId,
                    $stateId,
                    $product->gram_weight * $cart[$productId]['quantity'],
                    $product->selling * $cart[$productId]['quantity']
                );
                if ($cart[$productId]['quantity'] > $product->stock) {
                    return back()->with('error', 'Cannot add more than available stock for ' . $product->name);
                }
            } else {
                $cart[$productId] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'quantity' => $request->quantity,
                    'price' => $product->selling,
                    'image' => $product->image,
                    'shipping_charge' => $shippingCharge,
                ];
            }

            session()->put('cart', $cart);
        }

        return redirect()->route('cart.view')->with('success', 'Product added to cart successfully!');
    }

    public function view()
    {
        $cartItems = [];
        $total = 0;
        $totalShipping = 0;

        if (Auth::check()) {
            $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
            $total = $cartItems->sum(function ($item) {
                return $item->quantity * $item->price;
            });
            $totalShipping = $cartItems->sum('shipping_charge');
        } else {
            $cart = session()->get('cart', []);
            foreach ($cart as $item) {
                $cartItems[] = (object) $item;
                $total += $item['quantity'] * $item['price'];
                $totalShipping += $item['shipping_charge'];
            }
        }

        return view('users.cart', compact('cartItems', 'total', 'totalShipping'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock < $request->quantity) {
            return back()->with('error', 'Insufficient stock available for ' . $product->name);
        }

        $countryId = Auth::check() ? Auth::user()->country_id ?? 1 : 1;
        $stateId = Auth::check() ? Auth::user()->state_id ?? null : null;
        $shippingCharge = calculateShippingCharge(
            $countryId,
            $stateId,
            $product->gram_weight * $request->quantity,
            $product->selling * $request->quantity
        );

        if (Auth::check()) {
            $cartItem = Cart::where('user_id', Auth::id())
                ->where('product_id', $request->product_id)
                ->first();

            if ($cartItem) {
                $cartItem->quantity = $request->quantity;
                $cartItem->shipping_charge = $shippingCharge;
                $cartItem->save();
            }
        } else {
            $cart = session()->get('cart', []);
            if (isset($cart[$request->product_id])) {
                $cart[$request->product_id]['quantity'] = $request->quantity;
                $cart[$request->product_id]['shipping_charge'] = $shippingCharge;
                session()->put('cart', $cart);
            }
        }

        return back()->with('success', 'Cart updated successfully!');
    }

    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        if (Auth::check()) {
            Cart::where('user_id', Auth::id())
                ->where('product_id', $request->product_id)
                ->delete();
        } else {
            $cart = session()->get('cart', []);
            if (isset($cart[$request->product_id])) {
                unset($cart[$request->product_id]);
                session()->put('cart', $cart);
            }
        }

        return back()->with('success', 'Product removed from cart!');
    }

    public static function mergeCart()
    {
        if (Auth::check() && session()->has('cart')) {
            $sessionCart = session()->get('cart', []);
            $messages = [];
            $countryId = Auth::user()->country_id ?? 1; // Default to India
            $stateId = Auth::user()->state_id ?? null;

            foreach ($sessionCart as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) {
                    $messages[] = "Product {$item['name']} is no longer available and was not added to your cart.";
                    continue;
                }

                if ($product->stock < $item['quantity']) {
                    $messages[] = "Insufficient stock for {$product->name}. Only {$product->stock} available.";
                    continue;
                }

                $shippingCharge = calculateShippingCharge(
                    $countryId,
                    $stateId,
                    $product->gram_weight * $item['quantity'],
                    $product->selling * $item['quantity']
                );

                $cartItem = Cart::where('user_id', Auth::id())
                    ->where('product_id', $item['product_id'])
                    ->first();

                if ($cartItem) {
                    $cartItem->quantity += $item['quantity'];
                    $cartItem->shipping_charge = calculateShippingCharge(
                        $countryId,
                        $stateId,
                        $product->gram_weight * $cartItem->quantity,
                        $product->selling * $cartItem->quantity
                    );
                    if ($cartItem->quantity > $product->stock) {
                        $messages[] = "Cannot add {$product->name} to cart. Only {$product->stock} available.";
                        continue;
                    }
                    $cartItem->save();
                } else {
                    Cart::create([
                        'user_id' => Auth::id(),
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'shipping_charge' => $shippingCharge,
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
}