<?php

namespace App\Http\Services\Manager;
use App\Http\Repositories\Manager\ManagerRepository;
use Auth;

class ManagerService
{

    public function getLocalsByManager(){
        $user = Auth::user();
        if($user->user_type == 3){
            $locals = collect(ManagerRepository::getLocalsByManager($user->id));
            foreach($locals AS $local){
                $local->tags = collect(ManagerRepository::getTagsByLocal($local->local_id))->first();
            }
            return json_encode($locals);
        }
        
    }
}
