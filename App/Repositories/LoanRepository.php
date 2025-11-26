<?php

namespace App\Repositories;

use App\Models\Loan;
use App\Models\LoanDocument;
use App\Models\LoanType;
use App\Repositories\Interfaces\LoanRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Log;

class LoanRepository implements LoanRepositoryInterface
{
    public function getLoanTypeById($id)
    {
        try {
            $loanType = LoanType::find($id);
            if (!$loanType) {
                return [
                    'success' => false,
                    'message' => 'Loan type not found.',
                    'data' => null
                ];
            }
            return [
                'success' => true,
                'message' => 'Loan type fetched successfully.',
                'data' => $loanType
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function createLoanRequest($data)
    {
        try {
            Log::info('repository');
            Log::info($data);
            $loan = Loan::create($data);
            if (!$loan) {
                return [
                    'success' => false,
                    'message' => 'Loan request not created.',
                    'data' => null
                ];
            }
            return [
                'success' => true,
                'message' => 'Loan request created successfully.',
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

    public function saveLoanDocuments($data)
    {
        try {
            $loan = Loan::find($data['loan_id']);
            if (!$loan) {
                return [
                    'success' => false,
                    'message' => 'Loan not found.',
                    'data' => null
                ];
            }
            foreach ($data['documents'] as $document) {
                LoanDocument::create([
                    'loan_id' => $loan->id,
                    'document_path' => $document
                ]);
            }
            return [
                'success' => true,
                'message' => 'Loan documents saved successfully.',
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

    public function getMemberDueLoans($id)
    {
        try {
            $loan = Loan::where('borrower_id', $id)->where('status', 'due')->get();
            return [
                'success' => true,
                'message' => 'Loan fetched successfully.',
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

    public function addGaurantors(array $data)
    {
        try {
            $loan = Loan::find($data['loan_id']);
            if (!$loan) {
                return [
                    'success' => false,
                    'message' => 'Loan not found.',
                    'data' => null
                ];
            }

            if ($loan->guarantor1_id && $loan->guarantor2_id) {
                return [
                    'success' => false,
                    'message' => 'Gaurantors already added.',
                    'data' => null
                ];
            }
            $loan->guranter1_id = $data['guarantor1_id'];
            $loan->guranter2_id = $data['guarantor2_id'];
            $loan->save();

            $loan = Loan::with(['gurantor1', 'gurantor2', 'borrower'])
                ->find($loan->id);

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

    public function submitLoanApplication(array $data)
    {
        try {
            $loan = Loan::find($data['loan_id']);
            if (!$loan) {
                return [
                    'success' => false,
                    'message' => 'Loan not found.',
                    'data' => null
                ];
            }
            $loan->status = 'pending';
            $loan->save();
            return [
                'success' => true,
                'message' => 'Loan submitted successfully.',
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

    public function getLoanById($id)
    {
        try {
            $loan = Loan::with(['loanType', 'gurantor1', 'gurantor2', 'borrower'])->find($id);
            if (!$loan) {
                return [
                    'success' => false,
                    'message' => 'Loan not found.',
                    'data' => null
                ];
            }
            return [
                'success' => true,
                'message' => 'Loan fetched successfully.',
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

    public function getAllLoanApplications($data)
    {
        try {
            $query = array_filter([
                'status' => $data['status'] ?? null,
                'loan_type_id' => $data['loan_type_id'] ?? null
            ]);

            $loans = Loan::with('borrower')
                ->where($query)
                ->get();

            return [
                'success' => true,
                'message' => 'Loan fetched successfully.',
                'data' => $loans
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
