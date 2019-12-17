<?php

namespace App\Http\Controllers\Restaurants;

use App\Http\Controllers\Controller;
use App\Http\Services\Restaurants\RestaurantsService;

class RestaurantsController extends Controller
{
    private $service;

    public function __construct(RestaurantsService $service){
        $this->service = $service;
    }

    public function getList(){
        return $this->service->getList();
    }
}
