<?php

namespace App\Http\Controllers\AuthApi;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class AuthApiController extends Controller
{
	public function generateAccessToken($user){
		$token = $user->createToken($user->email.'-'.now());

		return $token->accessToken;
	}


	public function register(Request $request){
		$request->validate([
            'name' => 'required',
            'email' => 'required',
			'password' => 'required'
		]);


		$user = User::create([
            'name' => $request->name,
            'email' => $request->email,
			'password' => bcrypt($request->password),
			'user_type' => 1
		]);

        return 0;
	}

	public function registerUuid(Request $request){
		$request->validate([
            'uuid' => 'required'
		]);


		$user = User::create([
            'name' => $request->uuid,
            'email' => $request->uuid,
			'password' => bcrypt($request->uuid)
		]);

        return 0;
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
}
