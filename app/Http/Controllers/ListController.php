<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Services\ListService;

class ListController extends Controller
{
    private $service;

    public function __construct(ListService $service){
        $this->service = $service;
    }

    public function getList(){
        return $this->service->getList();
        //coment
    }
}
