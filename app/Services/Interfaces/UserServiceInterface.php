<?php

namespace App\Services\Interfaces;

interface UserServiceInterface
{
    
    public function createUser(array $data);
    public function getUserById(int $id);
    public function updateUser(int $id, array $data);
    public function deleteUser(int $id);
    public function paginateWithFilters(int $perPage, $role = null, $search = null);
    public function updateStatus(int $id, array $data);

}