<?php

namespace App\Http\Repositories\Coupons;
use Illuminate\Support\Facades\DB;
use \Auth;


class CouponsRepository
{
    public static function getList($id_local_data_main){
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
                        c.delivery,
                        c.eat_in_local,
                        c.pick_up_local,
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
                        END AS coupon_left,
                        CASE WHEN CURRENT_TIME < '06:00' AND o.hour_to < '06:00' AND o.hour_to > CURRENT_TIME THEN TRUE
                             WHEN o.hour_from < CURRENT_TIME AND (o.hour_to > CURRENT_TIME OR o.hour_to < '06:00') THEN TRUE
                             ELSE FALSE
                        END AS is_available
                    FROM s_coupons.t_coupon_data_main c
                    LEFT JOIN s_locals.t_local_data_main l ON l.id = c.id_local_data_main
                    LEFT JOIN s_coupons.t_coupon_ref_favourite f ON f.id_user = {$id_user} AND f.id_coupon_data_main = c.id
                    LEFT JOIN used_counter ON used_counter.id_coupon_data_main = c.id
                    LEFT JOIN s_coupons.t_available_day_ref o ON o.id_coupon_data_main = c.id
                                                            AND o.id_weekday_const_type = CASE WHEN CURRENT_TIME < '06:00' THEN extract(dow from CURRENT_TIMESTAMP) - 1
                                                                                            ELSE extract(dow from CURRENT_TIMESTAMP)
                                                                                            END
                    WHERE c.id_local_data_main = {$id_local_data_main}
                    ORDER BY (CASE WHEN CURRENT_TIME < '06:00' AND o.hour_to < '06:00' AND o.hour_to > CURRENT_TIME THEN 1
                             WHEN o.hour_from < CURRENT_TIME AND (o.hour_to > CURRENT_TIME OR o.hour_to < '06:00') THEN 1
                             ELSE 0
                        END) DESC;
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
        $id_user = Auth::user()->id;
        $query = "
                WITH favourite_count AS (SELECT COUNT(*) AS favourite_count
                                        FROM s_coupons.t_coupon_ref_favourite f
                                        WHERE f.id_coupon_data_main = {$id_coupon_data_main}
                                        LIMIT 1)
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
                        END AS is_available,
                        c.delivery,
                        c.eat_in_local,
                        c.pick_up_local,
                        c.mature,
                        favourite_count.favourite_count
                    FROM s_coupons.t_coupon_data_main c
                    LEFT JOIN s_locals.t_local_data_main l ON l.id = c.id_local_data_main
                    LEFT JOIN s_coupons.t_coupon_ref_favourite f ON f.id_user = {$id_user} AND f.id_coupon_data_main = c.id
                    LEFT JOIN s_coupons.t_available_day_ref o ON o.id_coupon_data_main = c.id
                                                            AND o.id_weekday_const_type = CASE WHEN CURRENT_TIME < '06:00' THEN extract(dow from CURRENT_TIMESTAMP) - 1
                                                                                            ELSE extract(dow from CURRENT_TIMESTAMP)
                                                                                            END
                                                            AND (CASE WHEN CURRENT_TIME < '06:00' AND o.hour_to < '06:00' AND o.hour_to > CURRENT_TIME THEN TRUE
                                                                    WHEN o.hour_from < CURRENT_TIME AND (o.hour_to > CURRENT_TIME OR o.hour_to < '06:00') THEN TRUE
                                                                ELSE FALSE
                                                                END) = TRUE
                    LEFT JOIN favourite_count ON 0=0
                    WHERE c.id = {$id_coupon_data_main}
                    LIMIT 1;
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
                        END AS status,
                        CASE WHEN CURRENT_TIME < '06:00' AND o.hour_to < '06:00' AND o.hour_to > CURRENT_TIME THEN TRUE
                             WHEN o.hour_from < CURRENT_TIME AND (o.hour_to > CURRENT_TIME OR o.hour_to < '06:00') THEN TRUE
                             ELSE FALSE
                        END AS is_available
                    FROM  s_coupons.t_coupon_ref_favourite f
                    LEFT JOIN s_coupons.t_coupon_data_main c ON c.id = f.id_coupon_data_main
                    LEFT JOIN s_locals.t_local_data_main l ON l.id = c.id_local_data_main
                    LEFT JOIN s_coupons.t_available_day_ref o ON o.id_coupon_data_main = c.id
                                                            AND o.id_weekday_const_type = CASE WHEN CURRENT_TIME < '06:00' THEN extract(dow from CURRENT_TIMESTAMP) - 1
                                                                                            ELSE extract(dow from CURRENT_TIMESTAMP)
                                                                                            END
                    WHERE f.id_user = {$id_user}
                    ORDER BY (CASE WHEN CURRENT_TIME < '06:00' AND o.hour_to < '06:00' AND o.hour_to > CURRENT_TIME THEN 1
                             WHEN o.hour_from < CURRENT_TIME AND (o.hour_to > CURRENT_TIME OR o.hour_to < '06:00') THEN 1
                             ELSE 0
                        END) DESC;
                    ";
        return DB::select($query);
    }

