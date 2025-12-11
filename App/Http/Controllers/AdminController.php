<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\AdminServiceInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
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

    public function approveLoanRequest(Request $request)
    {
        try {
            $valodator = Validator::make($request->all(), [
                'loan_id' => 'required|exists:loans,id',
            ]);
            if ($valodator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $valodator->errors()->first(),
                    'data' => null
                ], 422);
            }
            $data = $request->all();
            $data['approved_by'] = JWTAuth::user()->id;
            $result = $this->adminService->approveLoanRequest($data);
            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 422);
        }
    }
    public function rejectLoanRequest(Request $request)
    {
        try {
            $valodator = Validator::make($request->all(), [
                'loan_id' => 'required|exists:loans,id',
            ]);
            if ($valodator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $valodator->errors()->first(),
                    'data' => null
                ], 422);
            }
            $data = $request->all();
            $data['approved_by'] = JWTAuth::user()->id;
            $result = $this->adminService->rejectLoanRequest($data);
            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 422);
        }
    }
    public function approveSavingAccount(Request $request)
    {
        try {
            $valodator = Validator::make($request->all(), [
                'account_id' => 'required|exists:saving_accounts,id',
            ]);
            if ($valodator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $valodator->errors()->first(),
                    'data' => null
                ], 422);
            }
            $data = $request->all();
            $data['approved_by'] = JWTAuth::user()->id;
            $result = $this->adminService->approveSavingAccount($data);
            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 422);
        }
    }
    public function rejectSavingAccount(Request $request)
    {
        try {
            $valodator = Validator::make($request->all(), [
                'account_id' => 'required|exists:saving_accounts,id',
            ]);
            if ($valodator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $valodator->errors()->first(),
                    'data' => null
                ], 422);
            }
            $data = $request->all();
            $data['approved_by'] = JWTAuth::user()->id;
            $result = $this->adminService->rejectSavingAccount($data);
            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 422);
        }
    }
}
