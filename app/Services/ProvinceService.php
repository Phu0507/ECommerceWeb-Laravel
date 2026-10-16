<?php

namespace App\Services;

use App\Models\Province;
use App\Services\Interfaces\ProvinceServiceInterface;


class ProvinceService implements ProvinceServiceInterface
{
    public function getAll()
    {
        return Province::all();
    }
    public function getById($id)
    {
        return Province::find($id);
    }
}