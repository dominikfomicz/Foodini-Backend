<?php

namespace App\Listeners;

use App\User;
use Laravel\Passport\Events\AccessTokenCreated;
use Illuminate\Http\Request;
use Laravel\Passport\Token;

class LogSuccessfullLogin
{
    /**
     * Create the event listener.
     *
     * @param  Request  $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(AccessTokenCreated $event)
    {
        //
        Token::where('id', '!=', $event->tokenId)
        ->where('user_id', $event->userId)
        ->where('client_id', $event->clientId)
        // ->where('expires_at', '<', Carbon::now())
        // ->orWhere('revoked', true)
        ->delete();
        
        $user = User::find($event->userId);
        $user->last_login_date = date('Y-m-d H:i:s');
        $user->save();
    }
}