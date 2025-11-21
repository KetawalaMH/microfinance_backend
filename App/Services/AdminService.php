<?php

namespace App\Services;

use App\Models\Admin;
use App\Repositories\Interfaces\AdminRepositoryInterface;
use App\Services\Interfaces\MemberServiceInterface;
use Exception;
use Log;

class AdminService implements Interfaces\AdminServiceInterface
{
    private $adminRepository;
    private $memberService;

    public function __construct(AdminRepositoryInterface $adminRepository, MemberServiceInterface $memberService)
    {
        $this->adminRepository = $adminRepository;
        $this->memberService = $memberService;
    }

    protected function logError($url, $error_message)
    {
        Log::error('Error in setting controller function', [
            'url' => $url,
            'error' => $error_message
        ]);
    }

    public function approveMemberRquest(array $data)
    {
        try {
            $member = $this->memberService->getMemberById($data['member_id']);
            if (!$member['success']) {
                return $member;
            }

            Log::info('Member requt approved');

            if ($member['data']['status'] != 'pending') {
                return [
                    'success' => false,
                    'messag' => 'Member is not in pending status',
                    'data' => null
                ];
            }

            //update member 
            $updatedMember = $this->memberService->updateMember(['id' => $data['member_id'], 'status' => 'active']);
            if (!$updatedMember['success']) {
                return $updatedMember;
            }

            $logData = [
                'done_by' => $data['done_by'],
                'title' => 'Member request approved',
                'description' => 'Member request approved by admin',
                'member_id' => $data['member_id'],
            ];

            Log::info('Member requt approved 56');

            //add action log
            $log = $this->adminRepository->approveMemberRequest($logData);

            Log::info('Member requt approved 700');
            Log::info($log);
            if (!$log['success']) {
                return $log;
            }
            Log::info('Member requt approved 60');
            return [
                'success' => true,
                'message' => 'Member request approved successfully',
                'data' => null
            ];


        } catch (Exception $e) {
            $this->logError("transactions", $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => "Something went wrong: " . $e->getMessage()
            ], 500);


        }

    }
}