<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\PatientRepositoryInterface;
use App\Domain\Exceptions\PatientNotFoundException;
use App\Domain\Models\Patient;
use Illuminate\Pagination\LengthAwarePaginator;

class PatientRepository implements PatientRepositoryInterface
{
    public function findById(int $id): Patient
    {
        $patient = Patient::find($id);
        
        if (!$patient) {
            throw new PatientNotFoundException();
        }
        
        return $patient;
    }

    public function paginate(int $perPage = 8): LengthAwarePaginator
    {
        return Patient::orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function search(?string $search, int $perPage = 8): LengthAwarePaginator
    {
        $query = Patient::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('cpf', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')
                     ->paginate($perPage)
                     ->withQueryString();
    }

    public function create(array $data): Patient
    {
        return Patient::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'cpf' => $data['cpf'],
            'phone' => $data['phone'],
            'gender' => $data['gender'],
            'birth_date' => $data['birth_date'],
            'emergency_contact' => $data['emergency_contact'],
            'medical_history' => $data['medical_history'] ?? '',
        ]);
    }

    public function update(Patient $patient, array $data): Patient
    {
        $patient->update([
            'name' => $data['name'] ?? $patient->name,
            'email' => $data['email'] ?? $patient->email,
            'phone' => $data['phone'] ?? $patient->phone,
            'gender' => $data['gender'] ?? $patient->gender,
            'emergency_contact' => $data['emergency_contact'] ?? $patient->emergency_contact,
            'medical_history' => $data['medical_history'] ?? $patient->medical_history,
        ]);

        return $patient->fresh();
    }

    public function delete(Patient $patient): void
    {
        $patient->delete();
    }

    public function searchForAutocomplete(string $query, int $limit = 10)
    {
        return Patient::where('name', 'LIKE', "%{$query}%")
            ->orWhere('cpf', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->select('id', 'name', 'cpf', 'email', 'phone')
            ->limit($limit)
            ->get();
    }
}