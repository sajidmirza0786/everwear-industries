<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()->whereNot('user_type', 'admin');

        if($request->filled('search')) {
            $searchTerm = $request->get('search');
            $users->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('mobile', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        $users = $users->orderByDesc('id')->paginate(30);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'local_password' => 'nullable|string|max:255',
            'country_code' => 'nullable|string|max:25',
            'state' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'zipcode' => 'nullable|string|max:25',
            'locality' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'ip_address' => 'nullable|ip',
            'user_type' => 'in:customer,employee',
            'status' => 'required|in:enable,disable',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            \DB::beginTransaction();

            $validated['uuid'] = (string) Str::uuid();
            $validated['password'] = Hash::make($validated['password']);
            $validated['local_password'] = $validated['password'];

            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $uniqueName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $validated['profile_picture'] = $file->storeAs('users', $uniqueName, 'public');
            }

            User::create($validated);

            \DB::commit();
            return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
        } catch (\Throwable $e) {
            \DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.create', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15|unique:users,mobile,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'local_password' => 'nullable|string|max:255',
            'country_code' => 'nullable|string|max:25',
            'state' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'zipcode' => 'nullable|string|max:25',
            'locality' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'ip_address' => 'nullable|ip',
            'user_type' => 'in:customer,employee',
            'status' => 'required|in:enable,disable',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            \DB::beginTransaction();

            if ($request->filled('password')) {
                $validated['password'] = Hash::make($request->password);
                $validated['local_password'] = $validated['password'];
            } else {
                unset($validated['password']);
            }

            if ($request->hasFile('profile_picture')) {
                if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                    Storage::disk('public')->delete($user->profile_picture);
                }

                $file = $request->file('profile_picture');
                $uniqueName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $validated['profile_picture'] = $file->storeAs('users', $uniqueName, 'public');
            }

            $user->update($validated);

            \DB::commit();
            return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
        } catch (\Throwable $e) {
            \DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }


    public function destroy(User $user)
    {
        try {
            $user->delete();
            return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
