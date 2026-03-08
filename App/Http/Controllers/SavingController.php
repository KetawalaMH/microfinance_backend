<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\SavingServiceInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;

class SavingController extends Controller
{
    protected $savingService;

    public function __construct(SavingServiceInterface $savingService)
    {
        $this->savingService = $savingService;
    }
    public function createSavingAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'member_id' => 'required|exists:members,id',
            'account_type_id' => 'required|exists:account_types,id',
            'initial_deposite' => 'required|numeric',
            'NIC_No' => 'required|string|min:10|max:13|unique:saving_accounts,NIC_No',
            'phone_number' => 'required|string|min:10|max:15|unique:saving_accounts,phone_number',
            'email_address' => 'required|string|email|max:255|unique:saving_accounts,email_address',
            'address' => 'required|string|max:500',
            'account_number' => 'required|string|unique:saving_accounts,account_number',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'data' => null
            ], 422);
        }
        $data = json_decode($request->getContent(), true);
        $data['url'] = $request->url();
        $data['created_by'] = JWTAuth::user()->id;
        $data['current_balance'] = $data['initial_deposite'];
        $result = $this->savingService->createSavingAccount($data);

        if ($result['success'] === false) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'data' => null
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Saving account created successfully',
            'data' => $result['data']
        ], 201);
    }

    public function uploadDocuments(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|max:5120', // max 5MB
                'key' => 'required|string',
                'saving_account_id' => 'required|exists:saving_accounts,id',
            ]);

            $file = $request->file('file');
            $key = $request->input('key');
            $saving_account_id = $request->input('saving_account_id');

            $fileName = $saving_account_id . '_' . $key . '_' . time() . '.' . $file->getClientOriginalExtension();

            $path = $file->storeAs('saving_accounts/' . $saving_account_id, $fileName, 'public');

            $fileUrl = asset('storage/saving_accounts/' . $saving_account_id . '/' . $fileName);

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

    public function updateSavingAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:saving_accounts,id',
            'NIC_doc' => 'required|string',
            'proof_of_address' => 'required|string',
            'deposite_slip' => 'required|string',
            'application_form' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'data' => null
            ], 422);
        }
        $data = json_decode($request->getContent(), true);
        $result = $this->savingService->updateSavingAccount($data);

        if ($result['success'] === false) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'data' => null
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Saving account updated successfully',
            'data' => $result['data']
        ]);
    }

    public function getSavingAccounts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_type_id' => 'nullable|exists:account_types,id',
            'status' => 'nullable|in:active,inactive,pending',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'data' => null
            ], 422);
        }
        $filters = $request->only('account_type_id', 'status');
        $result = $this->savingService->getSavingAccounts($filters);
        if ($result['success'] === false) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'data' => null
            ], 500);
        }
        return response()->json([
            'success' => true,
            'message' => 'Saving accounts fetched successfully',
            'data' => $result['data']
        ]);
    }

    public function getSavingAccountDetails(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'account_id' => 'required|exists:saving_accounts,id',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'data' => null
                ], 422);
            }
            $data = $request->all();
            $result = $this->savingService->getSavingAccountDetails($data['account_id']);
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
