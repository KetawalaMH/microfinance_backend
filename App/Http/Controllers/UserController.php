<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordOtpMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\BankRepositoryInterface;
use Illuminate\Support\Facades\Log; // Import Log facade at the top
use App\Mail\InvitationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    private $userRepository, $bankRepository;

    public function __construct(UserRepositoryInterface $userRepository, BankRepositoryInterface $bankRepository)
    {
        $this->userRepository = $userRepository;
        $this->bankRepository = $bankRepository;
    }



    protected function logError($url, $error_message)
    {
        Log::error('Error in user controller function', [
            'url' => $url,
            'error' => $error_message
        ]);
    }

    public function userSignUp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'full_name' => 'required|string|min:1|max:255',
                'email_address' => 'required|email|max:255',
                'password' => 'required|string|min:8|confirmed'
            ]);

            if ($validator->fails()) {
                $output['success'] = false;
                $output['message'] = $validator->errors()->first();
                $output['data'] = null;
            } else {
                $data = json_decode($request->getContent(), true);
                $data['url'] = $request->url();
                $data['user_type_id'] = 1;
                $out_data = $this->userRepository->userSignUp($data);
                if (!$out_data['success']) {
                    $output['success'] = false;
                    $output['message'] = $out_data['message'];
                    $output['data'] = null;
                    return response()->json(['success' => $output['success'], 'message' => $output['message'], 'output' => $output['data']], 200);
                }

                $user = $out_data['data'];
                // Now make the bank profile
                $bankData = [
                    'bank_name' => $data['bank_name'],
                    'location' => $data['location'],
                    'owner_id' => $user['user_id'],
                ];

                $bank_out_data = $this->bankRepository->createBankProfile($bankData);
                if (!$bank_out_data['success']) {
                    $output['success'] = false;
                    $output['message'] = $bank_out_data['message'];
                    $output['data'] = null;
                    return response()->json(['success' => $output['success'], 'message' => $output['message'], 'output' => $output['data']], 200);
                }

                $bank = $bank_out_data['data'];

                //update user with bank id
                $user_out_data = $this->userRepository->updateUser($user['user_id'], ['bank_id' => $bank['id']]);
                if (!$user_out_data['success']) {
                    $output['success'] = false;
                    $output['message'] = $user_out_data['message'];
                    $output['data'] = null;
                    return response()->json(['success' => $output['success'], 'message' => $output['message'], 'output' => $output['data']], 200);
                }

                $output['success'] = $out_data['success'];
                $output['message'] = $out_data['message'];
                $output['data'] = $out_data['data'];
            }
        } catch (\Exception $e) {
            $url = $request->url();
            $error_message = $e->getMessage();
            $this->logError($url, $error_message);
            $output['success'] = false;
            $output['message'] = "Something went wrong, please try again: " . $e->getMessage();
            $output['data'] = null;
        }

        return response()->json(['success' => $output['success'], 'message' => $output['message'], 'output' => $output['data']], 200);
    }

    public function userSignIn(Request $request)
    {
        try {
            $validator = Validator::make(data: $request->all(), rules: [
                'email_address' => 'required|email|max:255'
            ]);
            if ($validator->fails()) {
                $output['success'] = false;
                $output['message'] = $validator->errors()->first();
                $output['data'] = null;
            } else {
                //$data = json_decode($request->getContent(), true);
                $data = $request->all();
                $data['url'] = $request->url();
                $out_data = $this->userRepository->userSignIn(data: $data);

                $output['success'] = $out_data['success'];
                $output['message'] = $out_data['message'];
                $output['data'] = $out_data['data'];
            }
        } catch (\Exception $e) {
            $url = $request->url();
            $error_message = $e->getMessage();
            $this->logError($url, $error_message);
            $output['success'] = false;
            $output['message'] = "Something went wrong, please try again: " . $e->getMessage();
            $output['data'] = null;
        }

        return response()->json(['success' => $output['success'], 'message' => $output['message'], 'output' => $output['data']], 200);
    }

    public function generateOTP(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email_address' => 'required|email'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'data' => $validator->errors()
                ], 400);
            } else {
                $result = $this->userRepository->generateOTP($request->all());
                if (!$result['success']) {
                    return response()->json([
                        'success' => false,
                        'message' => $result['message'],
                        'data' => null
                    ], 400);
                }
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data']
                ], 200);
            }
        } catch (\Exception $e) {
            $url = "auth/otp/generate";
            $this->logError($url, $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => "Something went wrong, please try again: " . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function otpVerify(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email_address' => 'required|email',
                'otp' => 'required|string|min:6',
                'reference' => 'required|string|min:12|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'data' => $validator->errors()
                ], 400);
            } else {
                $result = $this->userRepository->otpVerify($request->all());
                if (!$result['success']) {
                    return response()->json([
                        'success' => false,
                        'message' => $result['message'],
                        'data' => null
                    ], 400);
                }
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data']
                ], 200);
            }
        } catch (\Exception $e) {
            $url = "auth/otp/verify";
            $this->logError($url, $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => "Something went wrong, please try again: " . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function logOut(Request $request)
    {
        try {
            //valid credential
            $credentials = json_decode($request->getContent(), true);
            $validator = Validator::make($credentials, [
                'token' => 'required'
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                $output['success'] = false;
                $output['data'] = null;
                //$output['message'] = $validator->messages();
                $output['message'] = "Something went wrong, please try again: " . $validator->messages();
            } else {
                $token = $credentials['token'];
                //Request is validated, do logout
                JWTAuth::invalidate($token);
                $output['success'] = true;
                $output['data'] = null;
                $output['message'] = "Logout successful";
            }

        } catch (\Exception $e) {
            $output['success'] = false;
            $output['data'] = null;
            $output['message'] = "Something went wrong, please try again: " . $e->getMessage();

        }
        return response()->json(['success' => $output['success'], 'message' => $output['message'], 'output' => $output['data']], 200);
    }
    public function getAllUser(Request $request)
    {
        try {
            $data = json_decode($request->getContent(), true);
            $data['url'] = $request->url();
            Log::info($data);
            $output['success'] = true;
            $output['data'] = $data;
            $output['message'] = "Get All User successful";
        } catch (\Exception $e) {
            $output['success'] = false;
            $output['data'] = null;
            $output['message'] = "Something went wrong, please try again: " . $e->getMessage();

        }
        return response()->json(['success' => $output['success'], 'message' => $output['message'], 'output' => $output['data']], 200);
    }

    public function getAllUsers()
    {
        try {
            $user = JwtAuth::parseToken()->authenticate();
            if ($user['user_type_id'] != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to access this resource',
                    'data' => null
                ]);
            }
            $result = $this->userRepository->getAllUsers($user['bank_id']);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'pagination' => $result['pagination'],
                'data' => $result['data'],
                'filter' => $result['filter']
            ], 200);

        } catch (\Exception $e) {
            $url = "auth/users";
            $this->logError($url, $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => "Something went wrong, please try again: " . $e->getMessage()
            ], 500);
        }
    }

    public function deleteUser(Request $request)
    {
        try {
            $id = $request->input('id');

            if (!$id) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ID is required'
                ], 400);
            }

            $result = $this->userRepository->deleteUser($id);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => $result['data']
            ], 200);

        } catch (\Exception $e) {
            $url = "auth/users/delete";
            $this->logError($url, $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => "Something went wrong, please try again: " . $e->getMessage()
            ], 500);
        }
    }

    public function updateUser(Request $request)
    {
        try {
            $id = $request->input('id');

            if (!$id) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ID is required'
                ], 400);
            }
            $validator = Validator::make($request->all(), [
                'full_name' => 'sometimes|string|max:255',
                'email_address' => 'sometimes|email|max:255',
                'mobile_number' => 'sometimes|string|max:20',
                'password' => 'sometimes|string|min:6',
                'user_type_id' => 'sometimes|integer',
                'nic_number' => 'sometimes|string|max:20',
                'address_line1' => 'sometimes|string|max:255',
                'address_line2' => 'sometimes|string|max:255',
                'city_id' => 'sometimes|integer',
                'district_id' => 'sometimes|integer',
                'province_id' => 'sometimes|integer'
            ]);

            $full_name = $request->input('full_name');
            $email_address = $request->input('email_address');
            $mobile_number = $request->input('mobile_number');
            $password = $request->input('password');
            $user_type_id = $request->input('user_type_id');
            $nic_number = $request->input('nic_number');
            $address_line1 = $request->input('address_line1');
            $address_line2 = $request->input('address_line2');
            $city_id = $request->input('city_id');
            $district_id = $request->input('district_id');
            $province_id = $request->input('province_id');

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 400);
            }

            $result = $this->userRepository->updateUser($id, [
                'full_name' => $full_name,
                'email_address' => $email_address,
                'mobile_number' => $mobile_number,
                'password' => $password,
                'user_type_id' => $user_type_id,
                'nic_number' => $nic_number,
                'address_line1' => $address_line1,
                'address_line2' => $address_line2,
                'city_id' => $city_id,
                'district_id' => $district_id,
                'province_id' => $province_id
            ]);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => $result['data']
            ], 200);

        } catch (\Exception $e) {
            $url = "auth/users/update";
            $this->logError($url, $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => "Something went wrong, please try again: " . $e->getMessage()
            ], 500);
        }
    }

    public function userData()
    {
        try {
            $id = JwtAuth::user()->id;
            $result = $this->userRepository->userData($id);
            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 400);
            }
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => $result['data']
            ], 200);
        } catch (\Exception $e) {
            $url = "auth/users/data";
            $this->logError($url, $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => "Something went wrong, please try again: " . $e->getMessage()
            ], 500);
        }
    }

    public function inviteUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email_address' => 'required|email',
                'full_name' => 'required|string|min:1|max:255',
                'user_type_id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'data' => $validator->errors()
                ], 400);
            }
            $data = json_decode($request->getContent(), true);
            $data['url'] = $request->url();
            $data['is_active'] = 0;
            //check esxisting invitation

            $out_data = $this->userRepository->userSignUp($data);
            if (!$out_data['success']) {
                $output['success'] = false;
                $output['message'] = $out_data['message'];
                $output['data'] = null;
                return response()->json(['success' => $output['success'], 'message' => $output['message'], 'output' => $output['data']], 200);
            }

            $new_user = $out_data['data'];
            $token = $new_user['token'];
            $inivitationData = [
                'user_id' => $new_user['user_id'],
                'sent_by' => JwtAuth::user()->id,
                'bank_id' => JwtAuth::user()->bank_id,
                'token' => $new_user['token'],
                'email_address' => $data['email_address']
            ];
            // save invitation 
            $invitation = $this->userRepository->saveInvitation($inivitationData);
            if (!$invitation['success']) {
                $this->userRepository->deleteUser($new_user['user_id']);
                $output['success'] = false;
                $output['message'] = $invitation['message'];
                $output['data'] = null;
                return response()->json(['success' => $output['success'], 'message' => $output['message'], 'output' => $output['data']], 200);
            }
            //send invitation email
            try {
                Mail::to($data['email_address'])->send(new InvitationMail($token));
            } catch (\Exception $e) {
                // Log the error or handle it as needed
                Log::error('Failed to send invitation email: ' . $e->getMessage());

                // Optionally return or throw a custom response
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send the invitation email. Please try again later.'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Invitation sent successfully',
                'data' => null
            ]);
        } catch (\Exception $e) {
            $url = "auth/users/data";
            $this->logError($url, $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => "Something went wrong, please try again: " . $e->getMessage()
            ], 500);
        }
    }

    public function verifyInvitation(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email_address' => 'required|email',
                'full_name' => 'required|string|min:1|max:255',
                'token' => 'required|string|min:1',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'data' => $validator->errors()
                ], 400);
            }
            $data = json_decode($request->getContent(), true);
            Log::info($data);
            //get invitation data
            $invitation = $this->userRepository->getInvitation($data['token']);
            if (!$invitation['success']) {
                $output['success'] = false;
                $output['message'] = $invitation['message'];
                $output['data'] = null;
                return response()->json(['success' => $output['success'], 'message' => $output['message'], 'output' => $output['data']], 200);
            }
            $invitation = $invitation['data'];
            $data['user_id'] = $invitation['user_id'];
            $data['bank_id'] = $invitation['bank_id'];
            $data['url'] = $request->url();


            $out_data = $this->userRepository->updateUser($data['user_id'], $data);
            if (!$out_data['success']) {
                $output['success'] = false;
                $output['message'] = $out_data['message'];
                $output['data'] = null;
                return response()->json(['success' => $output['success'], 'message' => $output['message'], 'output' => $output['data']], 200);
            }

            $updatedInviation = $this->userRepository->updateInvitation($invitation['id']);
            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => null
            ]);
        } catch (\Exception $e) {
            $url = "auth/users/data";
            $this->logError($url, $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => "Something went wrong, please try again: " . $e->getMessage()
            ], 500);
        }
    }

    public function resetPasswordOtpSend(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email_address' => 'required|email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'data' => $validator->errors()
                ], 400);
            }
            $data = json_decode(json: $request->getContent(), associative: true);
            $data['url'] = $request->url();
            //check for user
            $user = $this->userRepository->getUserByEmail($data['email_address']);
            if (!$user['success']) {
                return response()->json(data: [
                    'success' => false,
                    'message' => "This email is not rgisteres",
                    'data' => null
                ]);
            }
            // Generate OTP (6-digit code)
            $otp = rand(min: 100000, max: 999999);

            //save otp in db
            $dto = [
                'email' => $data['email_address'],
                'otp' => $otp,
                'expires_at' => now()->addMinutes(5),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $out_data = $this->userRepository->saveOtp($dto);
            if (!$out_data['success']) {
                return response()->json(data: [
                    'success' => false,
                    'message' => $out_data['message'],
                    'data' => null
                ]);
            }

            //send invitation email
            try {
                Mail::to(users: $data['email_address'])->send(mailable: new ResetPasswordOtpMail(otp: $otp));
            } catch (\Exception $e) {
                // Log the error or handle it as needed
                Log::error(message: 'Failed to send otp: ' . $e->getMessage());

                // Optionally return or throw a custom response
                return response()->json(data: [
                    'success' => false,
                    'message' => 'Failed to send the invitation email. Please try again later.'
                ], status: 500);
            }

            return response()->json(data: [
                'success' => true,
                'message' => 'Otp sent successfully',
                'data' => null
            ]);
        } catch (\Exception $e) {
            $url = "auth/users/data";
            $this->logError(url: $url, error_message: $e->getMessage());
            return response()->json(data: [
                'success' => false,
                'message' => "Something went wrong, please try again: " . $e->getMessage()
            ], status: 500);
        }
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make(data: $request->all(), rules: [
                'email_address' => 'required|email',
                'otp' => 'required|digits:6',
            ]);

            if ($validator->fails()) {
                return response()->json(data: ['success' => false, 'message' => 'Validation error', 'data' => $validator->errors()], status: 400);
            }

            $email = $request->email_address;
            $otp = $request->otp;

            $otpRecord = $this->userRepository->getOtp($email, $otp);

            if (!$otpRecord) {
                return response()->json(data: ['success' => false, 'message' => 'Invalid OTP'], status: 400);
            }

            if (now()->greaterThan(date: $otpRecord->expires_at)) {
                return response()->json(data: ['success' => false, 'message' => 'OTP has expired'], status: 400);
            }

            // Get user
            $user = $this->userRepository->getUserByEmail($email);
            if (!$user['success']) {
                return response()->json(data: [
                    'success' => false,
                    'message' => $user['message'],
                    'data' => null
                ]);
            }

            $user = $user['data'];

            // Generate auth token
            if (!$token = JWTAuth::fromUser($user)) {
                return response()->json(data: [
                    'success' => false,
                    'message' => 'Failed to generate token',
                    'data' => null
                ], status: 500);
            }

            // OTP is valid → delete it
            $this->userRepository->deleteOtp($otpRecord->id);
            $data = [
                'token' => $token,
                'user' => $user
            ];

            return response()->json(data: [
                'success' => true,
                'message' => 'OTP verified successfully',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            $url = "auth/users/data";
            $this->logError(url: $url, error_message: $e->getMessage());
            return response()->json(data: [
                'success' => false,
                'message' => "Something went wrong, please try again: " . $e->getMessage()
            ], status: 500);
        }
    }

    public function resetPassword(Request $request)
    {
        try {

            $validator = Validator::make(data: $request->all(), rules: [
                'email_address' => 'required|email',
                'password' => 'required|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json(data: ['success' => false, 'message' => 'Validation error', 'data' => $validator->errors()], status: 400);
            }
            $user = $this->userRepository->getUserByEmail($request->email_address);

            if (!$user['success']) {
                return response()->json(data: [
                    'success' => false,
                    'message' => 'User not found',
                    'data' => null
                ], status: 404);
            }
            $user = $user['data'];
            $updateData = [
                'password' => $request->password,
            ];

            // update password (hashed)
            $output = $this->userRepository->updateUser(id: $user['id'], data: $updateData);
            if (!$output['success']) {
                return response()->json(data: [
                    'success' => false,
                    'message' => $output['message'],
                    'data' => null
                ]);
            }
            // issue JWT token (like in your sign-in)
            if (!$token = JWTAuth::fromUser($user)) {
                return response()->json(data: [
                    'success' => false,
                    'message' => 'Failed to generate token',
                    'data' => null
                ], status: 500);
            }

            return response()->json(data: [
                'success' => true,
                'message' => 'Password set successfully',
                'data' => [
                    'user_id' => $user->id,
                    'full_name' => $user->full_name,
                    'email_address' => $user->email_address,
                    'token' => $token
                ]
            ], status: 200);

        } catch (\Exception $e) {
            \Log::error(message: 'Password setup failed: ' . $e->getMessage());
            return response()->json(data: [
                'success' => false,
                'message' => 'Something went wrong, please try again',
                'data' => null
            ], status: 500);
        }
    }
    public function updatePassword(Request $request)
    {
        try {
            $validator = Validator::make(data: $request->all(), rules: [
                'current_password' => 'required|min:6',
                'new_password' => 'required|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json(data: ['success' => false, 'message' => 'Validation error', 'data' => $validator->errors()], status: 400);
            }
            $id = JwtAuth::user()->id;
            $response = $this->userRepository->updatePassword($id, $request->current_password, $request->new_password);
            if (!$response['success']) {
                return response()->json(data: [
                    'success' => false,
                    'message' => $response['message'],
                    'data' => null
                ]);
            }
            return response()->json(data: [
                'success' => true,
                'message' => 'Password updated successfully',
                'data' => null
            ], status: 200);

        } catch (\Exception $e) {
            \Log::error(message: 'Password setup failed: ' . $e->getMessage());
            return response()->json(data: [
                'success' => false,
                'message' => 'Something went wrong, please try again',
                'data' => null
            ], status: 500);
        }
    }


}