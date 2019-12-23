<?php

namespace App\Http\Repositories\Locals;
use Illuminate\Support\Facades\DB;

class LocalsRepository 
{
    public static function getList($id_city_const_type){
        $day_of_week = date('N');
        if($day_of_week == 7){
            $day_of_week = 0;
        }
        $query = "SELECT 
                        l.name,
                        l.id AS local_id,
                        to_char(o.local_hour_from, 'HH24:MI') AS open_from,
                        to_char(o.local_hour_to, 'HH24:MI') AS open_to,
                        o.status_closed AS is_closed,
                        FALSE AS is_favourite,
                        l.delivery,
                        l.eat_in_local,
                        l.pick_up_local		
                    
                    FROM s_locals.t_local_data_main l
                    LEFT JOIN s_locals.t_open_ref_main o ON o.id_local_data_main = l.id
                                                            AND id_weekday_const_type = {$day_of_week}
                    WHERE l.id_city_const_type = {$id_city_const_type}                                        ;
                    ";
        return DB::select($query);
    }

    public static function getTags(){
        $query = "SELECT 
                        r.id_local_data_main,
                        t.id,
                        t.name,
                        r.priority_status AS is_main
                    FROM s_tags.t_local_ref_main r
                    LEFT JOIN s_tags.t_tag_data_main t ON t.id = r.id_tag_data_main;";
        return DB::select($query);
    }

    public static function getDetails($id_local_data_main){
        $day_of_week = date('N');
        if($day_of_week == 7){
            $day_of_week = 0;
        }

        $query = "WITH counter_fav AS (SELECT
                                        COUNT(*) AS favourite_count
                                    FROM s_locals.t_local_ref_favourite
                                    WHERE id_local_data_main = {$id_local_data_main}

                        ),
                        counter_coupons AS (SELECT
                                        COUNT(*) AS coupons_count
                                    FROM s_coupons.t_local_ref_coupon
                                    WHERE id_local_data_main = {$id_local_data_main}
                        )
                    SELECT 
                        l.name,
                        l.id AS local_id,
                        CASE WHEN o.local_hour_from < CURRENT_TIME AND o.local_hour_to > CURRENT_TIME THEN TRUE
                        ELSE FALSE
                        END AS local_open_status,
                        CASE WHEN o.kitchen_hour_from < CURRENT_TIME AND o.kitchen_hour_to > CURRENT_TIME THEN TRUE
                        ELSE FALSE
                        END AS kitchen_open_status,
                        CASE WHEN o.delivery_hour_from < CURRENT_TIME AND o.delivery_hour_to > CURRENT_TIME THEN TRUE
                        ELSE FALSE
                        END AS delivery_open_status,
                        o.status_closed AS is_closed,
                        FALSE AS is_favourite,
                        l.delivery,
                        l.eat_in_local,
                        l.pick_up_local,
                        l.address,
                        l.description,
                        l.facebook_url,
                        l.instagram_url,
                        counter_fav.favourite_count,
                        counter_coupons.coupons_count,
                        l.cash_payment,
                        l.creditcards_payment,
                        l.contactless_payment,
                        l.blik_payment,
                        l.phone_number
                    FROM s_locals.t_local_data_main l
                    LEFT JOIN s_locals.t_open_ref_main o ON o.id_local_data_main = l.id
                                                            AND id_weekday_const_type = {$day_of_week}
                    LEFT JOIN counter_fav ON 0=0
                    LEFT JOIN counter_coupons ON 0=0
                    WHERE l.id = {$id_local_data_main};
                    ";

        return DB::select($query);
    }

    public static function getTagsByLocal($local_id){
        $query = "SELECT 
                        t.id,
                        t.name,
                        r.priority_status AS is_main
                    FROM s_tags.t_local_ref_main r
                    LEFT JOIN s_tags.t_tag_data_main t ON t.id = r.id_tag_data_main
                    WHERE r.id_local_data_main = {$local_id};";
        return DB::select($query);
    }

    public static function getWorkHours($id_local_data_main){

        $query = "SELECT 
                        o.id_weekday_const_type AS id_day,
                        CONCAT_WS(' - ', to_char(o.local_hour_from, 'HH24:MI'), to_char(o.local_hour_to, 'HH24:MI')) AS open_hours
                    FROM s_locals.t_open_ref_main o 
                    WHERE o.id_local_data_main = {$id_local_data_main};";

        return DB::select($query);
    }
}
