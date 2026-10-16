<?php

namespace App\Services;

use App\Models\User;
use App\Services\Interfaces\UserServiceInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserService implements UserServiceInterface
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    public function createUser(array $data)
    {
        return $this->userRepository->create($data);
    }

    public function getUserById(int $id): ?User
    {
        return $this->userRepository->getUserById($id);
    }

    public function updateUser(int $id, array $data)
    {
        return $this->userRepository->update($id, $data);
    }

    public function deleteUser(int $id)
    {
        return $this->userRepository->delete($id);
    }
    
    public function paginateWithFilters(int $perPage, $role = null, $search = null)
    {
        return $this->userRepository->getAllPaginate($perPage, $role, $search);
    }

    public function updateStatus(int $id, array $data)
    {
        return $this->userRepository->updateStatus($id, $data);
    }
}