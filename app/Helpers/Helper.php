<?php

use App\Models\AccessLog;
use App\Models\HomePage;
use App\Models\ShippingCharge;
use App\Models\Category;
use App\Models\Product;

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
        return Category::where('status', 'enable')->orderByDesc('id')->get();
    }
}

if(!function_exists('randomProducts')) {
    function randomProducts()
    {
        return Product::where('status', 'enable')->limit(8)->inRandomOrder()->get();
    }
}
