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
            'uuid' => 'required',
		]);


		$user = User::create([
            'uuid' => $request->uuid
		]);

        return 0;
	}

	public function loginUuid(Request $request){
		$this->validate($request, [
			'uuid' => 'required',
			]);
		$user = User::where('user_type', 0)
			->where('uuid', $request->uuid)
			->first()
		;
		if ($user){
			Auth::login($user);
			$token = $user->createToken($user->email.'-'.now());

			return $token->accessToken;
		}
		return -1;
	}

	public function login(Request $request){
		$this->validate($request, [
			'email' => 'required|email',
			'password' => 'required',
			]);
		if (\Auth::attempt([
			'email' => $request->email,
			'password' => $request->password])
		){
			return redirect('/dashboard');
		}
		return redirect('/login')->with('error', 'Invalid Email address or Password');
	}
}
