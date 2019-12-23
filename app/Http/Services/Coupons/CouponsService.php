<?php

namespace App\Http\Services\Coupons;
use App\Models\s_coupons\CouponDataMain;
use App\Models\s_coupons\LocalsRefCoupon;
use App\Models\s_coupons\CouponRefFavourite;

use App\Http\Repositories\Coupons\CouponsRepository;
use App\Models\s_tags\CouponRefMain;
use \Auth;

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

    public function addCoupon($id_local_data_main, $coupon_data, $tags){

        $new_coupon = new CouponDataMain();

        $new_coupon->name = $coupon_data->name;
        $new_coupon->description = $coupon_data->description;
        $new_coupon->amount = $coupon_data->amount;
        $new_coupon->mature = $coupon_data->mature;
        $new_coupon->save();

        $new_ref = new LocalsRefCoupon();
        $new_ref->id_coupon_data_main = $new_coupon->id;
        $new_ref->id_local_data_main = $id_local_data_main;
        $new_ref->save();

        foreach($tags AS $tag){
            $this->addTagToCoupon($new_coupon->id, $tag->id, $tag->priority_status);
        }
    }

    public function addTagToCoupon($id_coupon_data_main, $id_tag_data_main, $priority_status){
            $new_ref = new CouponRefMain();
            $new_ref->id_coupon_data_main = $id_coupon_data_main;
            $new_ref->id_tag_data_main = $id_tag_data_main;
            $new_ref->priority_status = $priority_status;
            $new_ref->save();
    }

    public function getDetails($id_local_data_main, $id_coupon_data_main){
        $coupon = collect(CouponsRepository::getDetails($id_coupon_data_main))->first();
        $coupon->tags = collect(CouponsRepository::getTagsByCoupon($coupon->coupon_id));
        return json_encode($coupon);
    }

    public function addCouponToFavourite($id_coupon_data_main){
        $id_user = Auth::user()->id;
        $favourite = new CouponRefFavourite();
        $favourite->id_user = $id_user;
        $favourite->id_coupon_data_main = $id_coupon_data_main;
        $favourite->save();
    }

    public function removeFromFavourite($id_coupon_data_main){
        $id_user = Auth::user()->id;
        $favourite = CouponRefFavourite::where('id_user', $id_user)->where('id_coupon_data_main', $id_coupon_data_main)->delete();

    }

    public function removeCoupon($id_coupon_data_main){
        //to do
    }

    public function getFavouriteList(){
        $coupons = collect(CouponsRepository::getFavouriteList());
        $tags = collect(CouponsRepository::getTags());
        foreach($coupons AS $coupon){
            $coupon->tags = $tags->where('id_coupon_data_main', $coupon->coupon_id)->map(function ($item, $key) {
                return collect($item)->except(['id_coupon_data_main'])->all();
            });
        }
        return json_encode($coupons);
    }
}
