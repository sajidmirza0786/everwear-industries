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
    public function index()
    {
        $settings = HomePage::first();
        return view('admin.settings.index', compact('settings'));
    }

    public function edit()
    {
        $settings = HomePage::first();
        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = HomePage::firstOrNew([]);

        $request->validate([
            'title'          => 'nullable|string|max:255',
            'keywords'       => 'nullable|string',
            'description'    => 'nullable|string',
            'mobile'         => 'nullable|string|max:20',
            'email'          => 'nullable|email',
            'logo'           => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'favicon'        => 'nullable|image|mimes:jpeg,png,jpg,gif,ico,webp|max:1024',
            'banner_image_1' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'banner_image_2' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'banner_image_3' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'other_image_1'  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'other_image_2'  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        try {
            $imageFields = [
                'logo'           => ['w' => 400,  'h' => 200,  'type' => 'logo'],
                'favicon'        => ['w' => 64,   'h' => 64,   'type' => 'favicon'],
                'banner_image_1' => ['w' => 1800, 'h' => 800,  'type' => 'banner'],
                'banner_image_2' => ['w' => 1800, 'h' => 800,  'type' => 'banner'],
                'banner_image_3' => ['w' => 1800, 'h' => 800,  'type' => 'banner'],
                'other_image_1'  => ['w' => 1200, 'h' => 600,  'type' => 'banner'],
                'other_image_2'  => ['w' => 1200, 'h' => 600,  'type' => 'banner'],
            ];

            foreach ($imageFields as $field => $config) {
                if (!$request->hasFile($field)) continue;

                $file = $request->file($field);

                // Delete old file from storage
                if (!empty($settings->$field) && Storage::disk('public')->exists($settings->$field)) {
                    Storage::disk('public')->delete($settings->$field);
                }

                $ext        = $file->getClientOriginalExtension();
                $uniqueName = Str::uuid() . '.' . $ext;
                $folder     = 'homepage';
                $savePath   = storage_path('app/public/' . $folder . '/' . $uniqueName);
                $dbPath     = $folder . '/' . $uniqueName;

                // Make sure directory exists
                if (!file_exists(storage_path('app/public/' . $folder))) {
                    mkdir(storage_path('app/public/' . $folder), 0755, true);
                }

                $img = Image::make($file);

                if ($config['type'] === 'logo') {
                    // Logo: resize to max width/height, keep aspect ratio, no crop
                    $img->resize($config['w'], $config['h'], function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize(); // never enlarge
                    });
                } elseif ($config['type'] === 'favicon') {
                    // Favicon: square crop then resize
                    $img->fit($config['w'], $config['h']);
                } else {
                    // Banners: resize to max width, keep aspect ratio
                    $img->resize($config['w'], $config['h'], function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }

                $img->save($savePath);

                // Store relative path (no leading slash)
                $settings->$field = $dbPath;
            }

            // Save all other text fields
            $settings->fill(
                $request->except(array_keys($imageFields))
            );
            $settings->save();

            return redirect()->back()->with('success', 'Settings updated successfully.');

        } catch (\Throwable $e) {
            return back()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }
}