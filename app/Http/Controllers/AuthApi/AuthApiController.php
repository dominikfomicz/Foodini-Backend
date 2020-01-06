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
			'password' => bcrypt($request->password)
		]);

        return 0;
	}

	public function registerUuid(Request $request){
		$request->validate([
            'name' => 'required',
            'email' => 'required',
			'password' => 'required'
		]);


		$user = User::create([
            'name' => $request->uuid,
            'email' => $request->uuid,
			'password' => bcrypt($request->uuid)
		]);

        return 0;
	}
}
