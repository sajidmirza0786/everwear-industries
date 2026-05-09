<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
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
                $file      = $request->file('image');
                $directory = 'products/product_images';
                $uniqueName = str()->uuid() . '.' . $file->getClientOriginalExtension();

                $encoded = Image::make($file)
                    ->fit(800, 800, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->encode($file->getClientOriginalExtension());

                Storage::disk('public')->put($directory . '/' . $uniqueName, (string) $encoded);

                $product->images()->create([
                    'image_path' => $directory . '/' . $uniqueName,
                    'image_name' => $file->getClientOriginalName(),
                ]);
            }

            DB::commit();
            return back()->with('success', 'Image uploaded successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to upload image.');
        }
    }

    public function destroy(Product $product, ProductImage $image)
    {
        if ($image->product_id !== $product->id) {
            return back()->with('error', 'Image does not belong to this product.');
        }

        DB::beginTransaction();

        try {
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }

            $image->delete();

            DB::commit();
            return back()->with('success', 'Image deleted successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete image.');
        }
    }
}