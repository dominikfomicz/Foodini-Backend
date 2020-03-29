<?php

namespace App\Http\Services\Locals;

use App\Models\s_locals\LocalDataMain;

class LocalsStatisticsService
{
    public function showFacebookCount($id_local_data_main)
    {
        $local = LocalDataMain::find($id_local_data_main);
        $local->show_facebook_count = $local->show_facebook_count + 1;
        $local->save();
    }

    public function showInstagramCount($id_local_data_main)
    {
        $local = LocalDataMain::find($id_local_data_main);
        $local->show_instagram_count = $local->show_instagram_count + 1;
        $local->save();
    }

    public function showMenuCount($id_local_data_main)
    {
        $local = LocalDataMain::find($id_local_data_main);
        $local->show_menu_count = $local->show_menu_count + 1;
        $local->save();
    }

    public function showPhonenumberCount($id_local_data_main)
    {
        $local = LocalDataMain::find($id_local_data_main);
        $local->show_phonenumber_count = $local->show_phonenumber_count + 1;
        $local->save();
    }

    public function showOrderCount($id_local_data_main)
    {
        $local = LocalDataMain::find($id_local_data_main);
        $local->show_order_count = $local->show_order_count + 1;
        $local->save();
    }
}
