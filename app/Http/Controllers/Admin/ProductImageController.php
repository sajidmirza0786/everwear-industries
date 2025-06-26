<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class ProductImageController extends Controller
{
    public function storeSingle(Request $request, Product $product)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        DB::beginTransaction();

        try {
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                
                // Create a unique name for the image
                $uniqueName = str()->uuid() . '.' . $image->getClientOriginalExtension();
                
                // Resize the image and save it to storage
                $imagePath = 'products/product_images/' . $uniqueName; 
                $image = Image::make($image);

                // Resize the image while maintaining its aspect ratio and fit it into a 800x800 box
                $image->fit(800, 800, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize(); // Prevent the image from becoming bigger than its original size
                });

                // Save the image to the storage folder
                $image->save(storage_path('app/public/' . $imagePath));

                $product->images()->create([
                    'image_path' => $imagePath,
                    'image_name' => $request->file('image')->getClientOriginalName(),
                ]);
            }

            DB::commit();
            return back()->with('success', 'Image uploaded successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to upload image. ' . $e->getMessage());
        }
    }

    public function destroy(Product $product, ProductImage $image)
    {
        // Ensure image belongs to the given product
        if ($image->product_id !== $product->id) {
            return back()->with('error', 'Image does not belong to this product.');
        }

        DB::beginTransaction();

        try {
            // Delete the file from storage (if it exists)
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }

            // Delete the database record
            $image->delete();

            DB::commit();
            return back()->with('success', 'Image deleted successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete image. ' . $e->getMessage());
        }
    }
}