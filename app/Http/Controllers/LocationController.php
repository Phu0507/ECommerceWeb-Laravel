<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\DistrictServiceInterface as DistrictServiceInterface;
use App\Http\Controllers\Controller;
use App\Services\Interfaces\WardServiceInterface as WardServiceInterface;
use Illuminate\Http\Request;


class LocationController extends Controller
{
    protected $districtService;
    protected $wardService;
    public function __construct(DistrictServiceInterface $districtService, WardServiceInterface $wardService)
    {
        $this->districtService = $districtService;
        $this->wardService = $wardService;
    }
    public function getDistricts(Request $request)
    {
        $provinceId = $request->input('provinceId');
        return $this->districtService->getByProvinceId($provinceId);
        dd($this->districtService->getByProvinceId($provinceId));
    }

    public function getWards(Request $request)
    {
        
        $districtId = $request->input('districtId');
        return $this->wardService->getByDistrictId($districtId);
        dd($this->wardService->getByDistrictId($districtId));
    }
}