<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\SavingServiceInterface;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

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
}
