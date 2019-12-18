<?php

namespace App\Http\Services\Locals;
use  App\Models\s_locals\OpenRefMain;
use App\Models\s_locals\LocalDataMain;
use App\Models\s_tags\LocalRefMain;

use App\Http\Repositories\Locals\LocalsRepository;

class LocalsService 
{
    public function getList(){
        $locals = collect(LocalsRepository::getList());
        $tags = collect(LocalsRepository::getTags());
        foreach($locals AS $local){
            $local->tags = $tags->where('id_local_data_main', $local->local_id)->map(function ($item, $key) {
                return collect($item)->except(['id_local_data_main'])->all();
            });
        }
        return json_encode($locals);
    }

    public function addOpenDays(){
            $lokals = LocalDataMain::all();
            foreach($lokals AS $local){
                for ($i = 0; $i <= 6; $i++) {
                    $new_ref = new OpenRefMain();
                    $new_ref->id_local_data_main = $local->id;
                    $new_ref->id_weekday_const_type = $i;
                    $new_ref->local_hour_from = '08:00';
                    $new_ref->local_hour_to = '22:00';

                    $new_ref->kitchen_hour_from = '08:00';
                    $new_ref->kitchen_hour_to = '22:00';

                    $new_ref->delivery_hour_from = '08:00';
                    $new_ref->delivery_hour_to = '22:00';
                    $new_ref->save();
                }
            }
    }

    public function addTagsToLocal(){
        $lokals = LocalDataMain::all();
        foreach($lokals AS $local){
            $boolean = TRUE;
            for ($i = 0; $i <= 3; $i++) {
                $new_ref = new LocalRefMain();
                $new_ref->id_local_data_main = $local->id;
                $new_ref->id_tag_data_main = rand(1,12);
                $new_ref->priority_status = $boolean;
                if($i == 2){
                    $boolean = false;
                }
                $new_ref->save();
            }
        }
}
}
