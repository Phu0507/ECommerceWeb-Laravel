<?php

namespace App\Services;

use App\Models\District;
use App\Services\Interfaces\DistrictServiceInterface;


class DistrictService implements DistrictServiceInterface
{
    public function getAll()
    {
        return District::all();
    }
    public function getByProvinceId(int $provinceId)
    {
        return District::where('matp', $provinceId)->get();
    }
}