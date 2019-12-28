<?php

namespace App\Http\Controllers\Tags;

use App\Http\Controllers\Controller;
use App\Http\Services\Tags\TagsService;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    private $service;

    public function __construct(TagsService $service){
        $this->service = $service;
    }

    public function addTag(Request $request){
        return $this->service->addTag($request->name, $request->description);
    }

    public function getList(){
        return $this->service->getList();
    }

}
