<?php

namespace App\Http\Services\Manager;
use App\Http\Repositories\Manager\ManagerRepository;
use App\Models\s_locals\WorkerRefUser;
use App\Models\s_locals\ManagerRefUser;
use Auth;
use App\User;

class ManagerService
{

    public function getLocalsByManager(){
        $user = Auth::user();
        if($user->user_type == 3){
            $locals = collect(ManagerRepository::getLocalsByManager($user->id));
            foreach($locals AS $local){
                $local->tags = collect(ManagerRepository::getTagsByLocal($local->local_id));
            }
            return json_encode($locals);
        }

    }

    public function registerWorker($id_local_data_main, $uuid){
        $id_user = Auth::user()->id;
        $user_type = Auth::user()->user_type;

        $manager = ManagerRefUser::where('id_user', $id_user)->where('id_local_data_main', $id_local_data_main)->first();

		if($user_type == 3 && $manager != NULL){
            $user = User::where('email', $uuid)->first();
            $user->user_type = 2;
            $user->save();

            $worker = New WorkerRefUser();
            $worker->id_user = $user->id;
            $worker->id_local_data_main = $id_local_data_main;
            $worker->save();

			return json_encode(0);
        }
    }
}
