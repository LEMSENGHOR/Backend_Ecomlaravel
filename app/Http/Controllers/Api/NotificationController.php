<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Get all notifications
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->latest('created_at')
            ->paginate(20);

        return response()->json([
            'message' => 'Notifications retrieved successfully.',
            'notifications' => $notifications,
        ]);
    }

    // Get one notification
    public function show(
        Request $request,
        UserNotification $notification
    ) {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], 403);
        }

        return response()->json([
            'message' => 'Notification retrieved successfully.',
            'notification' => $notification,
        ]);
    }

    // Mark one as read
    public function markAsRead(
        Request $request,
        UserNotification $notification
    ) {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], 403);
        }

        $notification->update([
            'is_read' => true,
        ]);

        return response()->json([
            'message' => 'Notification marked as read.',
            'notification' => $notification,
        ]);
    }

    // Mark all as read
    public function markAllAsRead(Request $request)
    {
        $request->user()
            ->notifications()
            ->where('is_read', false)
            ->update([
                'is_read' => true,
            ]);

        return response()->json([
            'message' => 'All notifications marked as read.',
        ]);
    }

    // Delete notification
    public function destroy(
        Request $request,
        UserNotification $notification
    ) {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], 403);
        }

        $notification->delete();

        return response()->json([
            'message' => 'Notification deleted successfully.',
        ]);
    }
}