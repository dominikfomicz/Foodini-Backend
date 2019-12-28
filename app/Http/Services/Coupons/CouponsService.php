<?php

namespace App\Http\Services\Coupons;
use App\Models\s_coupons\CouponDataMain;
use App\Models\s_coupons\LocalsRefCoupon;
use App\Models\s_coupons\CouponRefFavourite;

use App\Http\Repositories\Coupons\CouponsRepository;
use App\Models\s_coupons\CouponRefUser;
use App\Models\s_locals\LocalDataMain;
use App\Models\s_sys\HexaConstType;
use App\Models\s_tags\CouponRefMain;
use \Auth;
use DB;

class CouponsService
{

    //kom
    public function getList($id_local_data_main){
        $this->checkAllCoupons();
        $coupons = collect(CouponsRepository::getList($id_local_data_main));
        $tags = collect(CouponsRepository::getTags());
        foreach($coupons AS $coupon){
            $coupon->tags = $tags->where('id_coupon_data_main', $coupon->coupon_id)->where('is_main', 'true')->map(function ($item, $key) {
                return collect($item)->except(['id_coupon_data_main'])->all();
            });
        }
        return json_encode($coupons);
    }

    
    public function checkAllCoupons(){
        CouponRefUser::where('used', 2)->where('create_date', '<', DB::raw("CURRENT_TIMESTAMP - interval '5 minute'"))->delete();
        $coupons = CouponDataMain::where('status', '<>','0')->get();
        foreach($coupons AS $coupon){
            $used_count = CouponRefUser::where('id_coupon_data_main', $coupon->id)->count();
            if($used_count >= $coupon->amount){
                $coupon->status = 2;
                $coupon->save();
            }else{
                $coupon->status = 1;
                $coupon->save();
            }
        }

    }

    public function addCoupon($id_local_data_main, $coupon_data, $tags){

        $new_coupon = new CouponDataMain();

        $new_coupon->name = $coupon_data->name;
        $new_coupon->description = $coupon_data->description;
        $new_coupon->amount = $coupon_data->amount;
        $new_coupon->mature = $coupon_data->mature;
        $new_coupon->id_local_data_main = $id_local_data_main;
        $new_coupon->save();

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

    public function getDetails($id_coupon_data_main){
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
        $this->checkAllCoupons();
        $coupons = collect(CouponsRepository::getFavouriteList());
        $tags = collect(CouponsRepository::getTags());
        foreach($coupons AS $coupon){
            $coupon->tags = $tags->where('id_coupon_data_main', $coupon->coupon_id)->map(function ($item, $key) {
                return collect($item)->except(['id_coupon_data_main'])->all();
            });
        }
        return json_encode($coupons);
    }

    public function orderCoupon($id_coupon_data_main){
        $coupon = CouponDataMain::find($id_coupon_data_main);
        $id_user = Auth::user()->id;

        CouponRefUser::where('used', 2)->where('id_coupon_data_main', $id_coupon_data_main)->where('id_user', $id_user)->delete();

        $ref_user = new CouponRefUser();
        $ref_user->id_coupon_data_main = $id_coupon_data_main;
        $ref_user->id_user = $id_user;
        $ref_user->used = 2;

        $ref_user->save();

        $local = LocalDataMain::find($coupon->id_local_data_main);
        $hexa_id = (($ref_user->id * 345545467) % 4096);
        $hexa = HexaConstType::find($hexa_id);
        $ref_user->unique_number = $local->hexa_value.$hexa->value;
        $ref_user->save();

        $this->checkAllCoupons();

        return $ref_user->unique_number;
    }

    public function checkCoupon($unique_number){
        $user_type = Auth::user()->user_type;

        if($user_type == 1){
            $coupon = CouponRefUser::where('used', 2)->where('unique_number', $unique_number)->where('create_date', '>', DB::raw("CURRENT_TIMESTAMP - interval '5 minute'"))->first();
            if($coupon != null){

                $coupon->used = 1;
                $coupon->unique_number = NULL;
                $coupon->save();

                return 1;
            }else{

                return 0;
            }
        }else{

            return -1;
        }
    }
}
