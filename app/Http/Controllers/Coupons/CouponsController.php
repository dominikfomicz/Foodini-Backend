<?php

namespace App\Http\Controllers\Coupons;

use App\Http\Controllers\Controller;
use App\Http\Services\Coupons\CouponsService;

class CouponsController extends Controller
{
    private $service;

    public function __construct(CouponsService $service){
        $this->service = $service;
    }

    public function getList(){
        return $this->service->getList();
    }

    public function addCoupon(){
        return $this->service->addCoupon();
    }
}
