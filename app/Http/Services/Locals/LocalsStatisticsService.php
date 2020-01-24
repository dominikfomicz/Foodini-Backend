<?php

namespace App\Http\Services\Locals;
use App\Models\s_locals\LocalDataMain;

class LocalsStatisticsService
{
    public function showFacebookCount($id_local_data_main){
        $local = LocalDataMain::find($id_local_data_main);
        $local->show_facebook_count = $local->show_facebook_count + 1;
        $local->save();
    }
}
