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

    public function changeTag(Request $request){
        return $this->service->changeTag($request->id_tag_data_main, $request->name, $request->description, $request->id_tag_const_category);
    }

    public function getList(){
        return $this->service->getList();
    }

}
