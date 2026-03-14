<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\FactoryInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class FactoryInviteController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $token = $request->query('token');
        if (! $token) {
            return redirect()->route('login')->with('error', 'Invalid or missing invitation link.');
        }

        $invitation = FactoryInvitation::where('token', $token)->first();
        if (! $invitation || $invitation->isExpired()) {
            return redirect()->route('login')->with('error', 'This invitation has expired or is invalid.');
        }

        return view('auth.factory-invite-accept', ['invitation' => $invitation]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string', 'exists:factory_invitations,token'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $invitation = FactoryInvitation::where('token', $request->token)->first();
        if (! $invitation || $invitation->isExpired()) {
            return back()->withErrors(['token' => 'This invitation has expired or is invalid.'])->withInput();
        }

        $user = $invitation->user;
        $user->update(['password' => Hash::make($request->password)]);
        $invitation->update(['status' => 'accepted']);

        Auth::login($user);

        return redirect()->route('pending-approval')->with('success', 'Welcome to HANZO! Your account is pending admin verification. Complete your profile while you wait.');
    }
}
