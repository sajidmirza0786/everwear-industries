<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->orderByDesc('id')->paginate(10);

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
            'name'             => 'required|string|max:255|unique:categories,name',
            'parent_id'        => 'nullable|exists:categories,id',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'hsn'              => 'nullable|string|max:50',
            'title'            => 'nullable|string|max:255',
            'keyword'          => 'nullable|string|max:500',
            'description'      => 'nullable|string|max:500',
            'long_description' => 'nullable|string',
            'status'           => 'in:enable,disable',
        ]);

        DB::beginTransaction();
        try {
            $validated['slug'] = Str::slug($validated['name']) . '-' . Str::uuid();

            if ($request->hasFile('image')) {
                $validated['image'] = $this->handleImageUpload($request->file('image'));
            }

            Category::create($validated);

            DB::commit();

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Category created successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create category. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        $categories = Category::where('id', '!=', $category->id)
                               ->whereNull('parent_id')
                               ->get();

        return view('admin.categories.create', compact('category', 'categories'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255|unique:categories,name,' . $category->id,
            'parent_id'        => 'nullable|exists:categories,id',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'hsn'              => 'nullable|string|max:50',
            'title'            => 'nullable|string|max:255',
            'keyword'          => 'nullable|string|max:500',
            'description'      => 'nullable|string|max:500',
            'long_description' => 'nullable|string',
            'status'           => 'in:enable,disable',
        ]);

        DB::beginTransaction();
        try {
            if ($request->hasFile('image')) {
                $this->deleteImage($category->image);
                $validated['image'] = $this->handleImageUpload($request->file('image'));
            }

            $category->update($validated);

            DB::commit();

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Category updated successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update category. Please try again.');
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
            $this->deleteImage($category->image);

            $category->delete();

            DB::commit();

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Category deleted successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Failed to delete category. Please try again.');
        }
    }

    // =========================================================
    //  Private Helpers
    // =========================================================

    /**
     * Resize the uploaded image and store it via Storage disk.
     * Returns the stored relative path.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  int  $width
     * @param  int  $height
     * @return string
     */
    private function handleImageUpload($file, int $width = 800, int $height = 800): string
    {
        $filename  = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $directory = 'categories';

        $image = Image::make($file)
            ->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode($file->getClientOriginalExtension());

        Storage::disk('public')->put($directory . '/' . $filename, (string) $image);

        return $directory . '/' . $filename;
    }

    /**
     * Delete an image from the public disk if it exists.
     *
     * @param  string|null  $path
     * @return void
     */
    private function deleteImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}