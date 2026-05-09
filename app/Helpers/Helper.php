<?php

use App\Models\AccessLog;
use App\Models\HomePage;
use App\Models\ShippingCharge;
use App\Models\Category;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Support\Collection;

if (!function_exists('accessLog')) {
    function accessLog($action, $type, $requestData = null, $userId = null, $model_name = null, $model_id = null)
    {
        // If action is not 'update', convert request data to JSON
        if ($action !== 'update') {
            $requestData = json_encode($requestData);
        }

        // Format the action
        $formattedAction = strtolower($type) . '_' . $action . '';

        // Prepare data for storing in the database
        $uidData = [
            'model_type'=> $model_name,
            'model_id'=> $model_id,
            "date" => date("Y-m-d"),
            "time" => date("H:m:i"),
            "server_timezone" => date("Y-m-d H:i:s"),
            'user_id' => $userId,
            'action' => $formattedAction,
            "description" => $requestData,
        ];

        // Store data in the database
        AccessLog::create($uidData);

        return true; // Return true
    }
}

if (!function_exists('get_logs')) {
    function get_logs($model_name = null, $model_id = null)
    {
        // Store data in the database
        $logs = AccessLog::where('model_type', $model_name)->where('model_id', $model_id)
            ->orderByDesc('id')->get();

        return $logs; // Return true
    }
}

/**
 * Compares the old and new values to generate a formatted description of changes.
 *
 * @param  mixed $oldValue The old value to compare.
 * @param  mixed $newValue The new value to compare.
 * @return string Returns a formatted description of the changes.
 */
if (!function_exists('compareValues')) {
    function compareValues($oldValue, $newValue)
    {
        // Convert the old value to an associative array for comparison
        $oldValues = json_decode(json_encode($oldValue), true);

        // Calculate the differences between the validated data and the old values
        $diff = array_diff_assoc($newValue, $oldValues);

        // Initialize the formatted description of changes
        $formattedDescription = '';

        // Check if there are any differences
        if (!empty($diff)) {
            // If differences exist, iterate through each difference
            foreach ($diff as $key => $item) {
                // Append each difference to the formatted description
                $formattedDescription .= $key . ' updated From: ' . $oldValue[$key] . ' To: ' . $item . '' . "\n";
            }
        }

        // Return the formatted description of changes
        return $formattedDescription;
    }
}

if(!function_exists('settings')) {
    function settings() {
        $settings = HomePage::first();
        return $settings;
    }
}

/**
 * Calculate shipping charge based on country, state, total weight, and order amount.
*
* @param string|null $country
* @param string|null $state
* @param float $totalWeight
* @param float $orderAmount
* @return float
*/
if(!function_exists('calculateShippingCharge')) {
    function calculateShippingCharge(?int $countryId, ?int $stateId, float $totalWeight, float $orderAmount): float
    {
        $charge = ShippingCharge::where(function ($query) use ($countryId, $stateId) {
                $query->where('country_id', $countryId)
                      ->orWhereNull('country_id');

                if ($stateId) {
                    $query->where(function ($q) use ($stateId) {
                        $q->where('state_id', $stateId)
                          ->orWhereNull('state_id');
                    });
                } else {
                    $query->whereNull('state_id');
                }
            })
            ->where('min_weight', '<=', $totalWeight)
            ->where(function ($q) use ($totalWeight) {
                $q->whereNull('max_weight')->orWhere('max_weight', '>=', $totalWeight);
            })
            ->where('min_order_amount', '<=', $orderAmount)
            ->where(function ($q) use ($orderAmount) {
                $q->whereNull('max_order_amount')->orWhere('max_order_amount', '>=', $orderAmount);
            })
            ->orderBy('min_weight')
            ->first();

        return $charge?->charge ?? 0.00;
    }
}

if(!function_exists('ucategories')) {
    function ucategories()
    {
        return Category::where('status', 'enable')->whereNull('parent_id')->latest()->take(8)->get();
    }
}

if(!function_exists('randomProducts')) {
    function randomProducts()
    {
        return Product::where('status', 'enable')->limit(8)->inRandomOrder()->get();
    }
}

if (!function_exists('cartItems')) {
    function cartItems(): Collection
    {
        if (Auth::check()) {
            return Cart::where('user_id', Auth::id())->with('product')->get();
        }

        $cart = session()->get('cart', []);
        $cartItems = [];

        foreach ($cart as $item) {
            $cartItems[] = (object) $item;
        }

        return collect($cartItems);
    }
}

function cartItemCount(): int
{
    return cartItems()->count();
}

if (!function_exists('getShortName')) {
    /**
     * Get initials from a full name (e.g., "Sajid Mirza" → "SM")
     *
     * @param string $name
     * @return string
     */
    function getShortName(string $name): string
    {
        return collect(explode(' ', trim($name)))
            ->filter()
            ->map(fn($word) => strtoupper(Str::substr($word, 0, 1)))
            ->implode('');
    }
}

if (!function_exists('get_first_char')) {
    function get_first_char($name)
    {
        return Str::substr(trim($name), 0, 1);
    }
}

if (!function_exists('get_last_char')) {
    function get_last_char($name)
    {
        // Remove spaces for strict last letter
        return Str::substr(str_replace(' ', '', trim($name)), -1);
    }
}

