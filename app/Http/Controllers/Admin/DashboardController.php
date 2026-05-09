<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;    // Import the Order model
use App\Models\User;     // Import the User model
use App\Models\Enquiry;  // Import the Enquiry model
use Illuminate\Support\Facades\DB; // Import DB facade for aggregates
use Illuminate\Support\Facades\Log; // Import Log facade for error logging

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $user = auth()->user();
        if($user->user_type !== "admin") {
            $orders = $user->orders()->withCount('items')->latest()->paginate(5);
            return view('dashboard', compact('user', 'orders'));
        }

        try {

            // Total Orders: Count of all orders
            $totalOrders = Order::count();

            // Total Revenue: Sum of 'total' from 'completed' orders
            $totalRevenue = Order::where('status', 'completed')->sum('total');

            // New Customers (in the last 30 days):
            // Counts users whose 'user_type' is 'customer' and were created within the last 30 days.
            $newCustomers = User::where('user_type', 'customer')
                                ->where('created_at', '>=', now()->subDays(30))
                                ->count();

            // Pending Orders: Count of orders with 'pending' status
            $pendingOrders = Order::where('status', 'pending')->count();


            // --- Fetching Recent Activities Data ---

            // Recent Orders: Get the 5 most recently created orders, ordered by creation date descending
            $recentOrders = Order::orderBy('created_at', 'desc')
                                 ->take(5) // Limit to 5 records for recent activity
                                 ->get();

            // Recent Enquiries: Get the 5 most recently created enquiries, ordered by creation date descending
            $recentEnquiries = Enquiry::orderBy('created_at', 'desc')
                                     ->take(5) // Limit to 5 records for recent activity
                                     ->get();

            // --- Data for Charts ---

            // Sales Over Time (Last 7 Days): Daily sales sum for completed orders
            $salesDataForChart = Order::select(
                                    DB::raw('DATE(created_at) as date'),
                                    DB::raw('SUM(total) as total_sales')
                                )
                                ->where('status', 'completed')
                                ->where('created_at', '>=', now()->subDays(30))
                                ->groupBy('date')
                                ->orderBy('date')
                                ->get();

            // Prepare data for the sales chart
            $salesLabels = $salesDataForChart->pluck('date')->map(fn($date) => \Carbon\Carbon::parse($date)->format('M d'))->toArray();
            $salesValues = $salesDataForChart->pluck('total_sales')->toArray();

            // Order Status Distribution: Count of orders by status
            $orderStatusCounts = Order::select('status', DB::raw('count(*) as count'))
                                      ->groupBy('status')
                                      ->pluck('count', 'status')
                                      ->toArray();

            // Prepare data for the status distribution chart
            $statusLabels = ['pending', 'completed', 'cancelled', 'in-transit']; // Ensure all possible statuses are covered
            $statusColors = [
                'pending' => '#ffc107',    // Yellow
                'completed' => '#28a745',  // Green
                'cancelled' => '#dc3545',  // Red
                'in-transit' => '#17a2b8'  // Info/Cyan
            ];
            $statusData = [];
            $statusBackgroundColors = [];
            foreach ($statusLabels as $status) {
                $statusData[] = $orderStatusCounts[$status] ?? 0; // Get count or 0 if status not present
                $statusBackgroundColors[] = $statusColors[$status];
            }


            // Pass all the fetched data to the dashboard view
            return view('admin.dashboard', compact(
                'totalOrders',
                'totalRevenue',
                'newCustomers',
                'pendingOrders',
                'recentOrders',
                'recentEnquiries',
                'salesLabels',          // For sales chart X-axis
                'salesValues',          // For sales chart Y-axis
                'statusLabels',         // For status chart labels
                'statusData',           // For status chart data values
                'statusBackgroundColors'// For status chart colors
            ));

        } catch (\Throwable $e) {

            // Optionally redirect or show a friendly error page
            return redirect()->back()->with('error', 'Dashboard could not be loaded. Please try again later.');
        }
    }
}
