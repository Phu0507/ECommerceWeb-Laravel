<?php

namespace App\Services\Interfaces;

interface ProvinceServiceInterface
{
    public function getAll();
    public function getById($id);
}