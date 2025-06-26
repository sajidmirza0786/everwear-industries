<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the admin.categories.
     */
    public function index(Request $request)
    {
        $query = Category::query();

        // Handle search by name
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Order by name for consistency
        $categories = $query->orderBy('name')->paginate(10); 

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        $categories = Category::whereNull('parent_id')->get();
        return view('admin.categories.create', compact('categories'));
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'slug' => 'nullable|string|unique:categories,slug',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image',
            'hsn' => 'nullable|string',
            'title' => 'nullable|string',
            'keyword' => 'nullable|string',
            'description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'status' => 'in:enable,disable',
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
                $imagePath = 'categories/' . $uniqueName; 
                $image = Image::make($image)->resize(800, 800);

                // Save the image to the storage folder
                $image->save(storage_path('app/public/' . $imagePath));

                // Store the relative path to the image in the database
                $validated['image'] = $imagePath;
            }

            Category::create($validated);

            DB::commit();
            return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Category Store Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create category. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        $categories = Category::where('id', '!=', $category->id)->whereNull('parent_id')->get();
        return view('admin.categories.create', compact('category', 'categories'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'slug' => 'nullable|string|unique:categories,slug,' . $category->id,
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image',
            'hsn' => 'nullable|string',
            'title' => 'nullable|string',
            'keyword' => 'nullable|string',
            'description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'status' => 'in:enable,disable',
        ]);

        DB::beginTransaction();
        try {
            if (empty($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            if ($request->hasFile('image')) {

                // Check if the product has an old image, and delete it if it exists
                if (Storage::disk('public')->exists($category->image)) {
                    Storage::disk('public')->delete($category->image);
                }
                
                $image = $request->file('image');
                
                // Create a unique name for the image
                $uniqueName = Str::uuid() . '.' . $image->getClientOriginalExtension();
                
                // Resize the image and save it to storage
                $imagePath = 'categories/' . $uniqueName; 
                $image = Image::make($image)->resize(800, 800);

                // Save the image to the storage folder
                $image->save(storage_path('app/public/' . $imagePath));

                // Store the relative path to the image in the database
                $validated['image'] = $imagePath;
            }

            $category->update($validated);

            DB::commit();
            return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Category Update Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update category. Please try again.');
        }
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category)
    {
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category)
    {
        DB::beginTransaction();
        try {
            $category->delete();
            DB::commit();
            return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Category Delete Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete category. Please try again.');
        }
    }
}
