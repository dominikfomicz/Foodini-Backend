<?php

namespace App\Http\Services\Coupons;
use App\Models\s_coupons\CouponDataMain;
use App\Models\s_coupons\CouponRefFavourite;

use App\Http\Repositories\Coupons\CouponsRepository;
use App\Models\s_coupons\CouponRefUser;
use App\Models\s_locals\LocalDataMain;
use App\Models\s_sys\HexaConstType;
use App\Models\s_tags\CouponRefMain;
use App\Http\Services\Coupons\FilesService;
use App\Models\s_coupons\AvailableDayRef;
use App\Models\s_coupons\CouponRefDocument;
use App\Models\s_coupons\DeletedCouponStatistics;
use App\Models\s_locals\ManagerRefUser;
use App\Models\s_locals\WorkerRefUser;
use App\Models\s_sys\DocumentDataMain;
use \Auth;
use DB;

class CouponsService
{

    //kom
    public function getList($id_local_data_main){
        $this->checkAllCoupons();
        $coupons = collect(CouponsRepository::getList($id_local_data_main));
        foreach($coupons AS $coupon){
            $coupon->tags = collect(CouponsRepository::getTagsByCoupon($coupon->coupon_id))->where('is_main', TRUE);
        }
        return json_encode($coupons);
    }


