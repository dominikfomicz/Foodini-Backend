<?php

namespace App\Http\Services;

use App\Http\Repositories\ListRepository;

class ListService 
{
    public function getList(){
        return ListRepository::getList();
        //comment
        //coment
    }
}
