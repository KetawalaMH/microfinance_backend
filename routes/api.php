<?php

//use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\SavingController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TransactionController;
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

    //--------------member--------
    Route::post(uri: '/auth/member/add-new-member', action: [MemberController::class, 'createMember']);
    Route::get(uri: '/auth/member/get-all-members', action: [MemberController::class, 'getAllMembers']);
    Route::get(uri: '/auth/member/get-member-details', action: [MemberController::class, 'getMemberDetails']);

    //-----------saving----------
    Route::post(uri: '/auth/saving/create-saving-account', action: [SavingController::class, 'createSavingAccount']);
    Route::post(uri: '/auth/saving/upload-documents', action: [SavingController::class, 'uploadDocuments']);
    Route::post(uri: '/auth/saving/update-saving-account', action: [SavingController::class, 'updateSavingAccount']);
    Route::get(uri: '/auth/saving/get-all-accounts', action: [SavingController::class, 'getSavingAccounts']);
    Route::post(uri: '/auth/transaction/create-transaction', action: [TransactionController::class, 'createTransaction']);
    Route::post(uri: '/auth/transaction/update-transaction', action: [TransactionController::class, 'updateTransaction']);
    Route::get(uri: '/auth/transaction/get-all-transactions', action: [TransactionController::class, 'getTransactions']);

    //----------admin ------------
    Route::post(uri: '/auth/admin/approve-member-request', action: [AdminController::class, 'approveMemberRequest']);
    Route::post(uri: '/auth/admin/approve-loan-request', action: [AdminController::class, 'approveLoanRequest']);

    //-----------loan-------------
    Route::post(uri: '/auth/loan/add-loan-application', action: [LoanController::class, 'createLoanRequest']);
    Route::post(uri: '/auth/loan/upload-documents', action: [LoanController::class, 'uploadDocuments']);
    Route::post(uri: '/auth/loan/save-loan-decuments', action: [LoanController::class, 'saveLoanDocuments']);
    Route::post(uri: '/auth/loan/add-guarantors', action: [LoanController::class, 'addGuarantors']);
    Route::post(uri: '/auth/loan/submit-loan-application', action: [LoanController::class, 'submitLoanApplication']);
    Route::post(uri: '/auth/loan/add-installments', action: [LoanController::class, 'addInstallments']);
    Route::get(uri: '/auth/loan/get-all-loan-applications', action: [LoanController::class, 'getAllLoanApplications']);
    Route::get(uri: '/auth/loan/get-loan-details', action: [LoanController::class, 'getLoanDetails']);
});
