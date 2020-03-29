<?php

namespace App\Http\Repositories\Locals;

use Illuminate\Support\Facades\DB;
use \Auth;

class LocalsRepository
{
    public static function getList($id_city_const_type)
    {
        $id_user = Auth::user()->id;
        $query = "SELECT
                        l.name,
                        l.id AS local_id,
                        CASE WHEN CURRENT_TIME < '06:00' AND o.local_hour_to < '06:00' AND o.local_hour_to > CURRENT_TIME THEN TRUE
                        	WHEN o.local_hour_from < CURRENT_TIME AND (o.local_hour_to > CURRENT_TIME OR o.local_hour_to < '06:00') THEN TRUE
                        ELSE FALSE
                        END AS is_open_now,
                        to_char(o.local_hour_from, 'HH24:MI') AS open_from,
                        to_char(o.local_hour_to, 'HH24:MI') AS open_to,
                        o.status_closed AS is_closed,
                        CASE WHEN f.id IS NOT NULL THEN TRUE
                        ELSE FALSE
                        END AS is_favouirite,
                        l.delivery,
                        l.eat_in_local,
                        l.pick_up_local,
                        l.order_url
                    FROM s_locals.t_local_data_main l
                    LEFT JOIN s_locals.t_open_ref_main o ON o.id_local_data_main = l.id
                                                            AND id_weekday_const_type = CASE WHEN CURRENT_TIME < '06:00' THEN extract(dow from CURRENT_TIMESTAMP) - 1
                                                                                            ELSE extract(dow from CURRENT_TIMESTAMP)
                                                                                            END
                    LEFT JOIN s_locals.t_local_ref_favourite f ON f.id_user = {$id_user} AND f.id_local_data_main = l.id
                    WHERE l.id_city_const_type = {$id_city_const_type} AND l.deleted = FALSE
                    ORDER BY (CASE WHEN CURRENT_TIME < '06:00' AND o.local_hour_to < '06:00' AND o.local_hour_to > CURRENT_TIME THEN 1
                        	WHEN o.local_hour_from < CURRENT_TIME AND (o.local_hour_to > CURRENT_TIME OR o.local_hour_to < '06:00') THEN 1
                        ELSE 0
                        END) DESC                                 ;
                    ";
        return DB::select($query);
    }

    public static function getDetails($id_local_data_main)
    {
        $id_user = Auth::user()->id;
        $query = "WITH counter_fav AS (SELECT
                                        COUNT(*) AS favourite_count
                                    FROM s_locals.t_local_ref_favourite
                                    WHERE id_local_data_main = {$id_local_data_main}

                        ),
                        counter_coupons AS (SELECT
                                        COUNT(*) AS coupons_count
                                    FROM s_coupons.t_coupon_data_main
                                    WHERE id_local_data_main = {$id_local_data_main}
                        )
                    SELECT
                        l.name,
                        l.id AS local_id,
                        CASE WHEN CURRENT_TIME < '06:00' AND o.local_hour_to < '06:00' AND o.local_hour_to > CURRENT_TIME THEN TRUE
                        	WHEN o.local_hour_from < CURRENT_TIME AND (o.local_hour_to > CURRENT_TIME OR o.local_hour_to < '06:00') THEN TRUE
                        ELSE FALSE
                        END AS local_open_status,
                        CASE WHEN CURRENT_TIME < '06:00' AND o.kitchen_hour_to < '06:00' AND o.kitchen_hour_to > CURRENT_TIME THEN TRUE
                        	WHEN o.kitchen_hour_from < CURRENT_TIME AND (o.kitchen_hour_to > CURRENT_TIME OR o.kitchen_hour_to < '06:00') THEN TRUE
                        ELSE FALSE
                        END AS kitchen_open_status,
                        CASE WHEN CURRENT_TIME < '06:00' AND o.delivery_hour_to < '06:00' AND o.delivery_hour_to > CURRENT_TIME THEN TRUE
                        	WHEN o.delivery_hour_from < CURRENT_TIME AND (o.delivery_hour_to > CURRENT_TIME OR o.delivery_hour_to < '06:00') THEN TRUE
                        ELSE FALSE
                        END AS delivery_open_status,
                        o.status_closed AS is_closed,
                        CASE WHEN f.id IS NOT NULL THEN TRUE
                        ELSE FALSE
                        END AS is_favouirite,
                        l.delivery,
                        l.eat_in_local,
                        l.pick_up_local,
                        l.address,
                        l.description,
                        l.other_info,
                        l.facebook_url,
                        l.instagram_url,
                        counter_fav.favourite_count,
                        counter_coupons.coupons_count,
                        l.cash_payment,
                        l.creditcards_payment,
                        l.contactless_payment,
                        l.blik_payment,
                        l.phone_number,
                        l.id_city_const_type,
                        l.order_url
                    FROM s_locals.t_local_data_main l
                    LEFT JOIN s_locals.t_open_ref_main o ON o.id_local_data_main = l.id
                                                            AND id_weekday_const_type = CASE WHEN CURRENT_TIME < '06:00' THEN extract(dow from CURRENT_TIMESTAMP) - 1
                                                                                        ELSE extract(dow from CURRENT_TIMESTAMP)
                                                                                        END
                    LEFT JOIN counter_fav ON 0=0
                    LEFT JOIN counter_coupons ON 0=0
                    LEFT JOIN s_locals.t_local_ref_favourite f ON f.id_user = {$id_user} AND f.id_local_data_main = l.id
                    WHERE l.id = {$id_local_data_main};
                    ";

