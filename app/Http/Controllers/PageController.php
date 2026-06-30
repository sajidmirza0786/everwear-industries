<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Enquiry;
use Illuminate\Support\Facades\DB;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;
use App\Mail\EnquiryMail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    private const ORDER_SUBJECTS = [
        'Order Enquiry',
        'Shipping & Delivery',
        'Returns & Refunds',
    ];

    private const ALL_SUBJECTS = [
        'Order Enquiry',
        'Shipping & Delivery',
        'Returns & Refunds',
        'Product Information',
        'General Support',
        'Other',
    ];

    public function categories()
    {
        $categories   = Category::where('status', 'enable')->whereNull('parent_id')->withCount('products')->get();
        return view('users.categories', compact('categories'));
    }

    public function listing(Request $request, $slug = null)
    {
        try {
            if ($request->filled('search')) {
                $searchTerm = $request->get('search');
                $products = Product::where('status', 'enable')
                    ->where('name', 'like', "%{$searchTerm}%")
                    ->get();
                return view('users.listings', compact('products'));
            }

            $category = Category::where('slug', $slug)
                ->where('status', 'enable')
                ->first();

            if ($category) {
                $products = Product::where('category_id', $category->id)
                    ->where('status', 'enable')
                    ->orderByDesc('id')
                    ->get();
                return view('users.listings', compact('category', 'products'));
            }

            $product = Product::with(['images', 'attributes' => function ($q) {
                $q->where('status', 'enable')->orderBy('id');
            }])
                ->where('slug', $slug)
                ->where('status', 'enable')
                ->first();

            if ($product) {
                $similarProducts = Product::where('category_id', $product->category_id)
                    ->where('id', '!=', $product->id)
                    ->where('status', 'enable')
                    ->inRandomOrder()->limit(8)->get();
                return view('users.product', compact('product', 'similarProducts'));
            }

            abort(404);
        } catch (\Throwable $e) {
            return back()->withErrors('Something went wrong. Please try again.')->withInput();
        }
    }

    // public function storeEnquiry(Request $request)
    // {
    //     if ($request->isMethod('post')) {
    //         $validated = $request->validate([
    //             'name'    => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
    //             'email'   => ['required', 'email', 'max:255'],
    //             'mobile'  => ['required', 'digits_between:10,13'],
    //             'subject' => ['required', 'string', 'max:255', 'not_regex:/https?:\/\//i'],
    //             'message' => ['required', 'string', 'max:1000', 'not_regex:/https?:\/\//i'],
    //         ], [
    //             'subject.not_regex' => 'Subject cannot contain URLs.',
    //             'message.not_regex' => 'Message cannot contain URLs.',
    //         ]);

    //         $validated['ip_address'] = $request->ip();
    //         $validated['page_url'] = $request->headers->get('referer');

    //         try {
    //             DB::beginTransaction();
    //             Enquiry::create($validated);
    //             DB::commit();

    //             return back()->with('success', 'Thank you! We will contact you shortly.');

    //         } catch (\Throwable $e) {
    //             DB::rollBack();
    //             return back()->with('error', 'Something went wrong. Please try again later.');
    //         }
    //     } else {
    //         return redirect(route('contact'));
    //     }
    // }

    public function storeEnquiry(Request $request)
    {
        if (! $request->isMethod('post')) {
            return redirect()->route('contact');
        }

        // ── Rate limiting ────────────────────────────────────────────────────
        $key = 'enquiry:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->with('error', "Too many submissions. Please try again in {$seconds} seconds.");
        }
        RateLimiter::hit($key, 300); // 5minutes

        // ── Validation ───────────────────────────────────────────────────────
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'email'   => ['required', 'email:rfc,dns', 'max:255'],
            'mobile'  => ['required', 'digits_between:10,13'],
            'subject' => ['required', 'string', Rule::in(self::ALL_SUBJECTS)],
            'message' => ['required', 'string', 'max:1000', 'not_regex:/https?:\/\//i'],
        ], [
            'subject.in'        => 'Please select a valid subject from the list.',
            'message.not_regex' => 'Message cannot contain URLs.',
            'email.email'       => 'Please enter a valid email address.',
        ]);

        $validated['ip_address'] = $request->ip();
        $validated['page_url']   = $request->headers->get('referer');

        // ── Resolve RECIPIENT address based on subject ───────────────────────
        $isOrderRelated = in_array($validated['subject'], self::ORDER_SUBJECTS, strict: true);

        // $toAddress = $isOrderRelated
        //     ? 'order@everwearindustries.com'
        //     : 'support@everwearindustries.com';

        $toAddress = $isOrderRelated
            ? 'cypwebtechs@gmail.com'
            : 'sajidmirja19@gmail.com';

        // ── Save + Send ──────────────────────────────────────────────────────
        try {
            DB::beginTransaction();
            Enquiry::create($validated);
            DB::commit();

            Mail::to($toAddress)->send(new EnquiryMail($validated));

            return back()->with('success', 'Thank you! We will contact you shortly.');

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Something went wrong. Please try again later.');
        }
    }

    public function testMail() {
        $recipientEmail = 'cypwebtechs@gmail.com';
        try {
            Mail::to($recipientEmail)->send(new TestMail());
            return back()->with('success', "Test email sent to " . $recipientEmail . "!");
        } catch (\Exception $e) {
            return "Failed to send email: " . $e->getMessage();
        }
    }

}
