<?php

namespace App\Http\Services\Locals;
use App\Models\s_locals\OpenRefMain;
use App\Models\s_tags\LocalRefMain;
use App\Models\s_locals\LocalRefFavourite;

use App\Http\Repositories\Locals\LocalsRepository;
use App\Models\s_locals\LocalDataMain;
use App\Models\s_sys\HexaConstType;
use App\Http\Services\Locals\FilesService;
use App\Models\s_locals\LocalLogStatistics;
use Auth;

class LocalsService
{
    public function getList($id_city_const_type){
        $locals = collect(LocalsRepository::getList($id_city_const_type));
        foreach($locals AS $local){
            $local->tags = collect(LocalsRepository::getTagsByLocal($local->local_id))->where('is_main', true);
        }
        return json_encode($locals);
    }

    public function getDetails($id_local_data_main){
        $id_user = Auth::user()->id;
        $local = collect(LocalsRepository::getDetails($id_local_data_main))->first();
        $local->work_hours = collect(LocalsRepository::getWorkHours($local->local_id));
        $tags = collect(LocalsRepository::getTagsByLocal($local->local_id));
        $local->main_tags = $tags->where('is_main', TRUE);
        $local->secondary_tags = $tags->where('is_main', FALSE);

        $stats_local = LocalDataMain::find($id_local_data_main);
        $stats_local->show_detail_count = $stats_local->show_detail_count + 1;
        $stats_local->save();

        $log_local = new LocalLogStatistics();
        $log_local->id_user = $id_user;
        $log_local->type = 1;
        $log_local->id_local_data_main = $id_local_data_main;
        $log_local->save();

        return json_encode($local);
    }

    public function changeOpenHoursDay($id_local_data_main, $week_day_id, $open_data){
        If (Auth::user()->user_type == -1){
            OpenRefMain::where('id_local_data_main', $id_local_data_main)->where('id_weekday_const_type', $week_day_id)->delete();

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

    }

    public function addTagToLocal($id_local_data_main, $id_tag_data_main, $priority_status){
        If (Auth::user()->user_type == -1){
            $new_ref = new LocalRefMain();
            $new_ref->id_local_data_main = $id_local_data_main;
            $new_ref->id_tag_data_main = $id_tag_data_main;
            $new_ref->priority_status = $priority_status;
            $new_ref->save();
        }

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
        foreach($locals AS $local){
            $local->tags = collect(LocalsRepository::getTagsByLocal($local->local_id))->where('is_main', true);
        }
        return json_encode($locals);
    }

    public function changeLocal($id_local_data_main, $local_data, $tags, $open_hours, $file_logo, $file_background, $files_menu, $file_map){
        If (Auth::user()->user_type == -1){
            if($id_local_data_main == -1){
                $new_local = new LocalDataMain();

                $hexa = HexaConstType::where('used_local', FALSE)->first();
                $new_local->hexa_value = $hexa->value;

                $hexa->used_local = TRUE;
                $hexa->save();
            }else{
                $new_local = LocalDataMain::find($id_local_data_main);
            }

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
            $new_local->contactless_payment = $local_data->contactless_payment;
            $new_local->blik_payment = $local_data->blik_payment;
            $new_local->delivery_range = $local_data->delivery_range;

            $new_local->longitude = $local_data->longitude;
            $new_local->latitude = $local_data->latitude;
            $new_local->save();

            LocalRefMain::where('id_local_data_main', $new_local->id)->delete();
            foreach($tags AS $tag){
                $this->addTagToLocal($new_local->id, $tag->id, $tag->priority_status);
            }

            foreach($open_hours AS $open_hour){
                $this->changeOpenHoursDay($new_local->id, $open_hour->id_week_day, $open_hour);
            }

            $files = new FilesService();
            $files->addLogo($new_local->id, $file_logo);
            $files->addBackground($new_local->id, $file_background);
            // $files->addMenuPhotos($new_local->id, $files_menu);
            $files->addMapLogo($new_local->id, $file_map);
            return json_encode($new_local);
        }

    }

    public function removeLocal($id_local_data_main){
        If (Auth::user()->user_type == -1){
            $local = LocalDataMain::find($id_local_data_main);
            $local->deleted = true;
            $local->save();
        }

    }

    public function getMapList($id_city_const_type){
        $locals = collect(LocalsRepository::getMapList($id_city_const_type));
        return json_encode($locals);
    }

    public function getDetailsEdit($id_local_data_main){
        If (Auth::user()->user_type == -1){
            $local = collect(LocalsRepository::getDetailsEdit($id_local_data_main))->first();
            $local->work_hours = collect(LocalsRepository::getAllWorkHours($local->id_local_data_main));
            $tags = collect(LocalsRepository::getTagsByLocal($local->id_local_data_main));
            $local->main_tags = $tags->where('is_main', TRUE);
            $local->secondary_tags = $tags->where('is_main', FALSE);
            return json_encode($local);
        }

    }

    public function getOrderedList($id_city_const_type, $id_sort_const_type){
        // 1 Najbardziej popularne | 2 Najnowsze | 3 Tylko otwarte
        switch ($id_sort_const_type) {
            case 1:
                $locals = collect(LocalsRepository::getOrderedList($id_city_const_type))->sortByDesc('favourite_count');
                foreach($locals AS $local){
                    $local->tags = collect(LocalsRepository::getTagsByLocal($local->local_id))->where('is_main', true);
                }
            break;

            case 2:
                $locals = collect(LocalsRepository::getOrderedList($id_city_const_type))->sortByDesc('create_date');
                foreach($locals AS $local){
                    $local->tags = collect(LocalsRepository::getTagsByLocal($local->local_id))->where('is_main', true);
                }
            break;

            case 3:
                $locals = collect(LocalsRepository::getOrderedList($id_city_const_type))->where('is_open_now', true);
                foreach($locals AS $local){
                    $local->tags = collect(LocalsRepository::getTagsByLocal($local->local_id))->where('is_main', true);
                }
            break;
        }
        return json_encode($locals);
    }

    public function getOrderedFavouriteList($id_city_const_type, $id_sort_const_type){
        // 1 Najbardziej popularne | 2 Najnowsze | 3 Tylko otwarte
        switch ($id_sort_const_type) {
            case 1:
                $locals = collect(LocalsRepository::getOrderedFavouriteList($id_city_const_type))->sortByDesc('favourite_count');
                foreach($locals AS $local){
                    $local->tags = collect(LocalsRepository::getTagsByLocal($local->local_id))->where('is_main', true);
                }
                return json_encode($locals);
            break;

            case 2:
                $locals = collect(LocalsRepository::getOrderedFavouriteList($id_city_const_type))->sortByDesc('create_date');
                foreach($locals AS $local){
                    $local->tags = collect(LocalsRepository::getTagsByLocal($local->local_id))->where('is_main', true);
                }
                return json_encode($locals);
            break;

            case 3:
                $locals = collect(LocalsRepository::getOrderedFavouriteList($id_city_const_type))->where('is_open_now', true);
                foreach($locals AS $local){
                    $local->tags = collect(LocalsRepository::getTagsByLocal($local->local_id))->where('is_main', true);
                }
                return json_encode($locals);
            break;
        }
    }
}
