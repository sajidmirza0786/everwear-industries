<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    // ─── Shared validation rules ───────────────────────────────────────────────

    /**
     * Returns the validation rule array.
     * Pass the current coupon when updating so the unique rule ignores its own row.
     */
    private function rules(?Coupon $coupon = null): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('coupons', 'code')->ignore($coupon?->id),
            ],
            'description'          => ['nullable', 'string', 'max:255'],
            'discount_type'        => ['required', Rule::in(['flat', 'percent'])],
            'discount_value'       => ['required', 'numeric', 'min:0.01'],
            'max_discount_amount'  => ['nullable', 'numeric', 'min:0'],
            'min_order_amount'     => ['nullable', 'numeric', 'min:0'],
            'max_order_amount'     => [
                'nullable',
                'numeric',
                'min:0',
                // max_order_amount must be > min_order_amount when both are provided
                function ($attribute, $value, $fail) {
                    $min = request()->input('min_order_amount');
                    if ($min !== null && $value !== null && (float) $value < (float) $min) {
                        $fail('Maximum order amount must be greater than the minimum order amount.');
                    }
                },
            ],
            'max_uses'             => ['nullable', 'integer', 'min:1'],
            'max_uses_per_user'    => ['nullable', 'integer', 'min:1'],
            'applicable_products'  => ['nullable', 'json'],
            'applicable_categories'=> ['nullable', 'json'],
            'starts_at'            => ['nullable', 'date'],
            'expires_at'           => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active'            => ['boolean'],
        ];
    }

    /**
     * Sanitise and prepare validated data before saving.
     * - Uppercases code.
     * - Decodes JSON arrays for applicable_products / applicable_categories.
     * - Nullifies max_discount_amount when type is flat (no-op but keeps DB clean).
     */
    private function prepare(array $data): array
    {
        $data['code'] = strtoupper($data['code']);

        // Decode JSON targeting arrays; store null when empty/absent
        foreach (['applicable_products', 'applicable_categories'] as $field) {
            if (!empty($data[$field])) {
                $decoded = json_decode($data[$field], true);
                // Keep only positive integer IDs
                $data[$field] = is_array($decoded)
                    ? array_values(array_filter(array_map('intval', $decoded), fn($v) => $v > 0))
                    : null;
                // Normalise empty array → null
                if (empty($data[$field])) {
                    $data[$field] = null;
                }
            } else {
                $data[$field] = null;
            }
        }

        // A cap only makes sense for percentage discounts
        if (($data['discount_type'] ?? 'flat') === 'flat') {
            $data['max_discount_amount'] = null;
        }

        return $data;
    }

    // ─── CRUD ─────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = Coupon::query();

        // Search by code or description
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', '%' . strtoupper(trim($search)) . '%')
                  ->orWhere('description', 'like', '%' . trim($search) . '%');
            });
        }

        // Filter by status
        match ($request->input('status')) {
            'active'   => $query->where('is_active', true)
                                ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()))
                                ->where(fn($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now())),
            'inactive' => $query->where('is_active', false),
            'expired'  => $query->where('expires_at', '<', now()),
            default    => null,
        };

        // Filter by type
        if ($type = $request->input('type')) {
            $query->where('discount_type', $type);
        }

        $coupons = $query->latest()->paginate(20)->withQueryString();

        // Stats for the header cards
        $activeCouponsCount = Coupon::where('is_active', true)
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()))
            ->where(fn($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->count();

        $expiredCouponsCount = Coupon::where('expires_at', '<', now())->count();

        return view('admin.coupons.index', compact('coupons', 'activeCouponsCount', 'expiredCouponsCount'));
    }

    public function create()
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data = $this->prepare($data);

        Coupon::create($data);

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon "' . $data['code'] . '" created successfully.');
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.create', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $data = $request->validate($this->rules($coupon));
        $data = $this->prepare($data);

        $coupon->update($data);

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon "' . $data['code'] . '" updated successfully.');
    }

    public function destroy(Coupon $coupon)
    {
        $code = $coupon->code;
        $coupon->delete();

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon "' . $code . '" deleted.');
    }

    /**
     * PATCH /admin/coupons/{coupon}/toggle
     * Quick active/inactive toggle from the index table.
     */
    public function toggle(Coupon $coupon)
    {
        $coupon->update(['is_active' => !$coupon->is_active]);

        $state = $coupon->is_active ? 'activated' : 'deactivated';
        return back()->with('success', 'Coupon "' . $coupon->code . '" ' . $state . '.');
    }

    // ─── AJAX: validate coupon ─────────────────────────────────────────────────

    /**
     * POST /admin/coupons/validate
     * Body: { code: string, subtotal: float, user_id?: int }
     * Returns JSON with coupon details and calculated discount.
     */
    public function validateCoupon(Request $request)
    {
        $request->validate([
            'code'     => ['required', 'string', 'max:50'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'user_id'  => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $coupon = Coupon::where('code', strtoupper(trim($request->code)))->first();

        if (!$coupon) {
            return $this->couponError('Coupon code not found.');
        }

        if (!$coupon->isValid()) {
            return $this->couponError('This coupon is expired or inactive.');
        }

        $subtotal = (float) $request->subtotal;

        if ($coupon->min_order_amount !== null && $subtotal < $coupon->min_order_amount) {
            return $this->couponError(
                'Minimum order amount of ₹' . number_format($coupon->min_order_amount, 2) . ' required.'
            );
        }

        if ($coupon->max_order_amount !== null && $subtotal > $coupon->max_order_amount) {
            return $this->couponError(
                'This coupon is only valid for orders up to ₹' . number_format($coupon->max_order_amount, 2) . '.'
            );
        }

        // Per-user usage check
        if ($coupon->max_uses_per_user && $request->filled('user_id')) {
            // Assumes an order_coupon_usages or coupon_usages table; adjust relation as needed
            $userUses = $coupon->usages()->where('user_id', $request->user_id)->count();
            if ($userUses >= $coupon->max_uses_per_user) {
                return $this->couponError('You have already used this coupon the maximum number of times.');
            }
        }

        $discount = $coupon->calculateDiscount($subtotal);

        return response()->json([
            'valid'                  => true,
            'coupon_id'              => $coupon->id,
            'code'                   => $coupon->code,
            'description'            => $coupon->description,
            'discount_type'          => $coupon->discount_type,
            'discount_value'         => $coupon->discount_value,
            'max_discount_amount'    => $coupon->max_discount_amount,
            'applicable_products'    => $coupon->applicable_products  ?? [],
            'applicable_categories'  => $coupon->applicable_categories ?? [],
            'discount_amount'        => $discount,
            'message'                => 'Coupon applied! You save ₹' . number_format($discount, 2) . '.',
        ]);
    }

    /** Helper: return a consistent 422 error response. */
    private function couponError(string $message): \Illuminate\Http\JsonResponse
    {
        return response()->json(['valid' => false, 'message' => $message], 422);
    }
}