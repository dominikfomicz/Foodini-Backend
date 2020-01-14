<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Services\Manager\ManagerService;
use App\Models\s_locals\WorkerRefUser;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    private $service;

    public function __construct(ManagerService $service){
        $this->service = $service;
    }

    public function registerWorker(Request $request){

		$request->validate([
            'uuid' => 'required'
		]);

		$user_type = Auth::user()->user_type;
		if($user_type == 3){
            $user = User::where('email', $request->uuid);
            $user->user_type = 2;
            $user->save();

            $worker = New WorkerRefUser();
            $worker->id_user = $user->id;
            $worker->id_local_data_main = $request->id_local_data_main;
            $worker->save();

			return json_encode(0);
		}


	}

    public function getLocalsByManager(Request $request){
        return $this->service->getLocalsByManager();
    }

}
