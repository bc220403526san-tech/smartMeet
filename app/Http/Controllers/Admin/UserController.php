<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users        = User::latest()->paginate(6);
        $totalUsers   = User::count();
        $activeUsers  = User::where('is_active', 1)->count();
        $inactiveUsers = User::where('is_active', 0)->count();
        return view('admin.users.index', compact('users', 'totalUsers', 'activeUsers', 'inactiveUsers'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role'     => 'required|in:admin,organizer,participant',
            'image' => 'nullable|mimes:jpg,jpeg,png,webp,avif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('user_images', 'public');
        }

        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'is_active' => $request->boolean('is_active'),
            'image'     => $imagePath,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->image_url = $user->image
            ? (str_starts_with($user->image, 'http')
                ? $user->image
                : Storage::url($user->image))
            : asset('images/default-avatar.png');

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|in:admin,organizer,participant',
            'image' => 'nullable|mimes:jpg,jpeg,png,webp,avif|max:2048',
        ]);

        $data = [
            'name'      => $request->name,
            'email'     => $request->email,
            'role'      => $request->role,
            'is_active' => $request->boolean('is_active'),
        ];

        // IMAGE REMOVE
        if ($request->remove_image) {
            if ($user->image && !str_starts_with($user->image, 'http')) {
                Storage::disk('public')->delete($user->image);
            }
            $data['image'] = null;
        }

        // IMAGE UPDATE
        if ($request->hasFile('image')) {
            if ($user->image && !str_starts_with($user->image, 'http')) {
                Storage::disk('public')->delete($user->image);
            }
            $data['image'] = $request->file('image')->store('user_images', 'public');
        }

        $user->update($data);

        return back()->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Check — admin apna aap ko delete na kare
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        if ($user->image && !str_starts_with($user->image, 'http')) {
            Storage::disk('public')->delete($user->image);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User removed successfully!');
    }

    // UserController mein
    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account!');
        }

        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', 'User status updated.');
    }
}
