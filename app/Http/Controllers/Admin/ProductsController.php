<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();

        $productsQuery = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $productsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('code', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('category_id')) {
            $productsQuery->where('category_id', $request->input('category_id'));
        }

        $products = $productsQuery->latest()->paginate(10);
        $trashedProductsCount = Product::onlyTrashed()->count();
        $products->appends($request->only(['search', 'category_id']));

        return view('admin.products.index', compact('products', 'categories', 'trashedProductsCount'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'      => 'required|exists:categories,id',
            'name'             => 'required|string|max:255|unique:products,name',
            'slug'             => 'nullable|string|unique:products,slug',
            'code'             => 'required|string|max:255|unique:products,code',
            'mrp'              => 'required|numeric|min:1',
            'selling'          => 'required|numeric|min:1|lte:mrp',
            'stock'            => 'required|numeric|min:0',
            'gram_weight'      => 'required|numeric|min:1',
            'size'             => 'nullable|string',
            'color'            => 'nullable|string',
            'title'            => 'nullable|string',
            'keyword'          => 'nullable|string',
            'image'            => 'nullable|image|max:2048',
            'status'           => 'in:enable,disable',
            'description'      => 'nullable|string',
            'long_description' => 'nullable|string',
            'video_url'        => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $validated['slug'] = $this->handleSlug(
                $validated['slug'] ?? $validated['name'],
                'products'
            );

            if ($request->hasFile('image')) {
                $validated['image'] = $this->handleImageUpload($request->file('image'));
            }

            Product::create($validated);

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Failed to create product. Please try again.');
        }
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.create', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id'      => 'required|exists:categories,id',
            'name'             => 'required|string|max:255|unique:products,name,' . $product->id,
            'slug'             => 'nullable|string|unique:products,slug,' . $product->id,
            'code'             => 'required|string|max:255|unique:products,code,' . $product->id,
            'mrp'              => 'required|numeric|min:1',
            'selling'          => 'required|numeric|min:1|lte:mrp',
            'stock'            => 'required|numeric|min:0',
            'gram_weight'      => 'required|numeric|min:1',
            'size'             => 'nullable|string',
            'color'            => 'nullable|string',
            'title'            => 'nullable|string',
            'keyword'          => 'nullable|string',
            'image'            => 'nullable|image|max:2048',
            'status'           => 'in:enable,disable',
            'description'      => 'nullable|string',
            'long_description' => 'nullable|string',
            'video_url'        => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            if (empty($validated['slug'])) {
                $validated['slug'] = $this->handleSlug(
                    $validated['name'],
                    'products',
                    $product->id
                );
            }

            if ($request->hasFile('image')) {
                $this->deleteImage($product->image);
                $validated['image'] = $this->handleImageUpload($request->file('image'));
            }
     
            $product->update($validated);

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Failed to update product. Please try again.');
        }
    }

    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    public function destroy(Product $product)
    {
        DB::beginTransaction();
        try {
            $product->delete();
            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete product. Please try again.');
        }
    }

    public function trash()
    {
        $categories = Category::all();
        $trashedProductsCount = Product::onlyTrashed()->count();
        $products = Product::onlyTrashed()->with('category')->latest()->paginate(10);
        return view('admin.products.index', compact('products', 'trashedProductsCount', 'categories'));
    }

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
            return redirect()->route('admin.products.trash')->with('error', 'Failed to restore product.');
        }
    }

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
            return redirect()->back()->with('error', 'Failed to permanently delete product.');
        }
    }

    // =========================================================
    //  Private Helpers
    // =========================================================

    /**
     * Generate a unique slug for the given table.
     *
     * Slugifies $base, then appends -2, -3, … until the slug
     * is not found in $table (excluding $excludeId for updates).
     *
     * Usage:
     *   // Create:  $this->handleSlug('Ring Name', 'products')
     *   // Update:  $this->handleSlug('Ring Name', 'products', $product->id)
     *   // Works for categories too: $this->handleSlug($name, 'categories')
     */
    private function handleSlug(string $base, string $table, ?int $excludeId = null): string
    {
        $slug     = Str::slug($base);
        $original = $slug;
        $counter  = 2;

        while (true) {
            $query = DB::table($table)->where('slug', $slug);

            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            if (! $query->exists()) {
                break;
            }

            $slug = $original . '-' . $counter++;
        }

        return $slug;
    }

    /**
     * Resize and store an uploaded image via Storage disk.
     * Returns the relative path stored in the DB.
     */
    private function handleImageUpload($file, int $width = 800, int $height = 800): string
    {
        $filename  = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $directory = 'products';

        $encoded = Image::make($file)
            ->fit($width, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode($file->getClientOriginalExtension());

        Storage::disk('public')->put($directory . '/' . $filename, (string) $encoded);

        return $directory . '/' . $filename;
    }

    /**
     * Delete an image from the public disk if it exists.
     */
    private function deleteImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}