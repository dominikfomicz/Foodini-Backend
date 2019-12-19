<?php

namespace App\Http\Services\Coupons;
use App\Models\s_coupons\CouponDataMain;
use App\Models\s_coupons\LocalsRefCoupon;

use App\Http\Repositories\Coupons\CouponsRepository;
use App\Models\s_tags\CouponRefMain;

class CouponsService 
{
    public function getList($id_local_data_main){
        $coupons = collect(CouponsRepository::getList($id_local_data_main));
        $tags = collect(CouponsRepository::getTags());
        foreach($coupons AS $coupon){
            $coupon->tags = $tags->where('id_coupon_data_main', $coupon->coupon_id)->map(function ($item, $key) {
                return collect($item)->except(['id_coupon_data_main'])->all();
            });
        }
        return json_encode($coupons);
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

    public function addTagsToCoupon(){
        // $coupons = CouponDataMain::all();
        // foreach($coupons AS $coupon){
        //     $boolean = TRUE;
        //     for ($i = 0; $i <= 3; $i++) {
        //         $new_ref = new CouponRefMain();
        //         $new_ref->id_coupon_data_main = $coupon->id;
        //         $new_ref->id_tag_data_main = rand(1,12);
        //         $new_ref->priority_status = $boolean;
        //         if($i == 2){
        //             $boolean = false;
        //         }
        //         $new_ref->save();
        //     }
        // }
    }

    public function getDetails($id_local_data_main, $id_coupon_data_main){
        $coupon = collect(CouponsRepository::getDetails($id_coupon_data_main))->first();
        $coupon->tags = collect(CouponsRepository::getTagsByCoupon($coupon->coupon_id));
        return json_encode($coupon);
    }
}
