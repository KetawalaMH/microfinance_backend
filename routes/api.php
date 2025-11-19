<?php

//use Illuminate\Http\Request;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\SavingController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::post(uri: 'auth/login', action: [UserController::class, 'userSignIn']);
Route::post(uri: 'auth/register', action: [UserController::class, 'userSignUp']);
Route::post(uri: 'auth/user/send-reset-password-otp', action: [UserController::class, 'resetPasswordOtpSend']);
Route::post(uri: 'auth/user/verify-invitation', action: [UserController::class, 'verifyInvitation']);
Route::post(uri: '/auth/verify-otp', action: [UserController::class, 'verifyOtp']);
Route::get(uri: '/settings/get-member-types', action: [SettingController::class, 'getMemberTypes']);
Route::get(uri: '/settings/get-account-types', action: [SettingController::class, 'getAccountTypes']);

Route::post(uri: 'auth/user/verification', action: [UserController::class, 'approvalVerification']);

Route::group(attributes: ['middleware' => ['jwt.auth']], routes: function (): void {
    Route::get(uri: '/auth/user', action: [UserController::class, 'userData']);
    Route::get(uri: '/auth/users', action: [UserController::class, 'getAllUsers']);
    Route::post(uri: '/auth/users/delete', action: [UserController::class, 'deleteUser']);
    Route::post(uri: '/auth/users/update', action: [UserController::class, 'updateUser']);
    Route::post(uri: '/auth/users/invite', action: [UserController::class, 'inviteUser']);
    Route::post(uri: '/auth/user/add-user-bulk', action: [UserController::class, 'addUserBulk']);
    Route::post(uri: '/auth/user/reset-password', action: [UserController::class, 'resetPassword']);
    Route::post(uri: '/auth/user/update-password', action: [UserController::class, 'updatePassword']);
    Route::post(uri: '/auth/member/add-new-member', action: [MemberController::class, 'createMember']);
    Route::get(uri: '/auth/member/get-all-members', action: [MemberController::class, 'getAllMembers']);
    Route::post(uri: '/auth/saving/create-saving-account', action: [SavingController::class, 'createSavingAccount']);
    Route::post(uri: '/auth/saving/upload-documents', action: [SavingController::class, 'uploadDocuments']);
    Route::post(uri: '/auth/saving/update-saving-account', action: [SavingController::class, 'updateSavingAccount']);
    Route::get(uri: '/auth/saving/get-all-accounts', action: [SavingController::class, 'getSavingAccounts']);
});