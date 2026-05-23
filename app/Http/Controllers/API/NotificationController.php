<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->limit(20)
            ->get();

        return response()->json($notifications);
    }

    public function markRead(int $id): JsonResponse
    {
        $notification = \App\Models\Notification::findOrFail($id);
        $notification->markAsRead();
        return response()->json(['ok' => true]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $request->user()->notifications()->unread()->update(['is_read' => true, 'read_at' => now()]);
        return response()->json(['ok' => true]);
    }
}
