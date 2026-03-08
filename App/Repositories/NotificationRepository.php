<?php

namespace App\Repositories;

use App\Models\Notification;
use App\Repositories\Interfaces\NotificationRepositoryInterface;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function fetchUserNotifications($userId)
    {
        return Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function createNotification(array $data)
    {
        return Notification::create($data);
    }

    public function markAsRead($notificationId)
    {
        return Notification::where('id', $notificationId)
            ->update(['is_read' => true]);
    }

    public function markAllAsRead($userId)
    {
        return Notification::where('user_id', $userId)
            ->update(['is_read' => true]);
    }

    public function deleteNotification($id)
    {
        return Notification::destroy($id);
    }
}
