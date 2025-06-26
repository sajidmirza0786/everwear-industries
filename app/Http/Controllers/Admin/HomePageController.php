<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomePage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class HomePageController extends Controller
{
    // Show current homepage settings
    public function index()
    {
        $settings = HomePage::first();
        return view('admin.settings.index', compact('settings'));
    }

    // Show edit form
    public function edit()
    {
        $settings = HomePage::first();
        return view('admin.settings.edit', compact('settings'));
    }

    // Save updated settings
    public function update(Request $request)
    {
        $settings = HomePage::first();

        $request->validate([
            'title' => 'nullable|string|max:255',
            'keywords' => 'nullable|string',
            'description' => 'nullable|string',
            'mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,ico|max:1024',
            'banner_image_1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner_image_2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner_image_3' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'other_image_1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'other_image_2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            foreach (['logo', 'favicon', 'banner_image_1', 'banner_image_2', 'banner_image_3', 'other_image_1', 'other_image_2'] as $imageField) {
                if ($request->hasFile($imageField)) {
                    // Delete old image
                    if ($settings->$imageField && Storage::disk('public')->exists($settings->$imageField)) {
                        Storage::disk('public')->delete($settings->$imageField);
                    }
                
                    $image = $request->file($imageField);
                
                    // Create a unique name for the image
                    $uniqueName = Str::uuid() . '.' . $image->getClientOriginalExtension();
                    
                    // Resize the image and save it to storage
                    $imagePath = 'homepage/' . $uniqueName; 
                    $image = Image::make($image);

                    if(in_array($imageField, ['logo', 'favicon'])) {
                        // Resize the image while maintaining its aspect ratio and fit it into a 800x800 box
                        $image->fit(300, 180, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                    } else {
                        // Resize the image while maintaining its aspect ratio and fit it into a 800x800 box
                        $image->fit(1800, 800, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                    }

                    // Save the image to the storage folder
                    $image->save(storage_path('app/public/' . $imagePath));

                    // Store the relative path to the image in the database
                    $settings->$imageField = $imagePath;
                }
            }

            // Fill other fields
            $settings->fill($request->except(['logo', 'favicon', 'banner_image_1', 'banner_image_2', 'banner_image_3', 'other_image_1', 'other_image_2']));
            $settings->save();

            return redirect()->back()->with('success', 'Homepage settings updated successfully.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }
}
