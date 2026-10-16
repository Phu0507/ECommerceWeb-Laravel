<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function getAllPaginate(int $perPage, $role = null, $search = null)
    {
        $query = User::with(['province', 'district', 'ward']);

        // Lọc theo role nếu có
        if ($role !== null && $role !== '') {
            $query->where('role', $role);
        }

        // Tìm kiếm tương đối
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('phone', 'like', '%' . $search . '%');   
            });
        }

        return $query->paginate($perPage)->appends(request()->query());
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function getUserById(int $id)
    {
        return User::with(['province', 'district', 'ward'])->findOrFail($id);
    }

    public function update(int $id, array $data)
    {
        $user = User::findOrFail($id);
        return $user->update($data);
    }
    
    public function delete(int $id)
    {
        $user = User::findOrFail($id);
        return $user->delete();
    }
    public function updateStatus(int $id, array $data)
    {
        $user = User::findOrFail($id);
        return $user->update(['status' => $data['status']]);
    }
}