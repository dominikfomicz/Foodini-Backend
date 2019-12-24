<?php

namespace App\Http\Services\Tools;

use App\Models\s_locals\CityConstType;
use Illuminate\Support\Collection;
use DB;

class SelectItemService {

    //
    public function getList($input_array) {
        $method = "get{$input_array['app_list_string']}";
        if (is_callable(array($this, $method))) {
            $reflect = new \ReflectionMethod(get_class(), $method);
            if (count($reflect->getParameters()) > 0) {
                unset($input_array['app_list_string']);
                $return = $this->{$method}($input_array);
            } else
                $return = $this->{$method}();
        } else {
            $return = 0;
        }

        return $return;
    }

    //
    private function convertToReturnArray($array_list, $item_id_prop, $item_name_prop) {
        $return_array = [];        
        foreach ($array_list as $item) {
            $tmpItem = new \stdClass();
            $tmpItem->item_id = $item->{$item_id_prop};
            $tmpItem->item_name = $item->{$item_name_prop};
            $return_array[] = $tmpItem;
        }

        return $return_array;
    }

    // 
    private function getCityConstType() {
        //$list = \App\Models\s_case\HistoryConstType::where("visible", "1")->select("id", "name")->get();
        $list = CityConstType::select("id", "name")->get();
        return $this->convertToReturnArray($list, "id", "name");
    }   
}
