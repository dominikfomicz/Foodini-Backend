<?php

namespace App\Http\Repositories\Manager;
use Illuminate\Support\Facades\DB;
use \Auth;

class ManagerRepository
{

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

    public static function getLocalsByManager($id_user){
        $query = "SELECT
                        l.name,
                        l.id AS local_id,
                        to_char(o.local_hour_from, 'HH24:MI') AS open_from,
                        to_char(o.local_hour_to, 'HH24:MI') AS open_to,
                        o.status_closed AS is_closed,
                        l.delivery,
                        l.eat_in_local,
                        l.pick_up_local

                    FROM s_locals.t_manager_ref_user r
                    LEFT JOIN s_locals.t_local_data_main l ON l.id = r.id_local_data_main
                    LEFT JOIN s_locals.t_open_ref_main o ON o.id_local_data_main = l.id
                                                            AND o.id_weekday_const_type = CASE WHEN CURRENT_TIME < '06:00' THEN extract(dow from CURRENT_TIMESTAMP) - 1
                                                                                            ELSE extract(dow from CURRENT_TIMESTAMP)
                                                                                            END
                    WHERE r.id_user = {$id_user};
                    ";
        return DB::select($query);
    }

    public static function getLocalStatistics($id_local_data_main){
        $query = "WITH fav_count AS (
                                SELECT
                                    COUNT(*) AS fav_count
                                FROM s_locals.t_local_ref_favourite
                                WHERE id_local_data_main = {$id_local_data_main}
                    ),
                users_count AS (
                            SELECT
                                COUNT(*) AS users_count
                            FROM users
                            WHERE last_login_date >= date_trunc('month', current_date)
                    )

                SELECT
                    l.name AS local_name,
                    l.show_detail_count,
                    l.show_facebook_count,
                    l.show_menu_count,
                    l.show_instagram_count,
                    l.show_phonenumber_count,
                    fav_count.fav_count,
                    users_count.users_count,
                    l.id_city_const_type
                FROM s_locals.t_local_data_main l
                LEFT JOIN fav_count ON 0=0
                LEFT JOIN users_count ON 0=0
                WHERE l.id = {$id_local_data_main}
                LIMIT 1
                    ";
        return DB::select($query);
    }

    public static function getUsedCouponsCity($id_city_const_type){
        $query = "SELECT
                        COUNT(*) AS city_count
                    FROM s_locals.t_local_data_main l
                    LEFT JOIN s_coupons.t_coupon_data_main c ON c.id_local_data_main = l.id
                    LEFT JOIN s_coupons.t_coupon_ref_user r ON r.id_coupon_data_main = c.id AND r.used = 1
                    WHERE l.id_city_const_type = {$id_city_const_type} AND r.id IS NOT NULL ";
        return DB::select($query);
    }

    public static function getCouponStatistics($id_local_data_main){
        $query = "WITH coupons AS (SELECT c.name,
                                            c.id,
                                            c.description,
                                            c.show_detail_count
                        FROM  s_coupons.t_coupon_data_main c
                        WHERE c.id_local_data_main = {$id_local_data_main}),
                coupon_count AS  (
                            SELECT COUNT(*) AS count_used,
                                    r.id_coupon_data_main
                            FROM coupons
                            LEFT JOIN s_coupons.t_coupon_ref_user r ON r.id_coupon_data_main = coupons.id AND r.used = 1
                            WHERE r.id IS NOT NULL
                            GROUP BY r.id_coupon_data_main)
                SELECT coupons.* ,
                coupon_count.count_used
                FROM coupons
                LEFT JOIN coupon_count ON coupon_count.id_coupon_data_main = coupons.id
                    ";
        return DB::select($query);
    }

    public static function getWorkerList($id_local_data_main){
        $query = "SELECT
                        u.email,
                        r.id AS id_worker_ref_user
                    FROM s_locals.t_worker_ref_user r
                    LEFT JOIN users u ON u.id = r.id_user
                    WHERE r.id_local_data_main = {$id_local_data_main};";
        return DB::select($query);
    }
}