    public static function getCouponsByCity($id_city_const_type){
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
                        END AS coupon_left,
                        CASE WHEN CURRENT_TIME < '06:00' AND o.hour_to < '06:00' AND o.hour_to > CURRENT_TIME THEN TRUE
                             WHEN o.hour_from < CURRENT_TIME AND (o.hour_to > CURRENT_TIME OR o.hour_to < '06:00') THEN TRUE
                             ELSE FALSE
                        END AS is_available
                    FROM s_locals.t_local_data_main l
                    LEFT JOIN s_coupons.t_coupon_data_main c ON l.id = c.id_local_data_main
                    LEFT JOIN s_coupons.t_coupon_ref_favourite f ON f.id_user = {$id_user} AND f.id_coupon_data_main = c.id
                    LEFT JOIN used_counter ON used_counter.id_coupon_data_main = c.id
                    LEFT JOIN s_coupons.t_available_day_ref o ON o.id_coupon_data_main = c.id
                                                            AND o.id_weekday_const_type = CASE WHEN CURRENT_TIME < '06:00' THEN extract(dow from CURRENT_TIMESTAMP) - 1
                                                                                            ELSE extract(dow from CURRENT_TIMESTAMP)
                                                                                            END
                    WHERE l.id_city_const_type = {$id_city_const_type} AND l.deleted = FALSE AND c.status = 1
                    ORDER BY (CASE WHEN CURRENT_TIME < '06:00' AND o.hour_to < '06:00' AND o.hour_to > CURRENT_TIME THEN 1
                             WHEN o.hour_from < CURRENT_TIME AND (o.hour_to > CURRENT_TIME OR o.hour_to < '06:00') THEN 1
                             ELSE 0
                        END) DESC;
                    ";
        return DB::select($query);
    }

    public static function getAvailableHoursEdit($id_coupon_data_main){

        //
        $query = "SELECT
                        d.id_weekday_const_type AS id_week_day,
                        to_char(d.hour_from, 'HH24:MI') AS hour_from,
                        to_char(d.hour_to, 'HH24:MI') AS hour_to
                    FROM s_coupons.t_available_day_ref d
                    LEFT JOIN s_locals.t_weekday_const_type c ON c.id = d.id_weekday_const_type
                    WHERE d.id_coupon_data_main = {$id_coupon_data_main}
                    ORDER BY c.order_column;";

        return DB::select($query);
    }

    public static function getDetailsEdit($id_coupon_data_main){
        $query = "
                    SELECT
                        c.id AS id_coupon_data_main,
                        c.description,
                        c.amount,
                        c.name,
                        c.delivery,
                        c.eat_in_local,
                        c.pick_up_local,
                        c.mature,
                        c.id_local_data_main
                    FROM s_coupons.t_coupon_data_main c
                    WHERE c.id = {$id_coupon_data_main}
                    LIMIT 1;
                    ";

        return DB::select($query);
    }

