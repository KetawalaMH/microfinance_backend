<?php

namespace App\Services;

use App\Repositories\Interfaces\NotificationRepositoryInterface;
use App\Services\Interfaces\NotificationServiceInterface;
use Exception;

class NotificationService implements NotificationServiceInterface
{
    protected $notificationRepo;

    public function __construct(NotificationRepositoryInterface $notificationRepo)
    {
        $this->notificationRepo = $notificationRepo;
    }

    public function getUserNotifications($userId)
    {
        return [
            'success' => true,
            'message' => 'Notifications fetched successfully.',
            'data' => $this->notificationRepo->fetchUserNotifications($userId)
        ];
    }

    public function sendNotification($userId, $title, $message, $type = null, $metaData = null)
    {
        try {
            $data = [
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'meta_data' => $metaData,
            ];

            $notification = $this->notificationRepo->createNotification($data);

            return [
                'success' => true,
                'message' => 'Notification sent successfully.',
                'data' => $notification
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function markAsRead($id)
    {
        $this->notificationRepo->markAsRead($id);

        return [
            'success' => true,
            'message' => 'Notification marked as read.'
        ];
    }

    public function markAllAsRead($userId)
    {
        $this->notificationRepo->markAllAsRead($userId);

        return [
            'success' => true,
            'message' => 'All notifications marked as read.'
        ];
    }

    public function deleteNotification($id)
    {
        $this->notificationRepo->deleteNotification($id);

        return [
            'success' => true,
            'message' => 'Notification deleted.'
        ];
    }
}
