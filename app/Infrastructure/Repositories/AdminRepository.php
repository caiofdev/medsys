<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\AdminRepositoryInterface;
use App\Domain\Exceptions\AdminNotFoundException;
use App\Domain\Models\Admin;
use App\Domain\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

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

        return $admin->fresh($user);
    }

    public function update(Admin $admin, array $data): Admin
    {
        $userData = collect($data)->except(['is_master', 'password'])->toArray();

        if (isset($data['password'])) {
            $userData['password'] = $data['password'];
        }

        $admin->user->update($userData);

        if (isset($data['is_master'])) {
            $admin->is_master = $data['is_master'];
            $admin->save();
        }

        return $admin->fresh('user');
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