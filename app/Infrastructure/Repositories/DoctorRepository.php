<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\DoctorRepositoryInterface;
use App\Domain\Exceptions\DoctorNotFoundException;
use App\Domain\Models\Doctor;
use App\Domain\Models\User;
use App\Domain\Models\Consultation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class DoctorRepository implements DoctorRepositoryInterface
{
    public function findById(int $id): Doctor
    {
        $doctor = Doctor::with('user')->find($id);
        
        if (!$doctor) {
            throw new DoctorNotFoundException();
        }
        
        return $doctor;
    }

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return Doctor::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function search(?string $search, int $perPage = 10): LengthAwarePaginator
    {
        $query = Doctor::with('user');

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('cpf', 'like', "%{$search}%");
            })->orWhere('crm', 'like', "%{$search}%");
        }

        return $query->orderBy('created_at', 'desc')
                     ->paginate($perPage)
                     ->withQueryString();
    }

    public function create(array $data): Doctor
    {
        return DB::transaction(function () use ($data) {

            $user = User::create($data['user_data']);

            $doctor = new Doctor([
                'crm' => $data['crm'],
            ]);
            
            $doctor->user()->associate($user);
            $doctor->save();

            return $doctor->fresh('user');
        });
    }

    public function update(Doctor $doctor, array $data): Doctor
    {
        return DB::transaction(function () use ($doctor, $data) {

            $doctorData = [];
            $userData = [];

            if (isset($data['crm'])) {
                $doctorData['crm'] = $data['crm'];
            }

            $userFields = ['name', 'email', 'cpf', 'rg', 'phone', 'birth_date', 'photo', 'password'];
            foreach ($userFields as $field) {
                if (isset($data[$field])) {
                    $userData[$field] = $data[$field];
                }
            }

            if (!empty($userData)) {
                $doctor->user->update($userData);
            }

            if (!empty($doctorData)) {
                $doctor->update($doctorData);
            }

            return $doctor->fresh('user');
        });
    }

    public function delete(Doctor $doctor): void
    {
        DB::transaction(function () use ($doctor) {

            $doctor->user->delete();
        });
    }

    public function getPatientsWithConsultations(int $doctorId)
    {
        return Patient::whereHas('appointments', function ($query) use ($doctorId) {
            $query->where('doctor_id', $doctorId)
                  ->where('status', 'completed');
        })
        ->distinct()
        ->get();
    }

    public function getPatientConsultations(int $doctorId, int $patientId)
    {
        return Consultation::whereHas('appointment', function ($query) use ($doctorId, $patientId) {
            $query->where('doctor_id', $doctorId)
                  ->where('patient_id', $patientId)
                  ->where('status', 'completed');
        })
        ->with(['appointment'])
        ->orderBy('created_at', 'desc')
        ->get();
    }
}