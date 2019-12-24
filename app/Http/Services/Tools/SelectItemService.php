<?php

namespace App\Http\Services\Tools;

use App\Models\s_locals\CityConstType;
use App\Models\s_locals\WeekdayConstType;
use App\Models\s_tags\TagDataMain;
use Illuminate\Support\Collection;
use DB;

class SelectItemService {

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

    private function getCityConstType() {
        $list = CityConstType::select("id", "name")->get();
        return $this->convertToReturnArray($list, "id", "name");
    }   

    private function getWeekdayConstType() {
        $list = WeekdayConstType::select("id", "name")->get();
        return $this->convertToReturnArray($list, "id", "name");
    }

    private function getTagDataMain() {
        $list = TagDataMain::select("id", "name")->get();
        return $this->convertToReturnArray($list, "id", "name");
    }
}
