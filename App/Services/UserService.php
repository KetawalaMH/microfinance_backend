<?php

namespace App\Services;

use App\Http\Controllers\UserController;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\Interfaces\UserServiceInterface;


class UserService implements UserServiceInterface
{
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository, )
    {
        $this->userRepository = $userRepository;
    }

    public function signUp(array $data)
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

    public function approvalVerification(array $data){
        return $this->userRepository->approvalVerification($data);
    }
}