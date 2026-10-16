<?php

namespace App\Services;

use App\Models\Ward;
use App\Services\Interfaces\WardServiceInterface;


class WardService implements WardServiceInterface
{
    public function getAll()
    {
        return Ward::all();
    }
    public function getByDistrictId($districtId)
    {
        return Ward::where('maqh', $districtId)->get();
    }
    public function getById($id)
    {
        return Ward::find($id);
    }
}