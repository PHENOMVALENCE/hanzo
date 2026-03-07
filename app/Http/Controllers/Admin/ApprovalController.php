<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
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
        $users = User::role($role)->where('status', 'pending')->get();

        return view('admin.approvals.index', compact('users', 'type'));
    }

    public function approve(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'approved']);

        return back()->with('success', 'User approved successfully.');
    }

    public function reject(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $user->update([
            'status' => 'suspended',
            'approval_message' => $request->input('message'),
        ]);

        return back()->with('success', 'Application rejected.');
    }
}
