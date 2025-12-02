<?php

namespace App\Services\Interfaces;

interface NotificationServiceInterface
{
    public function getUserNotifications($userId);

    public function sendNotification($userId, $title, $message, $type, $metaData);

    public function markAsRead($id);

    public function markAllAsRead($userId);

    public function deleteNotification($id);
}
