<?php

namespace App\Services;

use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Services\Interfaces\SettingServiceInterface;

class SettingService implements SettingServiceInterface
{
    private $settingRepository;

    public function __construct(SettingRepositoryInterface $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    public function getMemberTypes()
    {
        return $this->settingRepository->getMemberTypes();
    }
}