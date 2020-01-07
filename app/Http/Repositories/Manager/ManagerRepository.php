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
}
