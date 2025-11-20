<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\MemberServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Log;

class MemberController extends Controller
{
    protected $memberService;

    public function __construct(MemberServiceInterface $memberService)
    {
        $this->memberService = $memberService;
    }

    public function createMember(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'member_type_id' => 'required|exists:member_types,id',
            'full_name' => 'required|string|min:2|max:255',
            'email_address' => 'email|max:255|unique:members,email_address',
            'mobile_number' => 'required|string|min:10|max:15|unique:members,mobile_number',
            'address' => 'string|max:500',
            'dob' => 'date',
            'occupation' => 'string|max:100',
            'NIC' => 'required|string|unique:members,NIC|min:10|max: 13',
            'is_active' => 'boolean'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'data' => null
            ], 422);
        }

        try {
            $data = json_decode($request->getContent(), true);
            $member = $this->memberService->createMember($data);

            if ($member['success'] === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create member: ' . $member['message'],
                    'data' => null
                ], 500);
            }
            return response()->json([
                'success' => true,
                'message' => 'Member created successfully',
                'data' => $member['data']
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create member: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }

    }
    public function getALlMembers(Request $request): JsonResponse
    {
        try {
            $members = $this->memberService->getALlMembers();

            if ($members['success'] === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch members: ' . $members['message'],
                    'data' => null
                ], 500);
            }
            return response()->json([
                'success' => true,
                'message' => 'Members fetched successfully',
                'data' => $members['data']
            ], 201);

        } catch (\Exception $e) {
            Log::info("Hikuna 2");
            return response()->json([
                'success' => false,
                'message' => 'Failed to create member: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }

    }
}
