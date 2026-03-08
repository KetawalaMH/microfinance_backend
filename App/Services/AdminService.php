<?php

namespace App\Services;

use App\Models\Admin;
use App\Repositories\Interfaces\AdminRepositoryInterface;
use App\Services\Interfaces\LoanServiceInterface;
use App\Services\Interfaces\MemberServiceInterface;
use App\Services\Interfaces\SavingServiceInterface;
use Exception;
use Illuminate\Support\Facades\Log;

class AdminService implements Interfaces\AdminServiceInterface
{
    private $adminRepository;
    private $memberService;

    private $loanService;

    private $savingService;

    public function __construct(
        AdminRepositoryInterface $adminRepository,
        MemberServiceInterface $memberService,
        LoanServiceInterface $loanService,
        SavingServiceInterface $savingService
    ) {
        $this->adminRepository = $adminRepository;
        $this->memberService = $memberService;
        $this->loanService = $loanService;
        $this->savingService = $savingService;
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

            //add action log
            $log = $this->adminRepository->logAction($logData);
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

    public function rejectMemberRquest(array $data)
    {
        try {
            $member = $this->memberService->getMemberById($data['member_id']);
            if (!$member['success']) {
                return $member;
            }

            if ($member['data']['status'] != 'pending') {
                return [
                    'success' => false,
                    'messag' => 'Member is not in pending status',
                    'data' => null
                ];
            }

            //update member 
            $updatedMember = $this->memberService->updateMember(['id' => $data['member_id'], 'status' => 'inactive']);
            if (!$updatedMember['success']) {
                return $updatedMember;
            }

            $logData = [
                'done_by' => $data['done_by'],
                'title' => 'Member request rejected',
                'description' => 'Member request rejected by admin',
                'member_id' => $data['member_id'],
            ];

            //add action log
            $log = $this->adminRepository->logAction($logData);
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

    public function approveLoanRequest(array $data)
    {
        try {
            $loanUpdate = $this->loanService->approveLoanRequest($data);
            if (!$loanUpdate['success']) {
                Log::info($loanUpdate['message']);
                return $loanUpdate;
            }
            $logData = [
                'done_by' => $data['approved_by'],
                'title' => 'Loan request approved',
                'description' => 'Loan request approved by admin',
                'loan_id' => $data['loan_id'],
            ];
            $log = $this->adminRepository->logAction($logData);
            return $log;
        } catch (Exception $e) {
            $this->logError("transactions", $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => "Something went wrong: " . $e->getMessage()
            ], 500);
        }
    }

    public function logAction(array $data)
    {
        return $this->adminRepository->logAction($data);
    }

    public function rejectLoanRequest(array $data)
    {
        try {
            $loanUpdate = $this->loanService->rejectLoanRequest($data);
            if (!$loanUpdate['success']) {
                Log::info($loanUpdate['message']);
                return $loanUpdate;
            }
            $logData = [
                'done_by' => $data['approved_by'],
                'title' => 'Loan request rejected',
                'description' => 'Loan request rejected by admin',
                'loan_id' => $data['loan_id'],
            ];
            $log = $this->adminRepository->logAction($logData);
            return $log;
        } catch (Exception $e) {
            $this->logError("transactions", $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => "Something went wrong: " . $e->getMessage()
            ], 500);
        }
    }
    public function approveSavingAccount(array $data)
    {
        try {
            $accountUpdate = $this->savingService->approveSavingAccount($data);
            if (!$accountUpdate['success']) {
                Log::info($accountUpdate['message']);
                return $accountUpdate;
            }
            $logData = [
                'done_by' => $data['approved_by'],
                'title' => 'Saving account approved',
                'description' => 'Saving Account approved by admin',
                'account_id' => $data['account_id'],
            ];
            $log = $this->adminRepository->logAction($logData);
            return $log;
        } catch (Exception $e) {
            $this->logError("transactions", $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => "Something went wrong: " . $e->getMessage()
            ], 500);
        }
    }
    public function rejectSavingAccount(array $data)
    {
        try {
            $loanUpdate = $this->savingService->rejectSavingAccount($data);
            if (!$loanUpdate['success']) {
                Log::info($loanUpdate['message']);
                return $loanUpdate;
            }
            $logData = [
                'done_by' => $data['approved_by'],
                'title' => 'Saving account rejected',
                'description' => 'Saving account was rejected by admin',
                'account_id' => $data['account_id'],
            ];
            $log = $this->adminRepository->logAction($logData);
            return $log;
        } catch (Exception $e) {
            $this->logError("transactions", $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => "Something went wrong: " . $e->getMessage()
            ], 500);
        }
    }
}
