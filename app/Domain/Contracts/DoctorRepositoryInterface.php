<?php

namespace App\Domain\Contracts;

use App\Domain\Models\Doctor;
use Illuminate\Pagination\LengthAwarePaginator;

interface DoctorRepositoryInterface
{
    public function findById(int $id): Doctor;

    public function paginate(int $perPage = 10): LengthAwarePaginator;

    public function search(?string $search, int $perPage = 10): LengthAwarePaginator;

    public function create(array $data): Doctor;

    public function update(Doctor $doctor, array $data): Doctor;

    public function delete(Doctor $doctor): void;

    public function getPatientsWithConsultations(int $doctorId);

    public function getPatientConsultations(int $doctorId, int $patientId);
}