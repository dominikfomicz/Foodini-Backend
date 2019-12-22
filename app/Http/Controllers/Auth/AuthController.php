<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
	protected function generateAccessToken($user){
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

		return response()->json($user);
	}
}
