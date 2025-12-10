<?php

namespace App\Services;

use App\Http\Controllers\UserController;
use App\Mail\InvitationMail;
use App\Mail\ResetPasswordOtpMail;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\Interfaces\OtpServiceInterface;
use App\Services\Interfaces\SettingServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserService implements UserServiceInterface
{
    private $userRepository;
    private $settingService;

    private $otpService;


    public function __construct(UserRepositoryInterface $userRepository, SettingServiceInterface $settingService, OtpServiceInterface $otpService)
    {
        $this->userRepository = $userRepository;
        $this->settingService = $settingService;
        $this->otpService = $otpService;
    }

    public function userSignUp(array $data)
    {
        return $this->userRepository->userSignUp($data);
    }


    public function approvalVerification($data)
    {
        return $this->userRepository->approvalVerification($data);
    }

    public function userSignIn(array $data)
    {
        return $this->userRepository->userSignIn($data);
    }

    public function userValidate(array $data)
    {
        return $this->userRepository->userValidate($data);
    }

    public function getAllUsers(array $data)
    {
        return $this->userRepository->getAllUsers($data);
    }

    public function deleteUser($id)
    {
        return $this->userRepository->deleteUser($id);
    }

    public function updateUser($id, array $data)
    {
        // return $this->userRepository->updateUser($id, $data);
    }

    public function getUserData($id)
    {
        // return $this->userRepository->getUserData($id);
    }

    public function saveInvitation(array $data)
    {
        return $this->userRepository->saveInvitation($data);
    }

    public function getInvitation(array $data)
    {
        $otpData = $this->otpService->verifyOtp(identifier: $data['email_address'], otp: $data['otp']);
        if (!$otpData) {
            return [
                'success' => false,
                'message' => 'Invalid OTP',
                'data' => null
            ];
        }
        return $this->userRepository->getInvitation($data);
    }

    public function updateInvitation($id)
    {
        // return $this->userRepository->updateInvitation($id);
    }

    public function resetPassword(array $data)
    {
        // return $this->userRepository->resetPassword($data);
    }

    public function updatePassword($id, $currentPassword, $newPassword)
    {
        return $this->userRepository->updatePassword($id, $currentPassword, $newPassword);
    }

    public function addUserBulk(array $data)
    {
        try {
            for ($i = 0; $i < count($data); $i++) {
                $department_id = $this->settingService->getDepartmntId($data[$i]['department']);
                $branch_id = $this->settingService->getBranchId($data[$i]['branch_code']);
                $data[$i]['branch_id'] = $branch_id;
                $data[$i]['department_id'] = $department_id;
                $data[$i]['password'] = $data[$i]['nic'];
                $user = $this->userRepository->userSignUp($data[$i]);
                if (!$user['success']) {
                    return $user;
                }
            }

            return [
                'success' => true,
                'message' => 'Users added successfully',
                'data' => null
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }


    public function userData($id)
    {
        try {
            $response = $this->userRepository->userData($id);

            if (!$response['success']) {
                return [
                    'success' => false,
                    'message' => $response['message'],
                    'data' => null
                ];
            }

            // user returned is an array
            $user = $response['data'];

            $formatted = [
                'user_id' => $user['id'],
                'full_name' => $user['full_name'],
                'email_address' => $user['email_address'],
                'mobile_number' => $user['mobile_number'],
                'email_verified_at' => $user['email_verified_at'],
                'is_active' => $user['is_active'],
                'created_at' => $user['created_at'],
                'updated_at' => $user['updated_at'],
                'user_type_id' => $user['user_type_id'],

                // relations (array check)
                'role' => $user['user_type']['user_type'] ?? null,
                'org' => $user['org'],
                'nic' => $user['nic'],
                'member_id' => $user['member_id'],

                'branch_id' => $user['branch_id'],
                'branch' => $user['branch']['branch'] ?? null,

                'department_id' => $user['department_id'],
                'department' => $user['department']['department'] ?? null,
            ];

            return [
                'success' => true,
                'message' => $response['message'],
                'data' => $formatted
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function addUser($data)
    {
        try {
            $out_data = $this->userSignUp($data);
            Log::info('out_data', $out_data);
            if (!$out_data['success']) {
                $output['success'] = false;
                $output['message'] = $out_data['message'];
                $output['data'] = null;
                return response()->json(['success' => $output['success'], 'message' => $output['message'], 'output' => $output['data']], 200);
            }
            $new_user = $out_data['data'];
            $token = $this->otpService->generateOTP($data['email_address']);
            $inivitationData = [
                'user_id' => $new_user['user_id'],
                'sent_by' => $data['sent_by'],
                'branch_id' => $data['branch_id'],
                'token' => $token,
                'email_address' => $data['email_address']
            ];
            // save invitation 
            $invitation = $this->saveInvitation($inivitationData);
            if (!$invitation['success']) {
                $this->deleteUser($new_user['user_id']);
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
        } catch (\Exception $e) {
            $output['success'] = false;
            $output['message'] = $e->getMessage();
            $output['data'] = null;
            return response()->json(['success' => $output['success'], 'message' => $output['message'], 'output' => $output['data']], 200);
        }
    }

    public function getUserByEmail($email)
    {
        return $this->userRepository->getUserByEmail($email);
    }



    public function resetPasswordOtpSend($data)
    {
        try {

            $otp = $this->otpService->generateOTP($data['email_address']);
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
            return response()->json(data: [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ]);
        }
    }

    public function verifyOtp($email, $otp)
    {
        return $this->otpService->verifyOtp($email, $otp);
    }
}
