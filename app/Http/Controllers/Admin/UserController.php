<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeUserMail;
use App\Models\Factory;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('roles')->latest()->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['buyer', 'factory', 'admin'])],
            'status' => ['nullable', Rule::in(['pending', 'approved', 'suspended'])],
            'company_name' => ['nullable', 'string', 'max:255'],
            'factory_name' => ['nullable', 'string', 'max:255'],
            'location_china' => ['nullable', 'string', 'max:150'],
            'contact_wechat' => ['nullable', 'string', 'max:100'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'factory_notes' => ['nullable', 'string', 'max:2000'],
            'phone' => ['nullable', 'string', 'max:30'],
            'country' => ['nullable', 'string', 'max:80'],
            'city' => ['nullable', 'string', 'max:100'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'send_welcome_email' => ['nullable', 'boolean'],
        ]);

        $sendWelcomeEmail = $request->boolean('send_welcome_email');
        $plainPassword = $validated['password'];

        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = $validated['status'] ?? 'approved';
        $validated['email_verified_at'] = now();

        if ($request->hasFile('avatar')) {
            $validated['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
        }
        unset($validated['avatar']);

        $user = User::create($validated);
        $user->assignRole($validated['role']);

        if ($validated['role'] === 'factory') {
            Factory::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'factory_name' => $validated['factory_name'] ?? $validated['company_name'] ?? $user->name,
                    'location_china' => $validated['location_china'] ?? null,
                    'contact_wechat' => $request->input('contact_wechat'),
                    'contact_phone' => $request->input('contact_phone'),
                    'notes' => $request->input('factory_notes'),
                    'verification_status' => 'pending',
                ]
            );
        }

        if ($sendWelcomeEmail) {
            Mail::to($user->email)->send(new WelcomeUserMail($user, $plainPassword));
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.' . ($sendWelcomeEmail ? ' Welcome email sent.' : ''));
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['buyer', 'factory', 'admin'])],
            'status' => ['required', Rule::in(['pending', 'approved', 'suspended'])],
            'company_name' => ['nullable', 'string', 'max:255'],
            'factory_name' => ['nullable', 'string', 'max:255'],
            'location_china' => ['nullable', 'string', 'max:150'],
            'contact_wechat' => ['nullable', 'string', 'max:100'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'factory_notes' => ['nullable', 'string', 'max:2000'],
            'phone' => ['nullable', 'string', 'max:30'],
            'country' => ['nullable', 'string', 'max:80'],
            'city' => ['nullable', 'string', 'max:100'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $user->fill($request->only(['name', 'email', 'status', 'company_name', 'phone', 'country', 'city']));

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $user->avatar_path = $request->file('avatar')->store('avatars', 'public');
        }

        $user->save();
        $user->syncRoles([$validated['role']]);

        if ($validated['role'] === 'factory') {
            $factory = Factory::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'factory_name' => $validated['factory_name'] ?? $validated['company_name'] ?? $user->name,
                    'location_china' => $validated['location_china'] ?? null,
                    'verification_status' => 'pending',
                ]
            );
            $factory->update([
                'factory_name' => $validated['factory_name'] ?? $validated['company_name'] ?? $factory->factory_name,
                'location_china' => $validated['location_china'] ?? $factory->location_china,
                'contact_wechat' => $request->input('contact_wechat'),
                'contact_phone' => $request->input('contact_phone'),
                'notes' => $request->input('factory_notes'),
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account.');
        }

        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
