<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Enquiry;
use Illuminate\Support\Facades\DB;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;

class PageController extends Controller
{
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

    public function storeEnquiry(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'name'    => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
                'email'   => ['required', 'email', 'max:255'],
                'mobile'  => ['required', 'digits_between:10,13'],
                'subject' => ['required', 'string', 'max:255', 'not_regex:/https?:\/\//i'],
                'message' => ['required', 'string', 'max:1000', 'not_regex:/https?:\/\//i'],
            ], [
                'subject.not_regex' => 'Subject cannot contain URLs.',
                'message.not_regex' => 'Message cannot contain URLs.',
            ]);

            $validated['ip_address'] = $request->ip();
            $validated['page_url'] = $request->headers->get('referer');

            try {
                DB::beginTransaction();
                Enquiry::create($validated);
                DB::commit();

                return back()->with('success', 'Thank you! We will contact you shortly.');

            } catch (\Throwable $e) {
                DB::rollBack();
                return back()->with('error', 'Something went wrong. Please try again later.');
            }
        } else {
            return redirect(route('contact'));
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
