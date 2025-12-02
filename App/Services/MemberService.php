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

    public function getMemberStats()
    {
        try {
            // Fetch members
            $members = $this->memberRepository->getAllActiveMembers();

            if (!$members || $members->count() === 0) {
                return [
                    'success' => false,
                    'message' => 'Active members not found.',
                    'data' => null
                ];
            }

            // Total active members
            $totalActiveMembers = $members->count();

            // Current & last month
            $currentMonth = now()->month;
            $lastMonth = now()->subMonth()->month;

            // Current month new members
            $currentMonthNew = $members->filter(function ($member) use ($currentMonth) {
                return Carbon::parse($member->created_at)->month == $currentMonth;
            })->count();

            // Last month new members
            $lastMonthNew = $members->filter(function ($member) use ($lastMonth) {
                return Carbon::parse($member->created_at)->month == $lastMonth;
            })->count();

            // Growth rate calculation
            if ($lastMonthNew == 0) {
                $growthRate = $currentMonthNew > 0 ? 100 : 0;
            } else {
                $growthRate = (($currentMonthNew - $lastMonthNew) / $lastMonthNew) * 100;
            }

            return [
                'success' => true,
                'message' => 'Member statistics calculated successfully.',
                'data' => [
                    'total_active_members' => $totalActiveMembers,
                    'current_month_new_members' => $currentMonthNew,
                    'last_month_new_members' => $lastMonthNew,
                    'growth_rate' => round($growthRate, 2),
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }
}
