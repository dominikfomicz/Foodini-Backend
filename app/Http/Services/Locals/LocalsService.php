<?php

namespace App\Http\Services\Locals;
use App\Models\s_locals\OpenRefMain;
use App\Models\s_tags\LocalRefMain;
use App\Models\s_locals\LocalRefFavourite;

use App\Http\Repositories\Locals\LocalsRepository;
use App\Models\s_locals\LocalDataMain;
use \Auth;

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
        $local->tags = collect(LocalsRepository::getTagsByLocal($local->local_id))->where('is_main', 'true');
        return json_encode($local);
    }

    public function changeOpenHoursDay($id_local_data_main, $week_day_id, $open_data){

        OpenRefMain::where('id_local_data_main', $id_local_data_main)->where('id_week_day_const_type', $week_day_id)->delete();

        $new_ref = new OpenRefMain();
        $new_ref->id_local_data_main = $id_local_data_main;
        $new_ref->id_weekday_const_type = $week_day_id;
        $new_ref->local_hour_from = $open_data->local_hour_from;
        $new_ref->local_hour_to = $open_data->local_hour_to;

        $new_ref->kitchen_hour_from = $open_data->kitchen_hour_from;
        $new_ref->kitchen_hour_to = $open_data->kitchen_hour_to;

        $new_ref->delivery_hour_from = $open_data->delivery_hour_from;
        $new_ref->delivery_hour_to = $open_data->delivery_hour_to;
        $new_ref->save();
    }

    public function addTagToLocal($id_local_data_main, $id_tag_data_main, $priority_status){
        $new_ref = new LocalRefMain();
        $new_ref->id_local_data_main = $id_local_data_main;
        $new_ref->id_tag_data_main = $id_tag_data_main;
        $new_ref->priority_status = $priority_status;
        $new_ref->save();
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

    public function getFavouriteList($id_city_const_type){
        $locals = collect(LocalsRepository::getFavouriteList($id_city_const_type));
        $tags = collect(LocalsRepository::getTags());
        foreach($locals AS $local){
            $local->tags = $tags->where('id_local_data_main', $local->local_id)->where('is_main', 'true')->map(function ($item, $key) {
                return collect($item)->except(['id_local_data_main'])->all();
            });
        }
        return json_encode($locals);
    }

    public function addLocal($local_data, $tags, $open_hours){

        //why not working
        $new_local = new LocalDataMain();
        $new_local->name = $local_data->name;
        $new_local->address = $local_data->address;
        $new_local->id_city_const_type = $local_data->id_city_const_type;
        $new_local->phone_number = $local_data->phone_number;
        $new_local->description = $local_data->description;
        $new_local->other_info = $local_data->other_info;
        $new_local->facebook_url = $local_data->facebook_url;
        $new_local->instagram_url = $local_data->instagram_url;
        $new_local->delivery = $local_data->delivery;
        $new_local->eat_in_local = $local_data->eat_in_local;
        $new_local->pick_up_local = $local_data->pick_up_local;
        $new_local->cash_payment = $local_data->cash_payment;
        $new_local->creditcards_payment = $local_data->creditcards_payment;
        $new_local->contackless_payment = $local_data->contackless_payment;
        $new_local->blik_payment = $local_data->blik_payment;

        $new_local->save();

        foreach($tags AS $tag){
            $this->addTagToLocal($new_local->id, $tag->id, $tag->priority_status);
        }

        foreach($open_hours AS $open_hour){
            $this->changeOpenHoursDay($new_local->id, $open_hour->id_week_day, $open_hour);
        }
        return json_encode($new_local);
    }

    public function removeLocal($id_local_data_main){
        $local = LocalDataMain::find($id_local_data_main);
        $local->delete = true;
        $local->save();
    }
}
