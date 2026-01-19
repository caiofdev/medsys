<?php

namespace App\Domain\Contracts;

use App\Domain\Models\Admin;
use Illuminate\Pagination\LengthAwarePaginator;

interface AdminRepositoryInterface
{
    public function findById(int $id): Admin;

    public function paginate(int $perPage = 10): LengthAwarePaginator;

    public function countMasters(): int;

    public function create(array $data): Admin;

    public function update(Admin $admin, array $data): Admin;

    public function delete(Admin $admin): void;

    public function search(?string $search, int $perPage = 10): LengthAwarePaginator;
}