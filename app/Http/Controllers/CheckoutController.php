<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Mail\OrderInvoiceMail;
use Illuminate\Support\Facades\Mail;

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

        if (!$cartItems && $cartItems->isEmpty()) {
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
            'payment_method' => ['required', 'in:cod,prepaid'],
        ]);

        try {
            $cartItems = Auth::check()
                ? Cart::where('user_id', Auth::id())->with('product')->get()
                : collect(session()->get('cart', []))->map(fn($item) => (object) $item);

            if ($cartItems->isEmpty()) {
                return redirect()->route('cart.view')->with('error', 'Your cart is empty.');
            }

            $total = $cartItems->sum(fn($item) => $item->quantity * $item->price);
            $totalShipping = $cartItems->sum('shipping_charge');

            DB::beginTransaction();

            $email = $request->email ?? auth()->user()->email;

            $userData = [
                'uuid' => str()->uuid()->toString(),
                'name' => $request->name,
                'email' => $email,
                'mobile' => $request->mobile,
                'address' => $request->address,
                'locality' => $request->locality,
                'city' => $request->city,
                'state' => $request->state,
                'zipcode' => $request->zipcode,
            ];

            if (!auth()->check()) {
                $datau = [
                    'password' => bcrypt($request->mobile),
                    'local_password' => $request->mobile,
                    'ip_address' => $request->ip(),
                ];
                $user = User::updateOrCreate(['email' => $email], array_merge($userData, $datau));
                Auth::login($user);
            } else {
                $user = User::updateOrCreate(['email' => $email], $userData);
            }

            if (!$user) {
                return back()->with('error', 'Something went wrong!');
            }

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
                    Cart::where('user_id', Auth::id())
                        ->where('product_id', $item->product_id)
                        ->update(['shipping_charge' => $item->shipping_charge]);
                } else {
                    $cart = session()->get('cart', []);
                    if (isset($cart[$item->product_id])) {
                        $cart[$item->product_id]['shipping_charge'] = $item->shipping_charge;
                        session()->put('cart', $cart);
                    }
                }

                $totalShipping += $item->shipping_charge;
            }

            $order = Order::create([
                'uuid' => str()->uuid()->toString(),
                'user_id' => Auth::id(),
                'name' => $request->name,
                'email' => $email,
                'mobile' => $request->mobile,
                'address' => $request->address,
                'locality' => $request->locality,
                'city' => $request->city,
                'state' => $request->state,
                'zipcode' => $request->zipcode,
                'total' => $total,
                'shipping_charge' => $totalShipping,
                'status' => 'pending',
                'ip_address' => $request->ip(),
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ]);

                Product::where('id', $item->product_id)->decrement('stock', $item->quantity);
            }

            Auth::check()
                ? Cart::where('user_id', Auth::id())->delete()
                : session()->forget('cart');

            DB::commit();
            return redirect()->route('order.confirmation', $order)->with('success', 'Order placed successfully!');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Checkout processing failed: ' . $e->getMessage());
        }
    }

    public function confirmation(Order $order)
    {
        // ✅ Restrict access to only the user who placed the order
        if (Auth::check() && $order->user_id !== Auth::id()) {
            return abort(403, 'Unauthorized access to order.');
        }
        Mail::to($order->user->email)->send(new OrderInvoiceMail($order->load('items.product')));

        return view('users.order-confirmation', compact('order'));
    }
}