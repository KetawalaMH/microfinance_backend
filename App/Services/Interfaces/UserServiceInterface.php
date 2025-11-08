<?php

namespace App\Services\Interfaces;


interface UserServiceInterface
{
    public function signUp(array $data);
    public function userSignIn(array $data);
    public function userValidate(array $data);
    public function getAllUsers(array $data);
    public function deleteUser($id);
    public function updateUser($id, array $data);
    public function getUserData($id);
    public function saveInvitation(array $data);
    public function getInvitation(array $data);
    public function updateInvitation($id);
    public function resetPassword(array $data);
    public function saveOtp(array $data);
    public function getOtp(string $email, string $otp);
    public function deleteOtp($id);
    public function updatePassword(string $id, string $currentPassword, string $newPassword);
}