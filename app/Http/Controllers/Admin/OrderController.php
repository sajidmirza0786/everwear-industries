<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB; // Import DB facade
use Illuminate\Validation\ValidationException; // Import ValidationException

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     */
    public function index(Request $request)
    {
        $query = Order::query();

        // Apply search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('uuid', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Apply payment method filter
        if ($paymentMethod = $request->input('payment_method')) {
            $query->where('payment_method', $paymentMethod);
        }

        // Order by latest first
        $orders = $query->orderBy('created_at', 'desc')->paginate(30);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        // Load order items and their associated products, and the user if available
        $order->load('items.product', 'user');
        $order_logs = get_logs('Order', $order->id);
        return view('admin.orders.show', compact('order', 'order_logs'));
    }

    /**
     * Show the form for editing the specified order.
     */
    public function edit(Order $order)
    {
        // Load order items and their associated products for display in the form
        $order->load('items.product');
        return view('admin.orders.edit', compact('order'));
    }

    /**
     * Update the specified order in storage.
     */
    public function update(Request $request, Order $order)
    {
        // 1. Validate the incoming request data
        $validatedData = $request->validate([
            'status' => ['required', 'string', 'in:completed,cancelled,in-transit'],
            'payment_method' => ['required', 'string', 'in:prepaid,cod'],
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:20'],
            'state' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'zipcode' => ['nullable', 'string', 'max:25'],
            'locality' => ['nullable', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
        ]);

        // Use a database transaction to ensure atomicity
        DB::beginTransaction();
        try {

            $formattedDescription = compareValues($order, $validatedData);

            // 2. Update the order with validated data
            $order->update($validatedData);

            accessLog('update', 'Order', $formattedDescription, auth()->id(), 'Order', $order->id);

            DB::commit(); // Commit the transaction if all updates are successful

            // Redirect back to the order details page with a success message
            return redirect()->route('admin.orders.show', $order)
                             ->with('success', 'Order #' . $order->uuid . ' updated successfully!');

        } catch (ValidationException $e) {
            DB::rollBack(); 
            return redirect()->back()
                             ->withErrors($e->errors())
                             ->withInput()
                             ->with('error', 'Please correct the errors in the form.');
        } catch (\Exception $e) {
            // Catch any other general exceptions
            DB::rollBack(); 

            // Redirect back with an error message
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Failed to update order. Please try again. ' . $e->getMessage()); // Display general error
        }
    }
}
