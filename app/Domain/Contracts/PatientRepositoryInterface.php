<?php
// filepath: app/Domain/Contracts/PatientRepositoryInterface.php

namespace App\Domain\Contracts;

use App\Domain\Models\Patient;
use Illuminate\Pagination\LengthAwarePaginator;

interface PatientRepositoryInterface
{
    public function findById(int $id): Patient;

    public function paginate(int $perPage = 8): LengthAwarePaginator;

    public function search(?string $search, int $perPage = 8): LengthAwarePaginator;

    public function create(array $data): Patient;

    public function update(Patient $patient, array $data): Patient;

    public function delete(Patient $patient): void;

    public function searchForAutocomplete(string $query, int $limit = 10);
}