<?php

namespace App\Services\Interfaces;

interface WardServiceInterface
{
    public function getAll();
    public function getByDistrictId($districtId);
    public function getById($id);
}