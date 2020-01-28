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

    public function changeLocal(Request $request){
        return $this->service->changeLocal($request->id_local_data_main,
                                            json_decode($request->local_data),
                                            json_decode($request->tags),
                                            json_decode($request->open_hours),
                                            $request->file('file_logo'),
                                            $request->file('file_background'),
                                            json_decode($request->file('file_menu')),
                                            $request->file('file_map')
                                        );
    }

    public function removeLocal(Request $request){
        return $this->service->removeLocal($request->id_local_data_main);
    }

    public function getMapList(Request $request){
        return $this->service->getMapList($request->id_city_const_type);
    }

    public function getDetailsEdit(Request $request){
        return $this->service->getDetailsEdit($request->id_local_data_main);
    }

}
