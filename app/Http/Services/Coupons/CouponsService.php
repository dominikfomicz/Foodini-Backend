<?php

namespace App\Http\Services\Coupons;
use App\Models\s_coupons\CouponDataMain;
use App\Models\s_coupons\LocalsRefCoupon;

use App\Http\Repositories\Coupons\CouponsRepository;

class CouponsService 
{
    public function getList(){
        return LocalsRepository::getList();
    }

    public function addCoupon(){
    //     for ($i = 1; $i <= 4; $i++) {
    //         $new_coupon = new CouponDataMain();
    //         $new_coupon->name = "Kupon test nr: ".$i;
    //         $new_coupon->description = "Opis test nr: ".$i;
    //         $new_coupon->amount = rand(20,40);
    //         $new_coupon->mature = FALSE;
    //         $new_coupon->save();

    //         $new_ref = new LocalsRefCoupon();
    //         $new_ref->id_coupon_data_main = $new_coupon->id;
    //         $new_ref->id_local_data_main = rand(1,2);
    //         $new_ref->save();

    //     }
    }
}