    public function checkAllCoupons(){
        CouponRefUser::where('used', 2)->where('create_date', '<', DB::raw("CURRENT_TIMESTAMP - interval '5 minute'"))->delete();
        $coupons = CouponDataMain::where('status', '<>','0')->get();
        foreach($coupons AS $coupon){
            if($coupon->amount > 0){
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

    }

    public function changeAvailableHours($id_coupon_data_main, $week_day_id, $open_data){
        If (Auth::user()->user_type == -1){
            AvailableDayRef::where('id_coupon_data_main', $id_coupon_data_main)->where('id_weekday_const_type', $week_day_id)->delete();

            $new_ref = new AvailableDayRef();
            $new_ref->id_coupon_data_main = $id_coupon_data_main;
            $new_ref->id_weekday_const_type = $week_day_id;
            $new_ref->hour_from = $open_data->hour_from;
            $new_ref->hour_to = $open_data->hour_to;
            $new_ref->save();
        }
        
    }

    public function changeCoupon($id_coupon_data_main, $id_local_data_main, $coupon_data, $tags, $file_logo, $open_hours){
        If (Auth::user()->user_type == -1){
            if($id_coupon_data_main == -1){
                $new_coupon = new CouponDataMain();
            }else{
                $new_coupon = CouponDataMain::find($id_coupon_data_main);
            }
    
            $new_coupon->name = $coupon_data->name;
            $new_coupon->description = $coupon_data->description;
            $new_coupon->amount = $coupon_data->amount;
            $new_coupon->mature = $coupon_data->mature;
            $new_coupon->id_local_data_main = $id_local_data_main;
            $new_coupon->save();
    
            CouponRefMain::where('id_coupon_data_main', $new_coupon->id)->delete();
    
            foreach($tags AS $tag){
                $this->addTagToCoupon($new_coupon->id, $tag->id, $tag->priority_status);
            }
    
            foreach($open_hours AS $open_hour){
                $this->changeAvailableHours($new_coupon->id, $open_hour->id_week_day, $open_hour);
            }
    
            $files = new FilesService();
            $file_logo = $files->addLogo($new_coupon->id, $file_logo);
        }
    }

    public function addTagToCoupon($id_coupon_data_main, $id_tag_data_main, $priority_status){
        If (Auth::user()->user_type == -1){
            $new_ref = new CouponRefMain();
            $new_ref->id_coupon_data_main = $id_coupon_data_main;
            $new_ref->id_tag_data_main = $id_tag_data_main;
            $new_ref->priority_status = $priority_status;
            $new_ref->save();
        }
            
    }

    public function getDetails($id_coupon_data_main){
        $id_user = Auth::user()->id;

        $coupon = collect(CouponsRepository::getDetails($id_coupon_data_main))->first();
        $coupon->tags = collect(CouponsRepository::getTagsByCoupon($coupon->coupon_id));
        $coupon->available_hours = collect(CouponsRepository::getAvailableHours($coupon->coupon_id));
        $already_used = CouponRefUser::where('used', 1)->where('id_coupon_data_main', $id_coupon_data_main)
                                        ->where('id_user', $id_user)->where('create_date', '>', DB::raw("CURRENT_TIMESTAMP - interval '1 day'"))->first();
        if($already_used != null){
            $coupon->already_used = TRUE;
        }else{
            $coupon->already_used = FALSE;
        }


        $stats_coupon = CouponDataMain::find($id_coupon_data_main);
        $stats_coupon->show_detail_count = $stats_coupon->show_detail_count + 1;
        $stats_coupon->save();

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
        If (Auth::user()->user_type == -1){
            $coupon = CouponDataMain::find($id_coupon_data_main);
            $local = LocalDataMain::find($coupon->id_local_data_main);
            $document_refs = CouponRefDocument::where('id_coupon_data_main', $id_coupon_data_main)->get();
            foreach($document_refs AS $document_ref ){
                DocumentDataMain::find($document_ref->id_document_data_main)->delete();
                $document_ref->delete();
            }

            AvailableDayRef::where('id_coupon_data_main', $id_coupon_data_main)->delete();
            CouponRefMain::where('id_coupon_data_main', $id_coupon_data_main)->delete();

            $count_used = CouponRefUser::where('id_coupon_data_main', $id_coupon_data_main)->where('used', 1)->count();

            $new_statics = new  DeletedCouponStatistics();
            $new_statics->id_local_data_main = $local->id;
            $new_statics->local_name = $local->name;
            $new_statics->used_coupons_count = $count_used;
            $new_statics->id_city_const_type = $local->id_city_const_type;
            $new_statics->coupon_name = $coupon->name;
            $new_statics->save();

            CouponRefUser::where('id_coupon_data_main', $id_coupon_data_main)->where('used', 1)->delete();
            $coupon->delete();
        }
    }


    public function getFavouriteList(){
        $this->checkAllCoupons();
        $coupons = collect(CouponsRepository::getFavouriteList());
        foreach($coupons AS $coupon){
            $coupon->tags = collect(CouponsRepository::getTagsByCoupon($coupon->coupon_id))->where('is_main', TRUE);
        }
        return json_encode($coupons);
    }

    public function orderCoupon($id_coupon_data_main){
        $coupon = CouponDataMain::find($id_coupon_data_main);
        $id_user = Auth::user()->id;

        $already_used = CouponRefUser::where('used', 1)->where('id_coupon_data_main', $id_coupon_data_main)
                                        ->where('id_user', $id_user)->where('create_date', '>', DB::raw("CURRENT_TIMESTAMP - interval '1 day'"))->first();
        If($already_used != null){
            return json_encode("Wykorzystany");
        }else{
            CouponRefUser::where('used', 2)->where('id_coupon_data_main', $id_coupon_data_main)->where('id_user', $id_user)->delete();

            $ref_user = new CouponRefUser();
            $ref_user->id_coupon_data_main = $id_coupon_data_main;
            $ref_user->id_user = $id_user;
            $ref_user->used = 2;
    
            $ref_user->save();
    
            $local = LocalDataMain::find($coupon->id_local_data_main);
            $hexa_id = (($ref_user->id * 345545467) % 4093);
            $hexa = HexaConstType::find($hexa_id);
            $ref_user->unique_number = $local->hexa_value.$hexa->value;
            $ref_user->save();
    
            $this->checkAllCoupons();
    
            return json_encode($ref_user->unique_number);
        }
        
    }

    public function checkCoupon($unique_number){
        $user_type = Auth::user()->user_type;
        $id_user = Auth::user()->id;
        if($user_type == 2 || $user_type == 3){
            $coupon = CouponRefUser::where('used', 2)->where('unique_number', DB::raw("UPPER('{$unique_number}')"))->where('create_date', '>', DB::raw("CURRENT_TIMESTAMP - interval '5 minute'"))->first();
            if($coupon != null){
                $coupon_data = CouponDataMain::find($coupon->id_coupon_data_main);
                $user_ref_local = WorkerRefUser::where('id_local_data_main', $coupon_data->id_local_data_main)->where('id_user', $id_user)->first();
                $user_ref_menago = ManagerRefUser::where('id_local_data_main', $coupon_data->id_local_data_main)->where('id_user', $id_user)->first();
                if($user_ref_local != null || $user_ref_menago != null){
                    $coupon->used = 1;
                    $coupon->unique_number = NULL;
                    $coupon->save();
                    return 1;

                }else{
                    
                    return 0;
                }
                

                
            }else{
                return 0;
                
            }
        }else{

            return -1;
        }
    }

    public function getCouponsByCity($id_city_const_type){
        $this->checkAllCoupons();
        $coupons = collect(CouponsRepository::getCouponsByCity($id_city_const_type));
        foreach($coupons AS $coupon){
            $coupon->tags = collect(CouponsRepository::getTagsByCoupon($coupon->coupon_id))->where('is_main', true);
        }
        return json_encode($coupons);
    }


    public function getDetailsEdit($id_coupon_data_main){
        $coupon = collect(CouponsRepository::getDetailsEdit($id_coupon_data_main))->first();
        $coupon->tags = collect(CouponsRepository::getTagsByCoupon($coupon->id_coupon_data_main));
        $coupon->available_hours = collect(CouponsRepository::getAvailableHoursEdit($coupon->id_coupon_data_main));


        return json_encode($coupon);
    }

    public function getSupportCouponsByCity($id_city_const_type){
        $coupons = collect(CouponsRepository::getSupportCouponsByCity($id_city_const_type));
        return json_encode($coupons);
    }
}
