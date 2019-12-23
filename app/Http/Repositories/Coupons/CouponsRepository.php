<?php

namespace App\Http\Repositories\Coupons;
use Illuminate\Support\Facades\DB;
use \Auth;


class CouponsRepository 
{
    public static function getList($id_local_data_main){
        $id_user = Auth::user()->id;
        $query = "SELECT 
                        c.id AS coupon_id,
                        l.id AS local_id,
                        c.name AS coupon_name,
                        c.mature,
                        l.delivery,
                        l.eat_in_local,
                        l.pick_up_local,
                        c.amount,
                        CASE WHEN f.id IS NOT NULL THEN TRUE
                        ELSE FALSE 
                        END AS is_favouirite,
                        l.name AS local_name,
                        CASE WHEN c.status = 1 THEN TRUE
                        ELSE FALSE
                        END AS status	
                    
                    FROM s_coupons.t_local_ref_coupon r
                    LEFT JOIN s_coupons.t_coupon_data_main c ON c.id = r.id_coupon_data_main
                    LEFT JOIN s_locals.t_local_data_main l ON l.id = r.id_local_data_main
                    LEFT JOIN s_coupons.t_coupon_ref_favourite f ON f.id_user = {$id_user} AND f.id_coupon_data_main = c.id
                    WHERE r.id_local_data_main = {$id_local_data_main}                                        ;
                    ";
        return DB::select($query);
    }

    public static function getTags(){
        $query = "SELECT 
                        r.id_coupon_data_main,
                        t.id,
                        t.name,
                        r.priority_status AS is_main
                    FROM s_tags.t_coupon_ref_main r
                    LEFT JOIN s_tags.t_tag_data_main t ON t.id = r.id_tag_data_main;";
        return DB::select($query);
    }

    public static function getDetails($id_coupon_data_main){
        $id_user = Auth::user()->id;
        $query = "
                    SELECT 
                        c.id AS coupon_id,
                        c.description,
                        c.amount,
                        c.name,
                        CASE WHEN f.id IS NOT NULL THEN TRUE
                        ELSE FALSE 
                        END AS is_favouirite
                    FROM s_coupons.t_coupon_data_main c
                    LEFT JOIN s_coupons.t_coupon_ref_favourite f ON f.id_user = {$id_user} AND f.id_coupon_data_main = c.id
                    WHERE c.id = {$id_coupon_data_main};
                    ";

        return DB::select($query);
    }

    public static function getTagsByCoupon($coupon_id){
        $query = "SELECT 
                        t.id,
                        t.name,
                        r.priority_status AS is_main
                    FROM s_tags.t_coupon_ref_main r
                    LEFT JOIN s_tags.t_tag_data_main t ON t.id = r.id_tag_data_main
                    WHERE r.id_coupon_data_main = {$coupon_id};";
        return DB::select($query);
    }

    public static function getFavouriteList(){
        $id_user = Auth::user()->id;
        $query = "SELECT 
                        c.id AS coupon_id,
                        l.id AS local_id,
                        l.name AS local_name,
                        c.name AS coupon_name,
                        c.mature,
                        l.delivery,
                        l.eat_in_local,
                        l.pick_up_local,
                        c.amount,
                        CASE WHEN f.id IS NOT NULL THEN TRUE
                        ELSE FALSE 
                        END AS is_favouirite,
                        l.name AS local_name,
                        CASE WHEN c.status = 1 THEN TRUE
                        ELSE FALSE
                        END AS status	
                    
                    FROM  s_coupons.t_coupon_ref_favourite f
                    LEFT JOIN s_coupons.t_coupon_data_main c ON c.id = f.id_coupon_data_main
                    LEFT JOIN s_coupons.t_local_ref_coupon r ON r.id = f.id_coupon_data_main
                    LEFT JOIN s_locals.t_local_data_main l ON l.id = r.id_local_data_main
                    WHERE f.id_user = {$id_user};
                    ";
        return DB::select($query);
    }
}
