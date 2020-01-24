<?php

namespace App\Http\Controllers\Locals;

use App\Http\Controllers\Controller;
use App\Http\Services\Locals\LocalsStatisticsService;
use Illuminate\Http\Request;

class LocalsStatisticsController extends Controller
{
    private $service;

    public function __construct(LocalsStatisticsService $service){
        $this->service = $service;
    }

    public function showFacebookCount(Request $request){
        return $this->service->showFacebookCount($request->id_local_data_main);
    }

    public function showInstagramCount(Request $request){
        return $this->service->showInstagramCount($request->id_local_data_main);
    }

    public function showMenuCount(Request $request){
        return $this->service->showMenuCount($request->id_local_data_main);
    }

    public function showPhonenumberCount(Request $request){
        return $this->service->showPhonenumberCount($request->id_local_data_main);
    }

}
