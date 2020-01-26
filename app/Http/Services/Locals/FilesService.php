<?php
namespace App\Http\Services\Locals;
use App\Models\s_locals\LocalRefDocument;
use App\Models\s_sys\DocumentDataMain;
use Image;
use Storage;
use \Auth;

class FilesService
{
    public function addLogo($id_local_data_main, $file){
        If (Auth::user()->user_type == -1){
            // $file_name = uniqid().".png";
            $file_name = "logo.png";

            $image = Image::make($file);

            $image->resize(160, 160);
            //kom
            $ref = LocalRefDocument::where('id_local_data_main', $id_local_data_main)->where('id_document_const_type', 1)->first();
            if($ref == null){
                $doc = New DocumentDataMain();
                $doc->id_document_const_type = 1;
                $doc->file_name = $file_name;
                $doc->save();

                $ref = new LocalRefDocument();
                $ref->id_local_data_main = $id_local_data_main;
                $ref->id_document_data_main = $doc->id;
                $ref->id_document_const_type = 1;
                $ref->save();
            }


            $filePath = "public/locals/".$id_local_data_main."/".$file_name;
            Storage::disk('local')->put($filePath, $image->encode());
        }


    }

    public function addBackground($id_local_data_main, $file){
        If (Auth::user()->user_type == -1){
            // $file_name = uniqid().".png";
            $file_name = "background.png";

            $image = Image::make($file);

            $image->resize(700, null, function ($constraint) {
                $constraint->aspectRatio();
            });

            $ref = LocalRefDocument::where('id_local_data_main', $id_local_data_main)->where('id_document_const_type', 2)->first();
            if($ref == null){

                $doc = New DocumentDataMain();
                $doc->id_document_const_type = 2;
                $doc->file_name = $file_name;
                $doc->save();

                $ref = new LocalRefDocument();
                $ref->id_local_data_main = $id_local_data_main;
                $ref->id_document_data_main = $doc->id;
                $ref->id_document_const_type = 2;
                $ref->save();

            }

            $filePath = "public/locals/".$id_local_data_main."/".$file_name;
            Storage::disk('local')->put($filePath, $image->encode());
        }


    }

    public function addMenuPhoto($id_local_data_main, $file){
        If (Auth::user()->user_type == -1){
            // $file_name = uniqid().".png";
            $file_name = "menu.png";

            $ref = LocalRefDocument::where('id_local_data_main', $id_local_data_main)->where('id_document_const_type', 3)->first();
            if($ref == null){
                $doc = New DocumentDataMain();
                $doc->id_document_const_type = 3;
                $doc->file_name = $file_name;
                $doc->save();

                $ref = new LocalRefDocument();
                $ref->id_local_data_main = $id_local_data_main;
                $ref->id_document_data_main = $doc->id;
                $ref->id_document_const_type = 3;
                $ref->save();
            }

            $filePath = "public/locals/".$id_local_data_main."/".$file_name;
            Storage::disk('local')->put($filePath, file_get_contents($file));
        }


    }

    public function addMapLogo($id_local_data_main, $file){
        If (Auth::user()->user_type == -1){
            // $file_name = uniqid().".png";
            $file_name = "map.png";
            $image = Image::make($file);

            $image->resize(90, null, function ($constraint) {
                $constraint->aspectRatio();
            });

            $ref = LocalRefDocument::where('id_local_data_main', $id_local_data_main)->where('id_document_const_type', 5)->first();
            if($ref == null){
                $doc = New DocumentDataMain();
                $doc->id_document_const_type = 5;
                $doc->file_name = $file_name;
                $doc->save();

                $ref = new LocalRefDocument();
                $ref->id_local_data_main = $id_local_data_main;
                $ref->id_document_data_main = $doc->id;
                $ref->id_document_const_type = 5;
                $ref->save();
            }


            $filePath = "public/locals/".$id_local_data_main."/".$file_name;
            Storage::disk('local')->put($filePath, $image->encode());
        }
    }

    public function addMenuPhotos($id_local_data_main, $files){
        If (Auth::user()->user_type == -1){

            $files_remove = Storage::allFiles("public/locals/".$id_local_data_main);

            foreach($files_remove As $file_remove){
                $file_name = basename($file_remove);
                if (strpos($file_name, 'menu_') !== false) {
                    Storage::delete($file_remove);
                }
            }
            $i = 1;
            foreach($files as $file){
                $file_name = 'menu_'.$i.'.png';
                $ref = LocalRefDocument::where('id_local_data_main', $id_local_data_main)->where('id_document_const_type', 3)->first();

                if($ref == null){
                    $doc = New DocumentDataMain();
                    $doc->id_document_const_type = 3;
                    $doc->file_name = $file_name;
                    $doc->save();

                    $ref = new LocalRefDocument();
                    $ref->id_local_data_main = $id_local_data_main;
                    $ref->id_document_data_main = $doc->id;
                    $ref->id_document_const_type = 3;
                    $ref->save();
                }

                $filePath = "public/locals/".$id_local_data_main."/".$file_name;
                Storage::disk('local')->put($filePath, file_get_contents($file));
                $i++;
            }
            return json_encode($i." - ".$id_local_data_main);
        }


    }

    public function countMenuPhotos($id_local_data_main){
        $files = Storage::allFiles("public/locals/".$id_local_data_main);
            $count = 0;
            foreach($files As $file){
                $file_name = basename($file);
                if (strpos($file_name, 'menu_') !== false) {
                    $count++;
                }
            }
        return json_encode($count);
    }
}
