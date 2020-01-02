<?php

namespace App\Http\Controllers\Locals;

use App\Http\Controllers\Controller;
use App\Http\Services\Locals\FilesService;
use Illuminate\Http\Request;

class FilesController extends Controller
{
    private $service;

    public function __construct(FilesService $service){
        $this->service = $service;
    }

    public function addLogo(Request $request){
        return $this->service->addLogo($request->id_local_data_main, $request->file('image'));
    }

    public function addBackground(Request $request){
        return $this->service->addBackground($request->id_local_data_main, $request->file('image'));
    }
}
