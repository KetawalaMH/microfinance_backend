<?php

namespace App\Repositories\Interfaces;

interface NotificationRepositoryInterface
{
    public function fetchUserNotifications($userId);

    public function createNotification(array $data);

    public function markAsRead($notificationId);

    public function markAllAsRead($userId);

    public function deleteNotification($id);
}
