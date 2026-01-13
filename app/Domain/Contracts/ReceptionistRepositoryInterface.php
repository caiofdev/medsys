<?php

namespace App\Domain\Contracts;

use App\Domain\Models\Receptionist;
use Illuminate\Pagination\LengthAwarePaginator;

interface ReceptionistRepositoryInterface
{
    public function findById(int $id): Receptionist;

    public function paginate(int $perPage = 8): LengthAwarePaginator;

    public function search(?string $search, int $perPage = 8): LengthAwarePaginator;

    public function create(array $data): Receptionist;

    public function update(Receptionist $receptionist, array $data): Receptionist;

    public function delete(Receptionist $receptionist): void;
}