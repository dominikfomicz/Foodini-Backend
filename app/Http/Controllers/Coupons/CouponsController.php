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
        return $this->service->getDetails($request->id_coupon_data_main);
    }

    public function addCoupon(Request $request){
        return $this->service->addCoupon($request->id_local_data_main, json_encode($request->coupon_data), json_encode($request->tags));
    }

    public function removeCoupon(Request $request){
        return $this->service->removeCoupon($request->id_coupon_data_main);
    }

    public function addCouponToFavourite(Request $request){
        return $this->service->addCouponToFavourite($request->id_coupon_data_main);
    }

    public function removeFromFavourite(Request $request){
        return $this->service->removeFromFavourite($request->id_coupon_data_main);
    }

    public function getFavouriteList(Request $request){
        return $this->service->getFavouriteList();
    }

    public function orderCoupon(Request $request){
        return $this->service->orderCoupon($request->id_coupon_data_main);
    }

    public function checkCoupon(Request $request){
        return $this->service->checkCoupon($request->unique_number);
    }

    public function getListForCity(Request $request){
        return $this->service->getListForCity($request->id_city_const_type);
    }

}
