<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductAttribute;
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
        $cartItems     = [];
        $total         = 0;
        $totalShipping = 0;

        if (Auth::check()) {
            $cartItems = Cart::where('user_id', Auth::id())
                ->with(['product', 'productAttribute'])
                ->get();

            $total         = $cartItems->sum(fn($i) => $i->quantity * $i->price);
            $totalShipping = $cartItems->sum('shipping_charge');
        } else {
            $cart = session()->get('cart', []);
            foreach ($cart as $item) {
                $cartItems[]    = (object) $item;
                $total         += $item['quantity'] * $item['price'];
                $totalShipping += $item['shipping_charge'];
            }
        }

        if (empty($cartItems) || (is_object($cartItems) && method_exists($cartItems, 'isEmpty') && $cartItems->isEmpty())) {
            return redirect()->route('cart.view')->with('error', 'Your cart is empty.');
        }

        return view('users.checkout', compact('cartItems', 'total', 'totalShipping'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => Auth::check() ? 'nullable|email|max:255' : 'required|email|max:255',
            'mobile'         => 'required|string|max:15',
            'state'          => 'required|exists:states,name',
            'zipcode'        => ['required', 'regex:/^[1-9][0-9]{5}$/'],
            'city'           => ['required', 'string', 'regex:/^[a-zA-Z\s]+$/', 'max:50'],
            'locality'       => 'required|string|max:255',
            'address'        => 'required|string|max:255',
            'payment_method' => ['required', 'in:cod,prepaid'],
        ]);

        try {
            // ── Load cart items ──
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

            // ── Validate stock for every item before touching the DB ──
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

            // ── Upsert user ──
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

            // ── Recalculate shipping with final address ──
            $totalShipping = 0;
            foreach ($cartItems as $item) {
                $product = Product::find($item->product_id);
                if (!$product) continue;

                $atrId          = $item->product_attribute_id ?? null;
                $sellingPrice   = $atrId
                    ? (ProductAttribute::find($atrId)?->selling_price ?? $product->selling)
                    : $product->selling;

                $shippingCharge = calculateShippingCharge(
                    $user->country_id ?? null,
                    $user->state_id   ?? null,
                    $product->gram_weight * $item->quantity,
                    $sellingPrice * $item->quantity
                );

                // Update shipping in cart/session
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

            $total = $cartItems->sum(fn($i) => $i->quantity * $i->price);

            // ── Create order ──
            $order = Order::create([
                'uuid'            => str()->uuid()->toString(),
                'user_id'         => Auth::id(),
                'name'            => $request->name,
                'email'           => $email,
                'mobile'          => $request->mobile,
                'address'         => $request->address,
                'locality'        => $request->locality,
                'city'            => $request->city,
                'state'           => $request->state,
                'zipcode'         => $request->zipcode,
                'total'           => $total,
                'shipping_charge' => $totalShipping,
                'status'          => 'pending',
                'payment_method'  => $request->payment_method,
                'ip_address'      => $request->ip(),
            ]);

            // ── Create order items + decrement stock ──
            foreach ($cartItems as $item) {
                $atrId = $item->product_attribute_id ?? null;

                OrderItem::create([
                    'order_id'             => $order->id,
                    'product_id'           => $item->product_id,
                    'product_attribute_id' => $atrId,
                    'quantity'             => $item->quantity,
                    'price'                => $item->price,
                ]);

                // if ($atrId) {
                //     // Decrement variant stock
                //     ProductAttribute::where('id', $atrId)
                //         ->decrement('stock', $item->quantity);
                // } else {
                //     // Decrement base product stock
                //     Product::where('id', $item->product_id)
                //         ->decrement('stock', $item->quantity);
                // }
            }

            // ── Clear cart ──
            Auth::check()
                ? Cart::where('user_id', Auth::id())->delete()
                : session()->forget('cart');

            DB::commit();

            // ── Send invoice email ──
            try {
                Mail::to($order->email)->send(new OrderInvoiceMail($order->load('items.product')));
            } catch (\Throwable $e) {
                // Don't fail the order if mail fails
                \Log::error('Order invoice mail failed: ' . $e->getMessage());
            }

            return redirect()->route('order.confirmation', $order)
                ->with('success', 'Order placed successfully!');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Checkout processing failed. Please try again.'. $e->getMessage());
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