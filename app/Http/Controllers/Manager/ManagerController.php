<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Services\Manager\ManagerService;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    private $service;

    public function __construct(ManagerService $service){
        $this->service = $service;
    }

    public function registerWorker(Request $request){
		
		$request->validate([
            'name' => 'required',
            'email' => 'required',
			'password' => 'required'
		]);

		$user_type = Auth::user()->user_type;
		if($user_type == 3){
			$user = User::create([
				'name' => $request->name,
				'email' => $request->email,
				'password' => bcrypt($request->password),
				'user_type' => 2
			]);
	
			return 0;
		}

		
	}

    public function getLocalsByManager(Request $request){
        return $this->service->getLocalsByManager();
    }

}
