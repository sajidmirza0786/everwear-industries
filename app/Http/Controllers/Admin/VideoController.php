<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Video;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VideoController extends Controller
{
    /**
     * Display a listing of the videos.
     */
    public function index(Request $request)
    {
        $query = Video::query();

        // Apply search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('url', 'like', "%{$search}%");
            });
        }

        // Order by latest first
        $videos = $query->orderBy('created_at', 'desc')->paginate(10); // Adjust per page as needed

        return view('admin.videos.index', compact('videos'));
    }

    /**
     * Show the form for creating a new video.
     */
    public function create()
    {
        return view('admin.videos.create'); 
    }

    /**
     * Store a newly created video in storage.
     */
    public function store(Request $request)
    {
        try {
            // 1. Validate the incoming request data
            $validatedData = $request->validate([
                'title' => ['nullable', 'string', 'max:255'],
                'url' => ['required', 'url', 'max:2048'], // Max URL length for databases
                'description' => ['nullable', 'string', 'max:1000'],
            ]);

            DB::beginTransaction();

            // 2. Create the new video
            Video::create($validatedData);

            DB::commit();

            return redirect()->route('admin.videos.index')
                             ->with('success', 'Video added successfully!');

        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()
                             ->withErrors($e->errors())
                             ->withInput()
                             ->with('error', 'Please correct the errors in the form.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Failed to add video. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified video.
     */
    public function edit(Video $video)
    {
        return view('admin.videos.create', compact('video')); // Pass the video to the form
    }

    /**
     * Update the specified video in storage.
     */
    public function update(Request $request, Video $video)
    {
        try {
            // 1. Validate the incoming request data
            $validatedData = $request->validate([
                'title' => ['nullable', 'string', 'max:255'],
                'url' => ['required', 'url', 'max:2048'],
                'description' => ['nullable', 'string', 'max:1000'],
            ]);

            DB::beginTransaction();

            // 2. Update the video
            $video->update($validatedData);

            DB::commit();

            return redirect()->route('admin.videos.index')
                             ->with('success', 'Video updated successfully!');

        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()
                             ->withErrors($e->errors())
                             ->withInput()
                             ->with('error', 'Please correct the errors in the form.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Failed to update video. Please try again.');
        }
    }

    /**
     * Remove the specified video from storage.
     */
    public function destroy(Video $video)
    {
        try {
            DB::beginTransaction();
            $videoId = $video->id; // Store ID before deleting
            $video->delete();
            DB::commit();
            return redirect()->route('admin.videos.index')
                             ->with('success', 'Video #' . $videoId . ' deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->with('error', 'Failed to delete video. Please try again.');
        }
    }
}