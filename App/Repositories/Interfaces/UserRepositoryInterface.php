<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;

interface UserRepositoryInterface
{
    public function userSignUp(array $data);
    public function userSignIn(array $data);
    public function userValidate($data);
    // public function updatePassword($data);
    // public function generateOTP($data);
    // public function otpVerify($data);

    public function encryptText($data);
    public function decryptText($data);
    // public function userVerify(array $data);
    /*public function requestOTPMobile(array $data);
    public function verifyOTPMobile(array $data);
    public function sendOTP(array $data);
    public function validateOTP(array $data);*/
    // public function userData();
    public function getAllUsers($data);
    // public function updateUser($id, $data);
    public function deleteUser($id);
    public function userData($id);
    public function saveInvitation($data);
    public function getInvitation($data);

}