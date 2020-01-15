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
                        l.delivery,
                        l.eat_in_local,
                        l.pick_up_local

                    FROM s_locals.t_manager_ref_user r
                    LEFT JOIN s_locals.t_local_data_main l ON l.id = r.id_local_data_main
                    LEFT JOIN s_locals.t_open_ref_main o ON o.id_local_data_main = l.id
                                                            AND id_weekday_const_type = {$day_of_week}
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
                            WHERE last_login_date >= date_trunc('month', current_date - interval '1' month)
                    )
                    
                SELECT	
                l.show_detail_count,
                l.show_facebook_count,
                l.show_menu_count,
                l.show_instagram_count,
                l.show_phonenumber_count,
                fav_count.fav_count,
                users_count.users_count
                FROM s_locals.t_local_data_main l
                LEFT JOIN fav_count ON 0=0
                LEFT JOIN users_count ON 0=0
                WHERE l.id = {$id_local_data_main}
                    ";
        return DB::select($query);
    }
}
