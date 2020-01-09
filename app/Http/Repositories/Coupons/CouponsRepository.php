<?php

namespace App\Http\Repositories\Coupons;
use Illuminate\Support\Facades\DB;
use \Auth;


class CouponsRepository
{
    public static function getList($id_local_data_main){
        $day_of_week = date('N');
        if($day_of_week == 7){
            $day_of_week = 0;
        }
        $id_user = Auth::user()->id;
        $query = "WITH used_counter AS (
                            SELECT
                                COUNT(*) AS used_counter,
                                id_coupon_data_main
                            FROM s_coupons.t_coupon_ref_user
                            GROUP BY id_coupon_data_main
                    )

                    SELECT
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
                        END AS status,
                        CASE WHEN c.amount = -1 THEN c.amount
                            WHEN used_counter.used_counter IS NOT NULL THEN c.amount - used_counter.used_counter
                        ELSE c.amount
                        END AS coupon_left
                    FROM s_coupons.t_coupon_data_main c
                    LEFT JOIN s_locals.t_local_data_main l ON l.id = c.id_local_data_main
                    LEFT JOIN s_coupons.t_coupon_ref_favourite f ON f.id_user = {$id_user} AND f.id_coupon_data_main = c.id
                    LEFT JOIN used_counter ON used_counter.id_coupon_data_main = c.id
                    LEFT JOIN s_coupons.t_available_day_ref o ON o.id_coupon_data_main = c.id
                                                            AND o.id_weekday_const_type = {$day_of_week}
                                                            AND hour_from <= current_time
                                                            AND hour_to >= current_time
                    WHERE c.id_local_data_main = {$id_local_data_main} AND o.id IS NOT NULL;
                    ";
        return DB::select($query);
    }

    public static function getMainTags(){
        $query = "SELECT
                        r.id_coupon_data_main,
                        t.id,
                        t.name
                    FROM s_tags.t_coupon_ref_main r
                    LEFT JOIN s_tags.t_tag_data_main t ON t.id = r.id_tag_data_main
                    WHERE r.priority_status = TRUE;";
        return DB::select($query);
    }

    //com
    public static function getDetails($id_coupon_data_main){
        $day_of_week = date('N');
        if($day_of_week == 7){
            $day_of_week = 0;
        }
        $id_user = Auth::user()->id;
        $query = "
                    SELECT
                        c.id AS coupon_id,
                        c.description,
                        c.amount,
                        c.name,
                        CASE WHEN f.id IS NOT NULL THEN TRUE
                        ELSE FALSE
                        END AS is_favouirite,
                        CASE WHEN o.id IS NOT NULL THEN TRUE
                        ELSE FALSE
                        END AS as_available
                    FROM s_coupons.t_coupon_data_main c
                    LEFT JOIN s_coupons.t_coupon_ref_favourite f ON f.id_user = {$id_user} AND f.id_coupon_data_main = c.id
                    LEFT JOIN s_coupons.t_available_day_ref o ON o.id_coupon_data_main = c.id
                                                            AND o.id_weekday_const_type = {$day_of_week}
                                                            AND hour_from <= current_time
                                                            AND hour_to >= current_time
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

    public static function getAvailableHours($id_coupon_data_main){

        //
        $query = "SELECT
                        d.id_weekday_const_type AS id_day,
                        to_char(d.hour_from, 'HH24:MI') AS hour_from,
                        to_char(d.hour_to, 'HH24:MI') AS hour_to
                    FROM s_coupons.t_available_day_ref d
                    LEFT JOIN s_locals.t_weekday_const_type c ON c.id = d.id_weekday_const_type
                    WHERE d.id_coupon_data_main = {$id_coupon_data_main}
                    ORDER BY c.order_column;";

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
                    LEFT JOIN s_locals.t_local_data_main l ON l.id = c.id_local_data_main
                    WHERE f.id_user = {$id_user};
                    ";
        return DB::select($query);
    }

    public static function getCouponsByCity($id_city_const_type){
        $day_of_week = date('N');
        if($day_of_week == 7){
            $day_of_week = 0;
        }
        $id_user = Auth::user()->id;
        $query = "WITH used_counter AS (
                            SELECT
                                COUNT(*) AS used_counter,
                                id_coupon_data_main
                            FROM s_coupons.t_coupon_ref_user
                            GROUP BY id_coupon_data_main
                    )

                    SELECT
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
                        END AS status,
                        CASE WHEN c.amount = -1 THEN c.amount
                            WHEN used_counter.used_counter IS NOT NULL THEN c.amount - used_counter.used_counter
                        ELSE c.amount
                        END AS coupon_left
                    FROM s_locals.t_local_data_main l 
                    LEFT JOIN s_coupons.t_coupon_data_main c ON l.id = c.id_local_data_main
                    LEFT JOIN s_coupons.t_coupon_ref_favourite f ON f.id_user = {$id_user} AND f.id_coupon_data_main = c.id
                    LEFT JOIN used_counter ON used_counter.id_coupon_data_main = c.id
                    LEFT JOIN s_coupons.t_available_day_ref o ON o.id_coupon_data_main = c.id
                                                            AND o.id_weekday_const_type = {$day_of_week}
                                                            AND hour_from <= current_time
                                                            AND hour_to >= current_time
                    WHERE l.id_city_const_type = {$id_city_const_type} AND c.status = 1 AND o.id IS NOT NULL;
                    ";
        return DB::select($query);
    }
}
