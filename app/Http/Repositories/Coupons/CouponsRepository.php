<?php

namespace App\Http\Repositories\Coupons;
use Illuminate\Support\Facades\DB;

class CouponsRepository 
{
    public static function getList(){
        return DB::select("SELECT * FROM locals.main");
    }
}
