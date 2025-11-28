<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\MemberRepositoryInterface;
use App\Services\Interfaces\MemberServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class MemberService implements MemberServiceInterface
{
    protected $memberRepository;
    protected $userService;

    public function __construct(MemberRepositoryInterface $memberRepository, UserServiceInterface $userService)
    {
        $this->memberRepository = $memberRepository;
        $this->userService = $userService;
    }

    public function createMember(array $data)
    {
        // Handle business logic here
        $member = $this->memberRepository->create($data);
        if ($member['success'] === false) {
            return $member;
        }


        //create user profile for member portal
        $member = $member['data'];
        $userData = [
            'full_name' => $member->full_name,
            'email_address' => $member->email_address,
            'password' => $member->NIC
        ];
        $user = $this->userService->userSignUp($userData);
        if ($user['success'] === false) {
            return $user;
        }

        return $member;
    }

    public function getAllMembers()
    {
        $members = $this->memberRepository->getAllMembers();

        return $members;
    }

    public function getMemberById($id)
    {
        return $this->memberRepository->getMemberById($id);
    }

    public function updateMember(array $data)
    {
        try {
            $respose = $this->memberRepository->updateMember($data);
            return $respose;
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }

    public function getMemberDetails($id)
    {
        try {
            $member = $this->memberRepository->getMemberById($id);
            if (!$member['success']) {
                return [
                    'success' => false,
                    'message' => 'Member not found',
                    'data' => null
                ];
            }

            $member = $member['data'];
            $memberData = [
                'member_id' => $member->id,
                'full_name' => $member->full_name,
                'email_address' => $member->email_address,
                'mobile_number' => $member->mobile_number,
                'address' => $member->address,
                'role' => 'member',
                'last_login' => Carbon::now(),
                'status' => $member->status,
                'member_scince' => $member->created_at
            ];
            return [
                'success' => true,
                'message' => 'Member fetched successfully',
                'data' => $memberData
            ];
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }
}
