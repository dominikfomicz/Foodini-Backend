<?php

namespace App\Http\Controllers\Feedback;

use App\Http\Controllers\Controller;
use App\Http\Services\Feedback\FeedbackService;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    private $service;

    public function __construct(FeedbackService $service){
        $this->service = $service;
    }

    public function add(Request $request){
        return $this->service->add($request->message, $request->name);
    }

}
