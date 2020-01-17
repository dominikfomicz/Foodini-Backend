<?php

namespace App\Listeners;

use App\User;
use Illuminate\Auth\Events\AccessTokenCreated;
use Illuminate\Http\Request;

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
        $user = User::find($event->userId);
        $user->last_login_date = date('Y-m-d H:i:s');
        $user->save();
    }
}