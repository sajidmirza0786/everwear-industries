<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
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

        if (empty($cartItems)) {
            return redirect()->route('cart.view')->with('error', 'Your cart is empty.');
        }

        return view('users.checkout', compact('cartItems', 'total', 'totalShipping'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => Auth::check() ? 'nullable|email|max:255' : 'required|email|max:255',
            'mobile' => 'required|string|max:15',
            'state' => 'required|exists:states,name',
            'zipcode' => ['required', 'regex:/^[1-9][0-9]{5}$/'],
            'city' => ['required', 'string', 'regex:/^[a-zA-Z\s]+$/', 'max:50'],
            'locality' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'payment_method' => ['required','in:cod,prepaid'],
        ]);

        $cartItems = [];
        $total = 0;
        $totalShipping = 0;

        try{
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

            if (empty($cartItems)) {
                return redirect()->route('cart.view')->with('error', 'Your cart is empty.');
            }
            
            DB::beginTransaction();

            // Recalculate shipping charges based on provided address
            foreach ($cartItems as $item) {
                $product = Product::find($item->product_id);
                if (!$product || $product->stock < $item->quantity) {
                    return back()->with('error', 'Insufficient stock for ' . ($item->product->name ?? $item->name));
                }
                $item->shipping_charge = calculateShippingCharge(
                    $request->country_id,
                    $request->state_id,
                    $product->gram_weight * $item->quantity,
                    $product->selling * $item->quantity
                );
                if (Auth::check()) {
                    $cartItem = Cart::where('user_id', Auth::id())
                        ->where('product_id', $item->product_id)
                        ->first();
                    if ($cartItem) {
                        $cartItem->shipping_charge = $item->shipping_charge;
                        $cartItem->save();
                    }
                } else {
                    $cart = session()->get('cart', []);
                    if (isset($cart[$item->product_id])) {
                        $cart[$item->product_id]['shipping_charge'] = $item->shipping_charge;
                        session()->put('cart', $cart);
                    }
                }
                $totalShipping += $item->shipping_charge;
            }

            // Create order
            $order = Order::create([
                'uuid' => str()->uuid()->toString(),
                'user_id' => Auth::id(),
                'name' => $request->name,
                'email' => $request->email ?? auth()->user()->email,
                'mobile' => $request->mobile,
                'address' => $request->address,
                'locality' => $request->locality,
                'city' => $request->city,
                'state' => $request->state,
                'zipcode' => $request->zipcode,
                'total' => $total,
                'shipping_charge' => $totalShipping,
                'status' => 'pending',
            ]);

            // Create order items
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ]);

                // Update product stock
                $product = Product::find($item->product_id);
                $product->stock -= $item->quantity;
                $product->save();
            }

            // Clear cart
            if (Auth::check()) {
                Cart::where('user_id', Auth::id())->delete();
            } else {
                session()->forget('cart');
            }

            DB::commit();

            return redirect()->route('order.confirmation', $order->uuid)->with('success', 'Order placed successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Checkout processing failed: ' . $e->getMessage());
        }
    }

    public function confirmation($orderId)
    {
        $order = Order::with('items.product')->findOrFail($orderId);
        return view('users.order-confirmation', compact('order'));
    }
}