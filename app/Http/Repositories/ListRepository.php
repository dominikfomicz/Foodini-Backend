<?php

namespace App\Http\Repositories;
use Illuminate\Support\Facades\DB;

class ListRepository 
{
    public static function getList(){
        return DB::select("SELECT * FROM locals.main");
    }
}
