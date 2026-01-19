<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\ReceptionistRepositoryInterface;
use App\Domain\Exceptions\ReceptionistNotFoundException;
use App\Domain\Models\Receptionist;
use App\Domain\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ReceptionistRepository implements ReceptionistRepositoryInterface
{
    public function findById(int $id): Receptionist
    {
        $receptionist = Receptionist::with('user')->find($id);
        
        if (!$receptionist) {
            throw new ReceptionistNotFoundException();
        }
        
        return $receptionist;
    }

    public function paginate(int $perPage = 8): LengthAwarePaginator
    {
        return Receptionist::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function search(?string $search, int $perPage = 8): LengthAwarePaginator
    {
        $query = Receptionist::with('user');

        if ($search) {
            $query->whereHas('user', function ($userQuery) use ($search) {
                $userQuery->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('cpf', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')
                     ->paginate($perPage)
                     ->withQueryString();
    }

    public function create(array $data): Receptionist
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['user_data']['name'],
                'email' => $data['user_data']['email'],
                'cpf' => $data['user_data']['cpf'],
                'phone' => $data['user_data']['phone'],
                'password' => bcrypt($data['user_data']['password']),
                'birth_date' => $data['user_data']['birth_date'],
                'photo' => $data['user_data']['photo'] ?? null,
            ]);

            $receptionist = Receptionist::create([
                'user_id' => $user->id,
                'registration_number' => $data['registration_number'],
            ]);

            return $receptionist->load('user');
        });
    }

    public function update(Receptionist $receptionist, array $data): Receptionist
    {
        return DB::transaction(function () use ($receptionist, $data) {
            $receptionist->user->update([
                'name' => $data['name'] ?? $receptionist->user->name,
                'email' => $data['email'] ?? $receptionist->user->email,
                'phone' => $data['phone'] ?? $receptionist->user->phone,
                'photo' => $data['photo'] ?? $receptionist->user->photo,
            ]);

            return $receptionist->fresh(['user']);
        });
    }

    public function delete(Receptionist $receptionist): void
    {
        DB::transaction(function () use ($receptionist) {
            $receptionist->user->delete();
        });
    }
}