<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\FactoryInvitationMail;
use App\Models\Factory;
use App\Models\FactoryInvitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InviteFactoryController extends Controller
{
    public function create(): View
    {
        return view('admin.invite-factory.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'factory_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'location_china' => ['nullable', 'string', 'max:200'],
            'product_categories' => ['nullable', 'string', 'max:500'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $tempPassword = Str::random(16);
        $user = User::create([
            'name' => $validated['contact_name'],
            'email' => $validated['email'],
            'phone' => $validated['contact_phone'] ?? null,
            'password' => Hash::make($tempPassword),
            'status' => 'pending',
            'email_verified_at' => now(),
        ]);
        $user->assignRole('factory');

        $factory = Factory::create([
            'user_id' => $user->id,
            'factory_name' => $validated['factory_name'],
            'location_china' => $validated['location_china'] ?? null,
            'categories' => $validated['product_categories'] ? array_map('trim', explode(',', $validated['product_categories'])) : [],
            'notes' => $validated['admin_notes'] ?? null,
            'verification_status' => 'pending',
        ]);

        $invitation = FactoryInvitation::create([
            'email' => $user->email,
            'token' => FactoryInvitation::createToken(),
            'expires_at' => now()->addHours(72),
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        try {
            Mail::to($user->email)->send(new FactoryInvitationMail($user, $invitation));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Factory invitation email failed: ' . $e->getMessage());
        }

        return redirect()->route('admin.users.index', ['role' => 'factory'])
            ->with('success', 'Factory invited. Invitation email sent (expires in 72 hours).');
    }
}
