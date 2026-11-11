<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationWebController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notifications = Notification::with('shipment')
            ->where('user_id', $user->id)
            ->latest('id')
            ->paginate(15);

        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markRead($id)
    {
        $user = Auth::user();
        $notification = Notification::findOrFail($id);

        if ($notification->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        if (!$notification->is_read) {
            $notification->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        return back()->with('success', __('ui.flash.notif_read'));
    }

    public function markAllRead()
    {
        $user = Auth::user();
        Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return back()->with('success', __('ui.flash.notif_all_read'));
    }
}