        return DB::select($query);
    }

    public static function getTagsByLocal($local_id)
    {
        $query = "SELECT
                        t.id,
                        t.name,
                        r.priority_status AS is_main
                    FROM s_tags.t_local_ref_main r
                    LEFT JOIN s_tags.t_tag_data_main t ON t.id = r.id_tag_data_main
                    WHERE r.id_local_data_main = {$local_id};";
        return DB::select($query);
    }

    public static function getWorkHours($id_local_data_main)
    {
        $query = "SELECT
                        o.id_weekday_const_type AS id_day,
                        to_char(o.local_hour_from, 'HH24:MI') AS open_from,
                        to_char(o.local_hour_to, 'HH24:MI') AS open_to
                    FROM s_locals.t_open_ref_main o
                    LEFT JOIN s_locals.t_weekday_const_type c ON c.id = o.id_weekday_const_type
                    WHERE o.id_local_data_main = {$id_local_data_main}
                    ORDER BY c.order_column;";

        return DB::select($query);
    }

    public static function getFavouriteList($id_city_const_type)
    {
        $id_user = Auth::user()->id;
        $query = "SELECT
                        l.name,
                        l.id AS local_id,
                        to_char(o.local_hour_from, 'HH24:MI') AS open_from,
                        to_char(o.local_hour_to, 'HH24:MI') AS open_to,
                        o.status_closed AS is_closed,
                        CASE WHEN f.id IS NOT NULL THEN TRUE
                        ELSE FALSE
                        END AS is_favouirite,
                        l.delivery,
                        l.eat_in_local,
                        l.pick_up_local,
                        l.order_url,
                        CASE WHEN CURRENT_TIME < '06:00' AND o.local_hour_to < '06:00' AND o.local_hour_to > CURRENT_TIME THEN TRUE
                        	WHEN o.local_hour_from < CURRENT_TIME AND (o.local_hour_to > CURRENT_TIME OR o.local_hour_to < '06:00') THEN TRUE
                        ELSE FALSE
                        END AS is_open_now
                    FROM  s_locals.t_local_ref_favourite f
                    LEFT JOIN s_locals.t_local_data_main l ON f.id_local_data_main = l.id
                    LEFT JOIN s_locals.t_open_ref_main o ON o.id_local_data_main = l.id
                                                            AND o.id_weekday_const_type = CASE WHEN CURRENT_TIME < '06:00' THEN extract(dow from CURRENT_TIMESTAMP) - 1
                                                                                            ELSE extract(dow from CURRENT_TIMESTAMP)
                                                                                            END

                    WHERE f.id_user = {$id_user} AND l.id_city_const_type = {$id_city_const_type} AND l.deleted = FALSE;
                    ";
        return DB::select($query);
    }

    public static function getMapList($id_city_const_type)
    {
        $query = "SELECT
                        l.id AS local_id,
                        l.latitude,
                        l.longitude
                    FROM s_locals.t_local_data_main l
                    WHERE l.id_city_const_type = {$id_city_const_type} AND l.deleted = FALSE;
                    ";
        return DB::select($query);
    }

    public static function getAllWorkHours($id_local_data_main)
    {
        // kom

        $query = "SELECT
                        o.id_weekday_const_type AS id_week_day,
                        to_char(o.local_hour_from, 'HH24:MI') AS local_hour_from,
                        to_char(o.local_hour_to, 'HH24:MI') AS local_hour_to,
                        to_char(o.kitchen_hour_from, 'HH24:MI') AS kitchen_hour_from,
                        to_char(o.kitchen_hour_to, 'HH24:MI') AS kitchen_hour_to,
                        to_char(o.delivery_hour_from, 'HH24:MI') AS delivery_hour_from,
                        to_char(o.delivery_hour_to, 'HH24:MI') AS delivery_hour_to
                    FROM s_locals.t_open_ref_main o
                    LEFT JOIN s_locals.t_weekday_const_type c ON c.id = o.id_weekday_const_type
                    WHERE o.id_local_data_main = {$id_local_data_main}
                    ORDER BY c.order_column;";

        return DB::select($query);
    }

    public static function getDetailsEdit($id_local_data_main)
    {

        $id_user = Auth::user()->id;
        $query = "
                    SELECT
                        l.id AS id_local_data_main,
                        l.name,
                        l.address,
                        l.id_city_const_type,
                        l.phone_number,
                        l.description,
                        l.other_info,
                        l.facebook_url,
                        l.instagram_url,
                        l.delivery,
                        l.eat_in_local,
                        l.pick_up_local,
                        l.cash_payment,
                        l.creditcards_payment,
                        l.contactless_payment,
                        l.blik_payment,
                        l.delivery_range,
                        l.longitude,
                        l.latitude,
                        l.order_url
                    FROM s_locals.t_local_data_main l
                    WHERE l.id = {$id_local_data_main};
                    ";

        return DB::select($query);
    }

    public static function getOrderedList($id_city_const_type)
    {

        $id_user = Auth::user()->id;
        $query = "WITH counter_fav AS (SELECT
                                                COUNT(*) AS favourite_count,
                                                id_local_data_main
                                            FROM s_locals.t_local_ref_favourite
                                            GROUP BY id_local_data_main

                                        )

                SELECT
                        l.name,
                        l.id AS local_id,
                        CASE WHEN CURRENT_TIME < '06:00' AND o.local_hour_to < '06:00' AND o.local_hour_to > CURRENT_TIME THEN TRUE
                            WHEN o.local_hour_from < CURRENT_TIME AND (o.local_hour_to > CURRENT_TIME OR o.local_hour_to < '06:00') THEN TRUE
                            ELSE FALSE
                        END AS is_open_now,
                        to_char(o.local_hour_from, 'HH24:MI') AS open_from,
                        to_char(o.local_hour_to, 'HH24:MI') AS open_to,
                        o.status_closed AS is_closed,
                        CASE WHEN f.id IS NOT NULL THEN TRUE
                            ELSE FALSE
                        END AS is_favouirite,
                        l.delivery,
                        l.eat_in_local,
                        l.pick_up_local,
                        l.create_date,
                        c.favourite_count,
                        l.order_url

                FROM s_locals.t_local_data_main l
                LEFT JOIN s_locals.t_open_ref_main o ON o.id_local_data_main = l.id
                                            AND id_weekday_const_type = CASE WHEN CURRENT_TIME < '06:00' THEN extract(dow from CURRENT_TIMESTAMP) - 1
                                                                            ELSE extract(dow from CURRENT_TIMESTAMP)
                                                                            END
                LEFT JOIN s_locals.t_local_ref_favourite f ON f.id_user = {$id_user} AND f.id_local_data_main = l.id
                LEFT JOIN counter_fav c on c.id_local_data_main = l.id
                WHERE l.id_city_const_type = {$id_city_const_type} AND l.deleted = FALSE
                ORDER BY (CASE WHEN CURRENT_TIME < '06:00' AND o.local_hour_to < '06:00' AND o.local_hour_to > CURRENT_TIME THEN 1
                WHEN o.local_hour_from < CURRENT_TIME AND (o.local_hour_to > CURRENT_TIME OR o.local_hour_to < '06:00') THEN 1
                ELSE 0
                END) DESC;";

        return DB::select($query);
    }

    public static function getOrderedFavouriteList($id_city_const_type)
    {
        $id_user = Auth::user()->id;
        $query = "
                WITH counter_fav AS (SELECT
                            COUNT(*) AS favourite_count,
                            id_local_data_main
                        FROM s_locals.t_local_ref_favourite
                        GROUP BY id_local_data_main

                    )
                SELECT
                        l.name,
                        l.id AS local_id,
                        to_char(o.local_hour_from, 'HH24:MI') AS open_from,
                        to_char(o.local_hour_to, 'HH24:MI') AS open_to,
                        o.status_closed AS is_closed,
                        CASE WHEN f.id IS NOT NULL THEN TRUE
                        ELSE FALSE
                        END AS is_favouirite,
                        l.delivery,
                        l.eat_in_local,
                        l.pick_up_local,
                        l.create_date,
                        c.favourite_count,
                        l.order_url,
                        CASE WHEN CURRENT_TIME < '06:00' AND o.local_hour_to < '06:00' AND o.local_hour_to > CURRENT_TIME THEN TRUE
                        	WHEN o.local_hour_from < CURRENT_TIME AND (o.local_hour_to > CURRENT_TIME OR o.local_hour_to < '06:00') THEN TRUE
                        ELSE FALSE
                        END AS is_open_now
                    FROM  s_locals.t_local_ref_favourite f
                    LEFT JOIN s_locals.t_local_data_main l ON f.id_local_data_main = l.id
                    LEFT JOIN s_locals.t_open_ref_main o ON o.id_local_data_main = l.id
                                                            AND o.id_weekday_const_type = CASE WHEN CURRENT_TIME < '06:00' THEN extract(dow from CURRENT_TIMESTAMP) - 1
                                                                                            ELSE extract(dow from CURRENT_TIMESTAMP)
                                                                                            END
                    LEFT JOIN counter_fav c on c.id_local_data_main = l.id
                    WHERE f.id_user = {$id_user} AND l.id_city_const_type = {$id_city_const_type} AND l.deleted = FALSE;
                    ";
        return DB::select($query);
    }
}
