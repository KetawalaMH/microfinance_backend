<?php

namespace App\Services\Interfaces;


interface UserServiceInterface
{
    public function userSignUp(array $data);
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

    public function addUserBulk(array $data);

    public function userData($id);

    public function addUser($data);

    public function getUserByEmail($email);

    public function resetPasswordOtpSend($data);

    public function verifyOtp($email, $otp);

    public function updatePassword($id, $currentPassword, $newPassword);
}