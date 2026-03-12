<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();
        $user->fill(collect($validated)->except('avatar')->all());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Always apply avatar when the field was submitted (profile form includes avatar radios)
        if (array_key_exists('avatar', $validated)) {
            $av = $validated['avatar'];
            $presetKeys = array_map('strval', array_keys(config('avatars.presets', [])));
            $user->avatar_path = in_array($av, ['', 'initials', null], true) ? null : (in_array((string) $av, $presetKeys, true) ? (string) $av : $user->avatar_path);
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }
}
