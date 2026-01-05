<?php

namespace App\Application\Actions\Admin;

use App\Domain\Contracts\AdminRepositoryInterface;
use App\Domain\Models\Admin;

class ShowAdmin
{
    public function __construct(
        private AdminRepositoryInterface $adminRepository
    ) {}

    /**
     * Retrieve administrator by ID
     *
     * @param int $adminId Administrator ID
     * @return Admin
     */
    public function execute(int $adminId): Admin
    {
        return $this->adminRepository->findById($adminId);
    }
}