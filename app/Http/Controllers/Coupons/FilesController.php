<?php

namespace App\Http\Controllers\Coupons;

use App\Http\Controllers\Controller;
use App\Http\Services\Coupons\FilesService;
use Illuminate\Http\Request;

class FilesController extends Controller
{
    private $service;

    public function __construct(FilesService $service){
        $this->service = $service;
    }

    public function addLogo(Request $request){
        return $this->service->addLogo($request->id_coupon_data_main, $request->file('image'));
    }
}
