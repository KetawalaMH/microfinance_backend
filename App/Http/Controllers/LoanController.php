<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\LoanServiceInterface;
use Exception;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoanController extends Controller
{
    protected $loanService;

    public function __construct(LoanServiceInterface $loanService)
    {
        $this->loanService = $loanService;
    }
    public function createLoanRequest(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'borrower_id' => 'required|exists:members,id',
                'amount' => 'required|numeric',
                'purpose' => 'required|string',
                'loan_type_id' => 'required|exists:loan_types,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $data = $request->all();
            $data['submitted_by'] = JWTAuth::user()->id;
            $data['branch_id'] = JWTAuth::user()->branch_id;
            $loan = $this->loanService->createLoanRequest($data);
            return response()->json($loan, 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function uploadDocuments(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|max:5120', // max 5MB
                'loan_id' => 'required|exists:loans,id',
            ]);

            $file = $request->file('file');
            $loan_id = $request->input('loan_id');

            $fileName = $loan_id . '_' . time() . '.' . $file->getClientOriginalExtension();

            $path = $file->storeAs('loans/' . $loan_id, $fileName, 'public');

            $fileUrl = asset('storage/loans/' . $loan_id . '/' . $fileName);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'data' => ['fileUrl' => $fileUrl]
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function saveLoanDocuments(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'loan_id' => 'required|exists:loans,id',
                'documents' => 'required|array|min:1',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'data' => null
                ], 422);
            }
            $data = $request->all();
            $result = $this->loanService->saveLoanDocuments($data);
            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function addGuarantors(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'loan_id' => 'required|exists:loans,id',
                'guarantor1_id' => 'required|exists:members,id',
                'guarantor2_id' => 'nullable|exists:members,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'data' => null
                ], 422);
            }
            $data = $request->all();
            $result = $this->loanService->addGaurantors($data);
            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function submitLoanApplication(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'loan_id' => 'required|exists:loans,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'data' => null
                ], 422);
            }
            $data = $request->all();
            $data['recorded_by'] = JWTAuth::user()->id;
            $result = $this->loanService->submitLoanApplication($data);
            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function addInstallments(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'loan_id' => 'required|exists:loans,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'data' => null
                ], 422);
            }
            $data = $request->all();
            $data['recorded_by'] = JWTAuth::user()->id;
            $result = $this->loanService->addInstallments($data);
            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function getAllLoanApplications(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'loan_type_id' => 'nullable|exists:loan_types,id',
                'status' => 'nullable|in:draft,guranter_pending,pending,approved,rejected, due, paid',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'data' => null
                ], 422);
            }
            $data = $request->all();
            $result = $this->loanService->getAllLoanApplications($data);
            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function getLoanDetails(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'loan_id' => 'required|exists:loans,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'data' => null
                ], 422);
            }
            $data = $request->all();
            $result = $this->loanService->getLoanDetails($data);
            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function getLoanTypeDetails(Request $request)
    {
        try {
            $data = $request->all();
            $result = $this->loanService->getLoanTypeDetails($data);
            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function markAsCollected(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'loan_id' => 'required|exists:loans,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'data' => null
                ], 422);
            }
            $data = $request->all();
            $data['recorded_by'] = JWTAuth::user()->id;
            $result = $this->loanService->markAsCollected($data);
            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
