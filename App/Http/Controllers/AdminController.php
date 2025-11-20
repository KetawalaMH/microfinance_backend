<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\AdminServiceInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminController extends Controller
{
    protected $adminService;

    public function __construct(AdminServiceInterface $adminService)
    {
        $this->adminService = $adminService;

    }

    public function approveMemberRequest(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'member_id' => 'required|exists:members,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'data' => null
                ], 422);
            }

            $data = $request->all();
            $data['done_by'] = JWTAuth::user()->id;
            Log::info('1 lst log');

            $respons = $this->adminService->approveMemberRquest($data);
            return response()->json($respons, 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create member: ' . $e->getMessage(),
                'data' => null
            ], 500);

        }

    }
}
