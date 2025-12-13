<?php

namespace App\Services\Interfaces;

interface HuggingfaceModelServiceInterface
{
    public function predict(array $prompt);
}
