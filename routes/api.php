<?php

//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::post(uri: 'auth/login', action: [UserController::class, 'userSignIn']);
Route::post(uri: 'auth/register', action: [UserController::class, 'userSignUp']);
Route::post(uri: 'auth/user/send-reset-password-otp', action: [UserController::class, 'resetPasswordOtpSend']);
Route::post(uri: 'auth/user/verify-invitation', action: [UserController::class, 'verifyInvitation']);
Route::post(uri: '/auth/verify-otp', action: [UserController::class, 'verifyOtp']);

Route::group(attributes: ['middleware' => ['jwt.auth']], routes: function (): void {
    Route::post(uri: 'auth/user', action: [UserController::class, 'userData']);
    Route::get(uri: 'auth/users', action: [UserController::class, 'getAllUsers']);
    Route::post(uri: 'auth/users/delete', action: [UserController::class, 'deleteUser']);
    Route::post(uri: 'auth/users/update', action: [UserController::class, 'updateUser']);
    Route::post(uri: 'auth/users/invite', action: [UserController::class, 'inviteUser']);
    Route::post(uri: 'auth/user/reset-password', action: [UserController::class, 'resetPassword']);
    Route::post(uri: 'auth/user/update-password', action: [UserController::class, 'updatePassword']);

});