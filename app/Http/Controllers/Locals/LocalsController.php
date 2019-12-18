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

    public function getList(){
        return $this->service->getList();
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
}
