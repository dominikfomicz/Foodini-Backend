<?php

namespace App\Http\Repositories\Locals;
use Illuminate\Support\Facades\DB;

class LocalsRepository 
{
    public static function getList(){
        $query = "SELECT 
                        l.name,
                        l.id AS local_id,
                        o.local_hour_from AS open_from,
                        o.local_hour_to AS open_to,
                        o.status_closed AS is_closed,
                        FALSE AS is_favourite,
                        l.supply,
                        l.eat_in_local,
                        l.pick_up_local		
                    
                    FROM s_locals.t_local_data_main l
                    LEFT JOIN s_locals.t_open_ref_main o ON o.id_local_data_main = l.id
                                                            AND id_weekday_const_type = 1;
                    ";
        return DB::select($query);
    }

    public static function getTags(){
        $query = "SELECT 
                        r.id_local_data_main,
                        t.id,
                        t.name
                    FROM s_tags.t_local_ref_main r
                    LEFT JOIN s_tags.t_tag_data_main t ON t.id = r.id_tag_data_main;";
        return DB::select($query);
    }
}
