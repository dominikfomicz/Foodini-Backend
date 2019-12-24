<?php


namespace App\Http\Controllers\Tools;

use Illuminate\Http\Request;
use App\Http\Services\Tools\SelectItemService;

class SelectItemController extends \App\Http\Controllers\Controller {

    private $service;

    public function __construct(SelectItemService $service) {
        $this->service = $service;
    }

    public function getList(Request $request) {
        return $this->service->getList($request->all());
    }

}
