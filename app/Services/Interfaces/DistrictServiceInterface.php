<?php

namespace App\Services\Interfaces;

interface DistrictServiceInterface
{
    public function getAll();
    public function getByProvinceId(int $provinceId);
}