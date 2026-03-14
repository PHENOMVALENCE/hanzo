<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BuyerApprovedMail;
use App\Mail\BuyerRejectedMail;
use App\Mail\BuyerRequestMoreInfoMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ApprovalController extends Controller
{
    public function buyers(): View
    {
        return $this->index('buyers');
    }

    public function factories(): View
    {
        return $this->index('factories');
    }

    protected function index(string $type): View|RedirectResponse
    {
        if (! in_array($type, ['buyers', 'factories'])) {
            return redirect()->route('admin.approvals.buyers');
        }

        $role = $type === 'buyers' ? 'buyer' : 'factory';
        $users = User::role($role)->where('status', 'pending')->latest()->get();

        return view('admin.approvals.index', compact('users', 'type'));
    }

    public function approve(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'approved']);

        if ($user->hasRole('buyer')) {
            try {
                Mail::to($user->email)->send(new BuyerApprovedMail($user));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Buyer approval email failed: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'User approved successfully.');
    }

    public function reject(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $message = $request->input('message', '');
        $user->update([
            'status' => 'suspended',
            'approval_message' => $message,
        ]);

        if ($user->hasRole('buyer')) {
            try {
                Mail::to($user->email)->send(new BuyerRejectedMail($user));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Buyer rejection email failed: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Application rejected.');
    }

    public function requestMoreInfo(Request $request, int $id): RedirectResponse
    {
        $request->validate(['message' => ['required', 'string', 'max:2000']]);

        $user = User::findOrFail($id);
        $user->update(['admin_requested_info' => $request->input('message')]);

        if ($user->hasRole('buyer')) {
            try {
                Mail::to($user->email)->send(new BuyerRequestMoreInfoMail($user, $request->input('message')));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Buyer request-more-info email failed: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Request for more information sent to the user.');
    }
}
