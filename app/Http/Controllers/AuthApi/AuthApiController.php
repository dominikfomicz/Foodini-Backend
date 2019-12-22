<?php

namespace App\Http\Controllers\AuthApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthApiController extends Controller
{
	public function generateAccessToken($user){
		$token = $user->createToken($user->email.'-'.now());

		return $token->accessToken;
	}


	public function register(Request $request){
		$request->validate([
			'name' => 'required',
			'password' => 'required'
		]);


		$user = User::create([
			'name' => $request->name,
			'password' => bcrypt($request->password)
		]);

        // return response()->json($user);
        return 1;
	}
}
