<?php

namespace App\Http\Services\Tags;
use App\Http\Repositories\Tags\TagsRepository;
use App\Models\s_tags\TagDataMain;
use Auth;

class TagsService
{

    public function changeTag($id_tag_data_main, $name, $description, $id_tag_const_category){

        if($id_tag_data_main == -1){
            $new_tag = new TagDataMain();
        } else {
            $new_tag = TagDataMain::find($id_tag_data_main);
        }

        $new_tag->name = $name;
        $new_tag->description = $description;
        $new_tag->id_tag_const_category = $id_tag_const_category;
        $new_tag->save();

    }

    public function getList(){

        $tags = TagDataMain::all();
        return $tags;

    }
}
