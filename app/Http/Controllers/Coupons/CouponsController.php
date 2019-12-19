<?php

namespace App\Http\Controllers\Coupons;

use App\Http\Controllers\Controller;
use App\Http\Services\Coupons\CouponsService;
use Illuminate\Http\Request;

class CouponsController extends Controller
{
    private $service;

    public function __construct(CouponsService $service){
        $this->service = $service;
    }

    public function getList(Request $request){
        return $this->service->getList($request->id_local_data_main);
    }

    public function getDetails(Request $request){
        return $this->service->getDetails($request->id_local_data_main, $request->id_coupon_data_main);
    }

    public function addCoupon(){
        return $this->service->addCoupon();
    }
}
