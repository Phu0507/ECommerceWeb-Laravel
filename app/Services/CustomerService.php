<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

/**
 * 
 * @package App\Services
 */
class CustomerService
{
    public function getAllPaginate(int $perPage, $search = null)
    {
        $query = Customer::with(['province', 'district', 'ward']);
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
        return Customer::create($data);
    }

    public function getUserById(int $id)
    {
        return Customer::with(['province', 'district', 'ward'])->findOrFail($id);
    }

    public function update(int $id, array $data)
    {
        $user = Customer::findOrFail($id);
        return $user->update($data);
    }
    
    public function delete(int $id)
    {
        $user = Customer::findOrFail($id);
        return $user->delete();
    }
    public function updateStatus(int $id, array $data)
    {
        $user = Customer::findOrFail($id);
        return $user->update(['status' => $data['status']]);
    }
}