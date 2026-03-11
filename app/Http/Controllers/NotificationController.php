<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()->limit(20)->get();

        if ($request->wantsJson()) {
            return response()->json($notifications);
        }

        return view('notifications.index', ['notifications' => $notifications]);
    }

    public function markAsRead(Request $request, string $id)
    {
        $request->user()->notifications()->where('id', $id)->first()?->markAsRead();

        return $request->wantsJson()
            ? response()->json(['ok' => true])
            : back();
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return $request->wantsJson()
            ? response()->json(['ok' => true])
            : back();
    }
}
