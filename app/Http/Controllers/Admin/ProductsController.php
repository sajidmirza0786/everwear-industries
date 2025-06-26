<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ProductsController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $categories = Category::all();

        // Start building the product query
        // By default, if SoftDeletes trait is used, only non-trashed items are retrieved
        $productsQuery = Product::with('category');

        // Search by product name or code
        if ($request->filled('search')) {
            $search = $request->input('search');
            $productsQuery->where(function($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('code', 'like', '%' . $search . '%');
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $categoryId = $request->input('category_id');
            $productsQuery->where('category_id', $categoryId);
        }

        // Order and paginate the results
        $products = $productsQuery->latest()->paginate(10);

        // Get count of trashed products for the UI link
        $trashedProductsCount = Product::onlyTrashed()->count();

        // Append search and category_id parameters to pagination links
        $products->appends($request->only(['search', 'category_id']));

        return view('admin.products.index', compact('products', 'categories', 'trashedProductsCount'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255|unique:products,name',
            'slug' => 'nullable|string|unique:products,slug',
            'code' => 'required|string|max:255|unique:products,code',
            'mrp' => 'required|numeric|min:1',
            'selling' => 'required|numeric|min:1|lte:mrp',
            'stock' => 'required|numeric|min:0',
            'gram_weight' => 'required|numeric|min:1',
            'size' => 'nullable|string',
            'color' => 'nullable|string',
            'title' => 'nullable|string',
            'keyword' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'status' => 'in:enable,disable',
            'description' => 'nullable|string',
            'long_description' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            if (empty($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                
                // Create a unique name for the image
                $uniqueName = Str::uuid() . '.' . $image->getClientOriginalExtension();
                
                // Resize the image and save it to storage
                $imagePath = 'products/' . $uniqueName; 
                $image = Image::make($image);

                // Resize the image while maintaining its aspect ratio and fit it into a 800x800 box
                $image->fit(800, 800, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize(); // Prevent the image from becoming bigger than its original size
                });

                // Save the image to the storage folder
                $image->save(storage_path('app/public/' . $imagePath));

                // Store the relative path to the image in the database
                $validated['image'] = $imagePath;
            }

            Product::create($validated);

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Product Store Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create product. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.create', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255|unique:products,name,' . $product->id,
            'slug' => 'nullable|string|unique:products,slug,' . $product->id,
            'code' => 'required|string|max:255|unique:products,code,' . $product->id,
            'mrp' => 'required|numeric|min:1',
            'selling' => 'required|numeric|min:1|lte:mrp',
            'stock' => 'required|numeric|min:0',
            'gram_weight' => 'required|numeric|min:1',
            'size' => 'nullable|string',
            'color' => 'nullable|string',
            'title' => 'nullable|string',
            'keyword' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'status' => 'in:enable,disable',
            'description' => 'nullable|string',
            'long_description' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            if (empty($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            if ($request->hasFile('image')) {

                // Check if the product has an old image, and delete it if it exists
                if (Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }

                $image = $request->file('image');
                
                // Create a unique name for the image
                $uniqueName = Str::uuid() . '.' . $image->getClientOriginalExtension();
                
                // Resize the image and save it to storage
                $imagePath = 'products/' . $uniqueName; 
                $image = Image::make($image);

                // Resize the image while maintaining its aspect ratio and fit it into a 800x800 box
                $image->fit(800, 800, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize(); // Prevent the image from becoming bigger than its original size
                });

                // Save the image to the storage folder
                $image->save(storage_path('app/public/' . $imagePath));

                // Store the relative path to the image in the database
                $validated['image'] = $imagePath;
            }

            $product->update($validated);

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Product Update Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update product. Please try again.');
        }
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        DB::beginTransaction();
        try {
            $product->delete();
            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Product Delete Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete product. Please try again.');
        }
    }
    /**
     * Show all soft-deleted products.
     */
    public function trash()
    {
        $categories = Category::all();
        $trashedProductsCount = Product::onlyTrashed()->count();
        $products = Product::onlyTrashed()->with('category')->latest()->paginate(10);
        return view('admin.products.index', compact('products', 'trashedProductsCount', 'categories'));
    }

    /**
     * Restore a soft-deleted product.
     */
    public function restore($id)
    {
        DB::beginTransaction();
        try {
            $product = Product::onlyTrashed()->where('slug', $id)->firstOrFail();
            $product->restore();

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Product restored successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('admin.products.trash')->with('error', 'Failed to restore product.'.$e->getMessage());
        }
    }

    /**
     * Permanently delete a soft-deleted product.
     */
    public function forceDelete($id)
    {
        DB::beginTransaction();
        try {
            $product = Product::onlyTrashed()->findOrFail($id);
            $product->forceDelete();

            DB::commit();
            return redirect()->route('admin.products.trash')->with('success', 'Product permanently deleted.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Product Force Delete Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to permanently delete product.');
        }
    }

}
