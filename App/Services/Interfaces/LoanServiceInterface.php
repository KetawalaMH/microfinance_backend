<?php

namespace App\Services\Interfaces;

interface LoanServiceInterface
{
    public function createLoanRequest(array $data);

    public function saveLoanDocuments(array $data);

    public function addGaurantors(array $data);

    public function submitLoanApplication(array $data);

    public function addInstallments(array $data);

    public function getAllLoanApplications(array $data);

    public function getLoanDetails(array $data);

    public function approveLoanRequest(array $data);
}
