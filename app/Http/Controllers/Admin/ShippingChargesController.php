<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingCharge;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ShippingChargesController extends Controller
{
    public function index(Request $request)
    {
        $states = State::all();
        $shippingCharges = ShippingCharge::with(['country', 'state']);
        if($request->filled('state_id')) {
            $shippingCharges->where('state_id', $request->get('state_id'));
        }
        $shippingCharges = $shippingCharges->latest()->paginate(20);
        return view('admin.shipping_charges.index', compact('shippingCharges', 'states'));
    }

    public function create()
    {
        $states = State::all();
        return view('admin.shipping_charges.create', compact('states'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'state_id' => 'required|exists:states,id',
            'min_weight' => 'required|numeric|min:0',
            'max_weight' => 'nullable|numeric|gte:min_weight',
            'min_order_amount' => 'required|numeric|min:0',
            'max_order_amount' => 'nullable|numeric|gte:min_order_amount',
            'charge' => 'required|numeric|min:0',
        ]);

        try {
            $state = State::find($validated['state_id']);

            $validated['country_id'] = $state->country_id;

            DB::transaction(function () use ($validated) {
                ShippingCharge::create($validated);
            });

            return redirect()->route('admin.shippingcharges.index')->with('success', 'Shipping charge created successfully.');
        } catch (Throwable $e) {
            return back()->withErrors('Failed to create shipping charge. Please try again.')->withInput();
        }
    }

    public function edit(ShippingCharge $shippingcharge)
    {
        $states = State::all();
        return view('admin.shipping_charges.create', compact('shippingcharge', 'states'));
    }

    public function update(Request $request, ShippingCharge $shippingcharge)
    {
        $validated = $request->validate([
            'state_id' => 'required|exists:states,id',
            'min_weight' => 'required|numeric|min:0',
            'max_weight' => 'nullable|numeric|gte:min_weight',
            'min_order_amount' => 'required|numeric|min:0',
            'max_order_amount' => 'nullable|numeric|gte:min_order_amount',
            'charge' => 'required|numeric|min:0',
        ]);

        try {
            $state = State::find($validated['state_id']);

            $validated['country_id'] = $state->country_id;

            DB::transaction(function () use ($validated, $shippingcharge) {
                $shippingcharge->update($validated);
            });

            return redirect()->route('admin.shippingcharges.index')->with('success', 'Shipping charge updated successfully.');
        } catch (Throwable $e) {
            Log::error('Shipping charge update failed: ' . $e->getMessage());
            return back()->withErrors('Failed to update shipping charge. Please try again.')->withInput();
        }
    }

    public function destroy(ShippingCharge $shippingcharge)
    {
        try {
            $shippingcharge->delete();
            return redirect()->route('admin.shippingcharges.index')->with('success', 'Shipping charge deleted successfully.');
        } catch (Throwable $e) {
            Log::error('Shipping charge delete failed: ' . $e->getMessage());
            return back()->withErrors('Failed to delete shipping charge.');
        }
    }
}
