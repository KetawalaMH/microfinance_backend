<?php

namespace App\Services;

use App\Http\Controllers\UserController;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\Interfaces\SettingServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use Hash;
use Log;


class UserService implements UserServiceInterface
{
    private $userRepository;
    private $settingService;


    public function __construct(UserRepositoryInterface $userRepository, SettingServiceInterface $settingService)
    {
        $this->userRepository = $userRepository;
        $this->settingService = $settingService;

    }

    public function userSignUp(array $data)
    {
        return $this->userRepository->userSignUp($data);
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
        return $this->userRepository->updateUser($id, $data);
    }

    public function getUserData($id)
    {
        return $this->userRepository->getUserData($id);
    }

    public function saveInvitation(array $data)
    {
        return $this->userRepository->saveInvitation($data);
    }

    public function getInvitation(array $data)
    {
        return $this->userRepository->getInvitation($data);
    }

    public function updateInvitation($id)
    {
        return $this->userRepository->updateInvitation($id);
    }

    public function resetPassword(array $data)
    {
        return $this->userRepository->resetPassword($data);
    }

    public function saveOtp(array $data)
    {
        return $this->userRepository->saveOtp($data);
    }

    public function getOtp(string $email, string $otp)
    {
        return $this->userRepository->getOtp($email, $otp);
    }

    public function deleteOtp($id)
    {
        return $this->userRepository->deleteOtp($id);
    }

    public function updatePassword(string $id, string $currentPassword, string $newPassword)
    {
        return $this->userRepository->updatePassword($id, $currentPassword, $newPassword);
    }

    public function approvalVerification(array $data)
    {
        return $this->userRepository->approvalVerification($data);
    }

    public function addUserBulk(array $data)
    {
        try {
            for ($i = 0; $i < count($data); $i++) {
                $department_id = $this->settingService->getDepartmntId($data[$i]['department']);
                $branch_id = $this->settingService->getBranchId($data[$i]['branch']);
                $data[$i]['branch_id'] = $branch_id;
                $data[$i]['department_id'] = $department_id;
                $data[$i]['password'] = Hash::make($data[$i]['nic']);
                $this->userRepository->addUserBulk($data[$i]);
            }
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

            Log::info($user);

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


}