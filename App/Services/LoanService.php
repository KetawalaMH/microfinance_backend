<?php

namespace App\Services;

use App\Models\Loan;
use App\Repositories\Interfaces\LoanRepositoryInterface;
use App\Services\Interfaces\LoanServiceInterface;
use App\Services\Interfaces\MemberServiceInterface;
use App\Services\Interfaces\SettingServiceInterface;
use App\Services\Interfaces\TransactionServiceInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class LoanService implements LoanServiceInterface
{
    private $loanRepository;
    private $memberService;
    private $transactionService;

    public function __construct(LoanRepositoryInterface $loanRepository, MemberServiceInterface $memberService, TransactionServiceInterface $transactionService)
    {
        $this->loanRepository = $loanRepository;
        $this->memberService = $memberService;
        $this->transactionService = $transactionService;
    }

    public function createLoanRequest($data)
    {
        try {
            $member = $this->memberService->getMemberById($data['borrower_id']);
            if (!$member['success']) {
                return $member;
            }

            $member = $member['data'];

            if ($member['status'] != 'active') {
                $memberStatus = $member['status'];
                return [
                    'success' => false,
                    'message' => 'Member is not in active status',
                    'data' => null
                ];
            }

            $loanType = $this->loanRepository->getLoanTypeById($data['loan_type_id']);
            if (!$loanType['success']) {
                return $loanType;
            }

            $loanType = $loanType['data'];

            if ($loanType->max_amount < $data['amount']) {
                return [
                    'success' => false,
                    'message' => 'Loan amount is greater than max amount',
                    'data' => null
                ];
            }

            $loanData = [
                'amount' => $data['amount'],
                'loan_type_id' => $data['loan_type_id'],
                'borrower_id' => $data['borrower_id'],
                'branch_id' => $data['branch_id'],
                'purpose' => $data['purpose'],
                'start_date' => Carbon::now(),
                'number_of_installments' => $loanType->max_installments,
                'installment_amount' => $this->calculateInstallment($data['amount'], $loanType->max_interest_rate, $loanType->max_installments),
                'submitted_by' => $data['submitted_by'],
                'remaining_installments' => $loanType->max_installments,
                'paid_amount' => 0,
                'remaining_amount' => $data['amount'],
                'duration' => $loanType->duration,
                'interest' => $loanType->max_interest_rate,
                'status' => 'pending'
            ];

            $loan = $this->loanRepository->createLoanRequest($loanData);

            return $loan;
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function saveLoanDocuments($data)
    {
        try {
            $loan = $this->loanRepository->saveLoanDocuments($data);
            return $loan;
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function addGaurantors($data)
    {
        try {
            //validate gaurators existance and status
            $gaurantor1 = $this->memberService->getMemberById($data['guarantor1_id']);
            if (!$gaurantor1['success']) {
                return $gaurantor1;
            }
            if ($gaurantor1['data']['status'] != 'active') {
                return [
                    'success' => false,
                    'message' => 'Gaurantor 1 is not active',
                    'data' => null
                ];
            }
            $gaurantor2 = $this->memberService->getMemberById($data['guarantor2_id']);
            if (!$gaurantor2['success']) {
                return $gaurantor2;
            }
            if ($gaurantor2['data']['status'] != 'active') {
                return [
                    'success' => false,
                    'message' => 'Gaurantor 2 is not active',
                    'data' => null
                ];
            }

            //validate gaurentor eligibility
            $gaurantor1 = $gaurantor1['data'];
            $gaurantor2 = $gaurantor2['data'];

            $gaurantor1DueLoan = $this->loanRepository->getMemberDueLoans($gaurantor1->id);
            if (!$gaurantor1DueLoan['success']) {
                return $gaurantor1DueLoan;
            }

            if ($gaurantor1DueLoan['data']->count() > 0) {
                return [
                    'success' => false,
                    'message' => 'Guarantor 1 has due loans',
                    'data' => null
                ];
            }

            $gaurantor2DueLoan = $this->loanRepository->getMemberDueLoans($gaurantor2->id);
            if (!$gaurantor2DueLoan['success']) {
                return $gaurantor2DueLoan;
            }

            if ($gaurantor2DueLoan['data']->count() > 0) {
                return [
                    'success' => false,
                    'message' => 'Gaurantor 2 has due loans',
                    'data' => null
                ];
            }

            //add gaurantors
            $output = $this->loanRepository->addGaurantors($data);
            $loan = $output['data'];
            Log::info($loan);
            $loan = [
                'loan_id' => $loan->id,
                'borrower' => $loan->borrower->full_name,
                'amount' => $loan->amount,
                'purpose' => $loan->purpose,
                'guarentors' => [
                    'guarantor1' => $loan->gurantor1->full_name,
                    'guarantor2' => $loan->gurantor2->full_name,
                ]
            ];
            return [
                'success' => true,
                'message' => 'Gaurantors added successfully.',
                'data' => $loan
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function submitLoanApplication($data)
    {
        return $this->loanRepository->submitLoanApplication($data);
    }

    public function addInstallments($data)
    {
        try {

            $loan = $this->loanRepository->getLoanById($data['loan_id']);
            if (!$loan['success']) {
                return $loan;
            }
            $loan = $loan['data'];

            if ($loan->status == 'paid') {
                return [
                    'success' => false,
                    'message' => 'Loan is already paid',
                    'data' => null
                ];
            }

            $currentDueDate = Carbon::parse($loan->next_installment_date);
            $nextDueDate = $currentDueDate->copy()->addMonth();


            if ($nextDueDate->isBefore(Carbon::now())) {
                $loan['status'] = 'due';
            } else {
                $loan['status'] = 'approved';
            }

            Log::info('loan update');
            Log::info($loan->installment_amount);

            $loan['last_installment_date'] = Carbon::now();
            $loan['next_installment_date'] = $nextDueDate;
            $loan['remaining_installments'] = $loan->remaining_installments - 1;
            $paidInstallments = $loan->total_installments - $loan->remaining_installments;
            $loan->remaining_amount = $this->remainingLoanBalance($loan->amount, $loan->loanType->max_interest_rate, $loan->total_installments, $paidInstallments);
            $loan->paid_amount += $loan->installment_amount;
            $loan->save();

            $installmentRecordData = [
                'loan_id' => $loan->id,
                'amount' => $loan->installment_amount,
                'paid_date' => Carbon::now(),
                'month' => $paidInstallments,
                'due_date' => $currentDueDate,
                'recorded_by' => $data['recorded_by']
            ];
            $output = $this->transactionService->addInstallments($installmentRecordData);

            return $output;
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function getAllLoanApplications($data)
    {
        try {
            $loans = $this->loanRepository->getAllLoanApplications($data);
            if (!$loans['success']) {
                return $loans;
            }
            $loans = $loans['data'];
            if ($loans->isEmpty()) {
                return [
                    'success' => true,
                    'message' => 'No loan applications found.',
                    'data' => []
                ];
            }

            $formattedLoanData = $loans->map(function ($loan) {
                return [
                    'loan_id' => $loan->id,
                    'borrower_name' => $loan->borrower->name ?? null,
                    'amount' => $loan->amount,
                    'category' => $loan->category,
                    'status' => $loan->status,
                ];
            });

            return [
                'success' => true,
                'message' => 'Loan fetched successfully.',
                'data' => $formattedLoanData
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function getLoanDetails($data)
    {
        try {
            $loan = $this->loanRepository->getLoanById($data['loan_id']);
            if (!$loan['success']) {
                return $loan;
            }
            $loan = $loan['data'];

            $loanInfomation = [
                'borrower' => $loan->borrower->full_name,
                'email_addres' => $loan->borrower->email_addres,
                'amount' => $loan->amount,
                'interest_rate' => $loan->loanType->max_interest_rate,
                'loan_catogery' => $loan->loanType->loan_type,
                'duration' => $loan->duration,
                'due_date' => $loan->next_installment_date,
                'status' => $loan->status,
            ];

            $loanSummery = [
                'amount' => $loan->amount,
                'remaining_balance' => $loan->remaining_amount,
                'monthly_payment' => $loan->installment_amount,
                'interet_rate' => $loan->loanType->max_interest_rate,
                'progress' => $this->calculateProgress($loan->amount, $loan->remaining_amount)
            ];

            $paymentData = $this->transactionService->getpaymentData($data['loan_id']);
            $repaymentSchedule = [];

            if ($paymentData->isNotEmpty()) {
                foreach ($paymentData as $payment) {
                    $repaymentSchedule[] = [
                        'amount' => $payment->amount,
                        'paid_date' => $payment->paid_date,
                        'month' => $payment->month,
                        'due_date' => $payment->due_date,
                        'status' => 'paid'
                    ];
                }
            }

            // Determine status of next installment
            $status = $loan->next_installment_date < Carbon::now()
                ? 'due'
                : 'pending';

            // Add upcoming installment
            $repaymentSchedule[] = [
                'amount' => $loan->installment_amount,
                'paid_date' => null,
                'month' => $loan->total_installments - $loan->remaining_installments + 1,
                'due_date' => $loan->next_installment_date,
                'status' => $status
            ];

            return [
                'success' => true,
                'message' => 'Loan fetched successfully.',
                'data' => [
                    'loan_information' => $loanInfomation,
                    'loan_summery' => $loanSummery,
                    'repayment_schedule' => $repaymentSchedule
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

    public function approveLoanRequest($data)
    {
        try {
            $loan = $this->loanRepository->getLoanById($data['loan_id']);
            if (!$loan['success']) {
                return $loan;
            }
            $loan = $loan['data'];

            $loan->status = 'approved';
            $loan->approved_by = $data['approved_by'];
            $loan->next_installment_date = Carbon::now()->addMonths($loan->duration);
            $loan->approved_date = Carbon::now();
            $loan->repayment_start_date = Carbon::now();
            $loan->save();

            return [
                'success' => true,
                'message' => 'Loan approved successfully.',
                'data' => $loan
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function calculateLoanStats()
    {
        $loanResponse = $this->loanRepository->getTotalLoanData();

        if (!$loanResponse['success']) {
            return $loanResponse;
        }

        $loans = $loanResponse['data'];

        // Total loan amount
        $totalAmount = $loans->sum('amount');

        // Current month
        $currentMonth = now()->month;
        $lastMonth = now()->subMonth()->month;

        // Totals by month (using the loaded collection)
        $currentMonthTotal = $loans->where('approved_date', '!=', null)
            ->filter(function ($loan) use ($currentMonth) {
                return Carbon::parse($loan->approved_date)->month == $currentMonth;
            })
            ->sum('amount');

        $lastMonthTotal = $loans->where('approved_date', '!=', null)
            ->filter(function ($loan) use ($lastMonth) {
                return Carbon::parse($loan->approved_date)->month == $lastMonth;
            })
            ->sum('amount');

        // Growth rate calculation
        if ($lastMonthTotal == 0) {
            $growthRate = 100; // or 0 depending on your preference
        } else {
            $growthRate = (($currentMonthTotal - $lastMonthTotal) / $lastMonthTotal) * 100;
        }

        return [
            'success' => true,
            'message' => 'Loan statistics calculated successfully.',
            'data' => [
                'total_amount' => $totalAmount,
                'current_month_total' => $currentMonthTotal,
                'last_month_total' => $lastMonthTotal,
                'growth_rate' => round($growthRate, 2),
            ]
        ];
    }


    private function calculateInstallment($loanAmount, $annualInterestRate, $months)
    {
        $monthlyRate = $annualInterestRate / 12 / 100;

        if ($monthlyRate == 0) {
            return round($loanAmount / $months, 2);
        }

        $emi = ($loanAmount * $monthlyRate * pow(1 + $monthlyRate, $months))
            / (pow(1 + $monthlyRate, $months) - 1);

        return round($emi, 2);
    }


    private function remainingLoanBalance($loanAmount, $annualInterestRate, $totalInstallments, $paidInstallments)
    {
        // Convert annual interest rate to monthly decimal rate
        $monthlyRate = $annualInterestRate / 12 / 100;

        // If no interest, simple straight-line loan
        if ($monthlyRate == 0) {
            $emi = $loanAmount / $totalInstallments;
            return round($loanAmount - ($emi * $paidInstallments), 2);
        }

        // EMI formula:
        // EMI = P * r * (1+r)^n / ((1+r)^n - 1)
        $emi = ($loanAmount * $monthlyRate * pow(1 + $monthlyRate, $totalInstallments))
            / (pow(1 + $monthlyRate, $totalInstallments) - 1);

        // Remaining loan balance formula:
        // B = P * (1+r)^k - EMI * ((1+r)^k - 1) / r
        $remaining = $loanAmount * pow(1 + $monthlyRate, $paidInstallments)
            - ($emi * (pow(1 + $monthlyRate, $paidInstallments) - 1) / $monthlyRate);

        return round($remaining, 2);
    }


    private function calculateProgress($loanAmount, $remainingBalance)
    {
        return round(($loanAmount - $remainingBalance) / $loanAmount * 100, 2);
    }

    public function getLoanTypeDetails()
    {
        return $this->loanRepository->getLoanTypeDetails();
    }

    public function markAsCollected(array $data)
    {
        try {
            $response = $this->loanRepository->updateLoanStatus($data['loan_id'], 'paid');
            if (!$response['success']) {
                return $response;
            }
            $logData = [
                'done_by' => $data['recorded_by'],
                'title' => 'Loan Marked As Collected',
                'description' => 'Loan repayment completed',
                'loan_id' => $data['loan_id'],
            ];
            $log = app(AdminService::class)->logAction($logData);
            if (!$log['success']) {
                return $log;
            }
            return [
                'success' => true,
                'message' => 'Loan marked as collected',
                'data' => null
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function rejectLoanRequest($data)
    {
        try {
            $response = $this->loanRepository->updateLoanStatus($data['loan_id'], 'rejected');
            if (!$response['success']) {
                return $response;
            }

            return [
                'success' => true,
                'message' => 'Loan approved successfully.',
                'data' => $response['data']
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
