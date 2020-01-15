<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Services\Manager\ManagerService;
use App\Models\s_locals\WorkerRefUser;
use Auth;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    private $service;

    public function __construct(ManagerService $service){
        $this->service = $service;
    }

    public function registerWorker(Request $request){
        return $this->service->registerWorker($request->id_local_data_main, $request->uuid);
	}

    public function getLocalsByManager(){
        return $this->service->getLocalsByManager();
    }

    public function getLocalStatistics(Request $request){
        return $this->service->getLocalStatistics($request->id_local_data_main);
	}

}
