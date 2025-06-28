<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class PageController extends Controller
{
    public function listing(Request $request, $slug = null)
    {
        if($request->filled('search')) {
            $searchTerm = $request->get('search');
            $products = Product::where('status', 'enable')
                ->where('name', 'like', "%{$searchTerm}%")
                ->get();
            return view('users.listings', compact('products'));
        }

        // Try finding a category by slug
        $category = Category::where('slug', $slug)
                            ->where('status', 'enable')
                            ->first();

        if ($category) {
            // If it's a category, fetch related products
            $products = Product::where('category_id', $category->id)
                               ->where('status', 'enable')
                               ->orderByDesc('id')
                               ->get();

            return view('users.listings', compact('category', 'products'));
        }

        // If not a category, try finding a product
        $product = Product::where('slug', $slug)
                          ->where('status', 'enable')
                          ->first();

        if ($product) {
            $similarProducts = Product::where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->where('status', 'enable')
                ->inRandomOrder()->limit(8)->get();
            return view('users.product', compact('product','similarProducts'));
        }

        // If neither found, show 404
        abort(404);
    }
}
