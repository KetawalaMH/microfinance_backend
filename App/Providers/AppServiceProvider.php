<?php

namespace App\Providers;

use App\Repositories\AdminRepository;
use App\Repositories\Interfaces\AdminRepositoryInterface;
use App\Repositories\Interfaces\MemberRepositoryInterface;
use App\Repositories\Interfaces\OtpRepositoryInterface;
use App\Repositories\Interfaces\SavingRepositoryInterface;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Repositories\MemberRepository;
use App\Repositories\OtpRepository;
use App\Repositories\SavingRepository;
use App\Repositories\TransactionRepository;
use App\Services\AdminService;
use App\Services\BankService;
use App\Services\Interfaces\AdminServiceInterface;
use App\Services\Interfaces\BankServiceInterface;
use App\Services\Interfaces\MemberServiceInterface;
use App\Services\Interfaces\OtpServiceInterface;
use App\Services\Interfaces\SavingServiceInterface;
use App\Services\Interfaces\SettingServiceInterface;
use App\Services\Interfaces\TransactionServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use App\Services\MemberService;
use App\Services\OtpService;
use App\Services\SavingService;
use App\Services\SettingService;
use App\Services\TransactionService;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;

use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Repositories\SettingRepository;

use App\Repositories\Interfaces\BankRepositoryInterface;
use App\Repositories\BankRepository;
use App\Repositories\Interfaces\LoanRepositoryInterface;
use App\Repositories\LoanRepository;
use App\Services\Interfaces\LoanServiceInterface;
use App\Services\LoanService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(SettingRepositoryInterface::class, SettingRepository::class);
        $this->app->bind(BankRepositoryInterface::class, BankRepository::class);
        $this->app->bind(MemberRepositoryInterface::class, MemberRepository::class);
        $this->app->bind(MemberServiceInterface::class, MemberService::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(BankServiceInterface::class, BankService::class);
        $this->app->bind(SettingServiceInterface::class, SettingService::class);
        $this->app->bind(SavingServiceInterface::class, SavingService::class);
        $this->app->bind(SavingRepositoryInterface::class, SavingRepository::class);
        $this->app->bind(TransactionServiceInterface::class, TransactionService::class);
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
        $this->app->bind(AdminRepositoryInterface::class, AdminRepository::class);
        $this->app->bind(AdminServiceInterface::class, AdminService::class);
        $this->app->bind(OtpServiceInterface::class, OtpService::class);
        $this->app->bind(OtpRepositoryInterface::class, OtpRepository::class);
        $this->app->bind(LoanRepositoryInterface::class, LoanRepository::class);
        $this->app->bind(LoanServiceInterface::class, LoanService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
