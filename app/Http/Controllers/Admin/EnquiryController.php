<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enquiry;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EnquiryController extends Controller
{
    /**
     * Display a listing of the enquiries.
     */
    public function index(Request $request)
    {
        $query = Enquiry::query();

        // Apply search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%"); // Search message content too
            });
        }

        // Apply reason filter
        if ($reason = $request->input('reason')) {
            $query->where('reason', 'like', "%{$reason}%");
        }

        // Order by latest first
        $enquiries = $query->orderBy('created_at', 'desc')->paginate(15); // Adjust per page as needed

        return view('admin.enquiries.index', compact('enquiries'));
    }

    /**
     * Display the specified enquiry.
     * You'll need to create a `show.blade.php` for this.
     */
    public function show(Enquiry $enquiry)
    {
        return view('admin.enquiries.show', compact('enquiry'));
    }

    /**
     * Remove the specified enquiry from storage.
     */
    public function destroy(Enquiry $enquiry)
    {
        try {
            DB::beginTransaction();
            $enquiryId = $enquiry->id;
            $enquiry->delete();
            DB::commit();
            return redirect()->route('admin.enquiries.index')
                             ->with('success', "Enquiry #{$enquiryId} from {$enquiry->name} deleted successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Failed to delete enquiry #{$enquiry->id}: " . $e->getMessage());
            return redirect()->back()
                             ->with('error', 'Failed to delete enquiry. Please try again.');
        }
    }
}