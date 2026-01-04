<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\AdminRepositoryInterface;
use App\Domain\Exceptions\AdminNotFoundException;
use App\Domain\Models\Admin;
use App\Domain\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AdminRepository implements AdminRepositoryInterface
{
    public function findById(int $id): Admin
    {
        $admin = Admin::with('user')->find($id);

        if(!$admin) {
            throw new AdminNotFoundException();
        }

        return $admin;
    }

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return Admin::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function countMasters(): int
    {
        return Admin::where('is_master', true)->count();
    }

    public function create(array $data): Admin
    {
        $user = User::create($data['user_data']);

        $admin = new Admin([
            'is_master' => $data['is_master'] ?? false,
        ]);

        $admin->user()->associate($user);
        $admin->save();

        return $admin->fresh('user');
    }

    public function update(Admin $admin, array $data): Admin
    {
        return DB::transaction(function () use ($admin, $data) {

            $userData = [];
            $userFields = ['name', 'email', 'phone', 'photo'];
            
            foreach ($userFields as $field) {
                if (array_key_exists($field, $data)) {
                    $userData[$field] = $data[$field];
                }
            }

            if (!empty($userData)) {
                $admin->user->update($userData);
            }

            return $admin->fresh('user');
        });
    }

    public function delete(Admin $admin): void
    {
        $admin->user->delete();
    }

    public function search(?string $search, int $perPage = 10): LengthAwarePaginator
    {
        $query = Admin::with('user');

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('cpf', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')
                     ->paginate($perPage);
    }
}