    public static function getSupportCouponsByCity($id_city_const_type){
        $query = "SELECT
                        c.id AS id_coupon_data_main,
                        c.name
                    FROM s_coupons.t_coupon_data_main c
                    LEFT JOIN s_locals.t_local_data_main l ON l.id = c.id_local_data_main
                    WHERE l.id_city_const_type = {$id_city_const_type};
                    ";
        return DB::select($query);
    }

    public static function getUsedCouponsStatistic($id_user){
        $query = "SELECT
                            c.name AS coupon_name,
                            used.create_date AS generate_date
                        FROM s_locals.t_manager_ref_user ref
                        LEFT JOIN s_locals.t_local_data_main l ON l.id = ref.id_local_data_main
                        LEFT JOIN s_coupons.t_coupon_data_main c ON c.id_local_data_main = l.id
                        LEFT JOIN s_coupons.t_coupon_ref_user used ON used.id_coupon_data_main = c.id AND used.used = 1
                        WHERE ref.id_user = {$id_user} AND used.id IS NOT NULL
                    UNION
                    SELECT
                            c.name AS coupon_name,
                            used.create_date AS generate_date
                        FROM s_locals.t_worker_ref_user ref
                        LEFT JOIN s_locals.t_local_data_main l ON l.id = ref.id_local_data_main
                        LEFT JOIN s_coupons.t_coupon_data_main c ON c.id_local_data_main = l.id
                        LEFT JOIN s_coupons.t_coupon_ref_user used ON used.id_coupon_data_main = c.id AND used.used = 1
                        WHERE ref.id_user = {$id_user} AND used.id IS NOT NULL
                        ORDER BY generate_date DESC;
                    ";
        return DB::select($query);
    }

    public static function getOrderedListByCity($id_city_const_type){
        $id_user = Auth::user()->id;
        $query = "WITH used_counter AS (
                                        SELECT
                                            COUNT(*) AS used_counter,
                                            id_coupon_data_main
                                        FROM s_coupons.t_coupon_ref_user
                                        GROUP BY id_coupon_data_main
                                        ),
                        counter_fav AS (SELECT
                            COUNT(*) AS favourite_count,
                            id_coupon_data_main
                        FROM s_coupons.t_coupon_ref_favourite
                        GROUP BY id_coupon_data_main)

