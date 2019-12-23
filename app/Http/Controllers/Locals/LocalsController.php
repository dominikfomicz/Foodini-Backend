<?php

namespace App\Http\Controllers\Locals;

use App\Http\Controllers\Controller;
use App\Http\Services\Locals\LocalsService;
use Illuminate\Http\Request;

class LocalsController extends Controller
{
    private $service;

    public function __construct(LocalsService $service){
        $this->service = $service;
    }

    public function getList(Request $request){
        return $this->service->getList($request->id_city_const_type);
    }

    public function getDetails(Request $request){
        return $this->service->getDetails($request->id_local_data_main);
    }

    public function addOpenDays(){
        return $this->service->addOpenDays();
    }

    public function addTagsToLocal(){
        return $this->service->addTagsToLocal();
    }

    public function addLocalToFavourite(Request $request){
        return $this->service->addLocalToFavourite($request->id_local_data_main);
    }

    public function removeLocalFromFavourite(Request $request){
        return $this->service->removeLocalFromFavourite($request->id_local_data_main);
    }

    public function getFavouriteList(Request $request){
        return $this->service->getFavouriteList($request->id_city_const_type);
    }

    public function addLocal(Request $request){
        return $this->service->addLocal();
    }

    public function removeLocal(Request $request){
        return $this->service->removeLocal($request->id_local_data_main);
    }
    
}
