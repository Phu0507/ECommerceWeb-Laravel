<?php

namespace App\Repositories\Interfaces;

interface UserRepositoryInterface
{
    public function getAllPaginate(int $perPage, $role = null, $search = null);
    public function create(array $data);
    public function getUserById(int $id);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function updateStatus(int $id, array $data);
}