                    SELECT
                            c.id AS coupon_id,
                            l.id AS local_id,
                            c.name AS coupon_name,
                            c.mature,
                            c.delivery,
                            c.eat_in_local,
                            c.pick_up_local,
                            c.amount,
                            c.create_date,
                            cf.favourite_count,
                            CASE
                                WHEN f.id IS NOT NULL THEN TRUE
                                ELSE FALSE
                            END AS is_favouirite,
                            l.name AS local_name,
                            CASE
                                WHEN c.status = 1 THEN TRUE
                                ELSE FALSE
                            END AS status,
                            CASE
                                WHEN c.amount = -1 THEN c.amount
                                WHEN used_counter.used_counter IS NOT NULL THEN c.amount - used_counter.used_counter
                                ELSE c.amount
                            END AS coupon_left,
                            CASE
                                WHEN CURRENT_TIME < '06:00' AND o.hour_to < '06:00' AND o.hour_to > CURRENT_TIME THEN TRUE
                                WHEN o.hour_from < CURRENT_TIME AND (o.hour_to > CURRENT_TIME OR o.hour_to < '06:00') THEN TRUE
                                ELSE FALSE
                            END AS is_available
                        FROM s_coupons.t_coupon_data_main c
                        LEFT JOIN s_locals.t_local_data_main l ON l.id = c.id_local_data_main
                        LEFT JOIN s_coupons.t_coupon_ref_favourite f ON f.id_user = {$id_user} AND f.id_coupon_data_main = c.id
                        LEFT JOIN used_counter ON used_counter.id_coupon_data_main = c.id
                        LEFT JOIN s_coupons.t_available_day_ref o ON o.id_coupon_data_main = c.id
                                                        AND o.id_weekday_const_type = CASE WHEN CURRENT_TIME < '06:00' THEN extract(dow from CURRENT_TIMESTAMP) - 1
                                                                                        ELSE extract(dow from CURRENT_TIMESTAMP)
                                                                                        END
                        LEFT JOIN counter_fav cf on cf.id_coupon_data_main = c.id
                        WHERE l.id_city_const_type = {$id_city_const_type}
                        ORDER BY (CASE WHEN CURRENT_TIME < '06:00' AND o.hour_to < '06:00' AND o.hour_to > CURRENT_TIME THEN 1
                        WHEN o.hour_from < CURRENT_TIME AND (o.hour_to > CURRENT_TIME OR o.hour_to < '06:00') THEN 1
                        ELSE 0
                        END) DESC;";
        return DB::select($query);
    }

    public static function getOrderedFavouriteList(){
        $id_user = Auth::user()->id;
        $query = "WITH used_counter AS (
                                        SELECT
                                            COUNT(*) AS used_counter,
                                            id_coupon_data_main
                                        FROM s_coupons.t_coupon_ref_user
                                        GROUP BY id_coupon_data_main
                                        ),
                        counter_fav AS (SELECT
                            COUNT(*) AS favourite_count,
                            id_coupon_data_main
                        FROM s_coupons.t_coupon_ref_favourite
                        GROUP BY id_coupon_data_main)

                    SELECT
                            c.id AS coupon_id,
                            l.id AS local_id,
                            c.name AS coupon_name,
                            c.mature,
                            c.delivery,
                            c.eat_in_local,
                            c.pick_up_local,
                            c.amount,
                            cf.favourite_count,
                            CASE
                                WHEN f.id IS NOT NULL THEN TRUE
                                ELSE FALSE
                            END AS is_favouirite,
                            l.name AS local_name,
                            CASE
                                WHEN c.status = 1 THEN TRUE
                                ELSE FALSE
                            END AS status,
                            CASE
                                WHEN c.amount = -1 THEN c.amount
                                WHEN used_counter.used_counter IS NOT NULL THEN c.amount - used_counter.used_counter
                                ELSE c.amount
                            END AS coupon_left,
                            CASE
                                WHEN CURRENT_TIME < '06:00' AND o.hour_to < '06:00' AND o.hour_to > CURRENT_TIME THEN TRUE
                                WHEN o.hour_from < CURRENT_TIME AND (o.hour_to > CURRENT_TIME OR o.hour_to < '06:00') THEN TRUE
                            ELSE FALSE
                            END AS is_available
                        FROM s_coupons.t_coupon_data_main c
                        LEFT JOIN s_locals.t_local_data_main l ON l.id = c.id_local_data_main
                        LEFT JOIN s_coupons.t_coupon_ref_favourite f ON f.id_user = {$id_user} AND f.id_coupon_data_main = c.id
                        LEFT JOIN used_counter ON used_counter.id_coupon_data_main = c.id
                        LEFT JOIN s_coupons.t_available_day_ref o ON o.id_coupon_data_main = c.id
                                                        AND o.id_weekday_const_type = CASE WHEN CURRENT_TIME < '06:00' THEN extract(dow from CURRENT_TIMESTAMP) - 1
                                                                                        ELSE extract(dow from CURRENT_TIMESTAMP)
                                                                                        END
                        LEFT JOIN counter_fav cf on cf.id_coupon_data_main = c.id
                        WHERE f.id_user = {$id_user}
                        ORDER BY (CASE WHEN CURRENT_TIME < '06:00' AND o.hour_to < '06:00' AND o.hour_to > CURRENT_TIME THEN 1
                        WHEN o.hour_from < CURRENT_TIME AND (o.hour_to > CURRENT_TIME OR o.hour_to < '06:00') THEN 1
                        ELSE 0
                        END) DESC;";
        return DB::select($query);
    }
}
