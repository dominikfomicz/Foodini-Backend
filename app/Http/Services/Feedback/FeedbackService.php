<?php

namespace App\Http\Services\Feedback;

use App\Models\s_sys\FeedbackDataMain;
use Auth;

class FeedbackService
{
    public function add($message, $name){
            $item = new FeedbackDataMain();
            $item->id_user_data_main = Auth::user()->id;
            $item->message = $message;
            $item->name = $name;
            $item->save();
    }


}
