<?php

namespace App\Http\Services\Restaurants;

use App\Http\Repositories\Restaurants\RestaurantsRepository;

class RestaurantsService 
{
    public function getList(){
        return RestaurantsRepository::getList();
        //coment
    }
}
