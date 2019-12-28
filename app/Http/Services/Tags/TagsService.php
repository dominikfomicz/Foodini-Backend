<?php

namespace App\Http\Services\Tags;
use App\Http\Repositories\Tags\TagsRepository;
use App\Models\s_tags\TagDataMain;
use Auth;

class TagsService
{

    public function addTag($name, $description){

        $new_tag = new TagDataMain();
        $new_tag->name = $name;
        $new_tag->description = $description;
        $new_tag->save();

    }

    public function getList(){

        $tags = TagDataMain::all();
        return $tags;

    }
}
