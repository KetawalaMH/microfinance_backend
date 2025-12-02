<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\NotificationServiceInterface;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationServiceInterface $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index($userId)
    {
        $userId = JWTAuth::user()->id;
        return response()->json(
            $this->notificationService->getUserNotifications($userId)
        );
    }

    public function store(Request $request)
    {
        return response()->json(
            $this->notificationService->sendNotification(
                $request->user_id,
                $request->title,
                $request->message,
                $request->type,
                $request->meta_data
            )
        );
    }

    public function markAsRead(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notification_id' => 'required|exists:notifications,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'data' => null
            ], 422);
        }

        $id = $request->notification_id;
        return response()->json(
            $this->notificationService->markAsRead($id)
        );
    }

    public function markAllRead($userId)
    {
        $userId = JWTAuth::user()->id;
        return response()->json(
            $this->notificationService->markAllAsRead($userId)
        );
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notification_id' => 'required|exists:notifications,id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'data' => null
            ], 422);
        }
        $id = $request->notification_id;
        return response()->json(
            $this->notificationService->deleteNotification($id)
        );
    }
}
