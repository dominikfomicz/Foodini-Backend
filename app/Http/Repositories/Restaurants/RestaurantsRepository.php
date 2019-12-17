<?php

namespace App\Http\Repositories\Restaurants;
use Illuminate\Support\Facades\DB;

class RestaurantsRepository 
{
    public static function getList(){
        return DB::select("SELECT * FROM locals.main");
    }
}
