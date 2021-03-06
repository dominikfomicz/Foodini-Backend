<?php

namespace App\Http\Controllers\AuthApi;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Auth;

class AuthApiController extends Controller
{
    public function generateAccessToken($user)
    {
        $token = $user->createToken($user->email . '-' . now());

        return $token->accessToken;
    }


    public function register(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',

        ]);

        if ($validate->fails()) {
            return -1;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'user_type' => 1
        ]);

        return 0;
    }

    public function registerUuid(Request $request)
    {
        $users = User::where('email', '=', $request->uuid)->first();
        if ($users === null) {
            $user = User::create([
                'name' => $request->uuid,
                'email' => $request->uuid,
                'password' => bcrypt($request->uuid),
                'user_type' => 0
            ]);
            return 0;
        } else {
            return -1;
        }
    }

    public function getUserStatus(Request $request)
    {
        $request->validate([
            'uuid' => 'required'
        ]);


        $user = User::where('email', $request->uuid)->first();

        if ($user != null) {
            $user_type = $user->user_type;
        } else {
            $user_type = 0;
        }

        return $user_type;
    }

    public function registerEmail(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required'
        ]);
        //mail like msc@msc is correct

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'user_type' => 1
        ]);
        $user->sendEmailVerificationNotification();

        return 0;
    }
}
