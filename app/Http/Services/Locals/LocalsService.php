<?php

namespace App\Http\Services\Locals;
use  App\Models\s_locals\OpenRefMain;
use App\Models\s_locals\LocalDataMain;
use App\Models\s_tags\LocalRefMain;
use App\Models\s_locals\LocalRefFavourite;
use \Auth;

use App\Http\Repositories\Locals\LocalsRepository;

class LocalsService
{
    public function getList($id_city_const_type){
        $locals = collect(LocalsRepository::getList($id_city_const_type));
        $tags = collect(LocalsRepository::getTags());
        foreach($locals AS $local){
            $local->tags = $tags->where('id_local_data_main', $local->local_id)->where('is_main', 'true')->map(function ($item, $key) {
                return collect($item)->except(['id_local_data_main'])->all();
            });
        }
        return json_encode($locals);
    }

    public function getDetails($id_local_data_main){
        $local = collect(LocalsRepository::getDetails($id_local_data_main))->first();
        $local->work_hours = collect(LocalsRepository::getWorkHours($local->local_id));
        $local->tags = collect(LocalsRepository::getTagsByLocal($local->local_id));
        return json_encode($local);
    }

    public function addOpenDays(){
            // $lokals = LocalDataMain::all();
            // foreach($lokals AS $local){
            //     for ($i = 0; $i <= 6; $i++) {
            //         $new_ref = new OpenRefMain();
            //         $new_ref->id_local_data_main = $local->id;
            //         $new_ref->id_weekday_const_type = $i;
            //         $new_ref->local_hour_from = '08:00';
            //         $new_ref->local_hour_to = '22:00';

            //         $new_ref->kitchen_hour_from = '08:00';
            //         $new_ref->kitchen_hour_to = '22:00';

            //         $new_ref->delivery_hour_from = '08:00';
            //         $new_ref->delivery_hour_to = '22:00';
            //         $new_ref->save();
            //     }
            // }
    }

    public function addTagsToLocal(){
        // $lokals = LocalDataMain::all();
        // foreach($lokals AS $local){
        //     $boolean = TRUE;
        //     for ($i = 0; $i <= 3; $i++) {
        //         $new_ref = new LocalRefMain();
        //         $new_ref->id_local_data_main = $local->id;
        //         $new_ref->id_tag_data_main = rand(1,12);
        //         $new_ref->priority_status = $boolean;
        //         if($i == 2){
        //             $boolean = false;
        //         }
        //         $new_ref->save();
        //     }
        // }
    }

    public function addLocalToFavourite($id_local_data_main){
        $id_user = Auth::user()->id;
        $favourite = new LocalRefFavourite();
        $favourite->id_user = $id_user;
        $favourite->id_local_data_main = $id_local_data_main;
        $favourite->save();
    }

    public function removeLocalFromFavourite($id_local_data_main){
        $id_user = Auth::user()->id;
        $favourite = LocalRefFavourite::where('id_user', $id_user)->where('id_local_data_main', $id_local_data_main)->delete();

    }
}
