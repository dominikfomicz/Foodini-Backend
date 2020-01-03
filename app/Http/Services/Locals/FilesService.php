<?php
namespace App\Http\Services\Locals;
use App\Models\s_locals\LocalRefDocument;
use App\Models\s_sys\DocumentDataMain;
use Image;
use Storage;
class FilesService
{
    public function addLogo($id_local_data_main, $file){
        // $file_name = uniqid().".png";
        $file_name = "logo.png";

        $image = Image::make($file);

        $image->resize(80, 80);
        //kom

        $doc = New DocumentDataMain();
        $doc->id_document_const_type = 1;
        $doc->file_name = $file_name;
        $doc->save();

        $ref = new LocalRefDocument();
        $ref->id_local_data_main = $id_local_data_main;
        $ref->id_document_data_main = $doc->id;
        $ref->save();
        $filePath = "public/locals/".$id_local_data_main."/".$file_name;
        Storage::disk('local')->put($filePath, $image->encode());

    }

    public function addBackground($id_local_data_main, $file){
        // $file_name = uniqid().".png";
        $file_name = "background.png";

        $image = Image::make($file);

        $image->resize(400, null, function ($constraint) {
            $constraint->aspectRatio();
        });

        $doc = New DocumentDataMain();
        $doc->id_document_const_type = 2;
        $doc->file_name = $file_name;
        $doc->save();

        $ref = new LocalRefDocument();
        $ref->id_local_data_main = $id_local_data_main;
        $ref->id_document_data_main = $doc->id;
        $ref->save();
        $filePath = "public/locals/".$id_local_data_main."/".$file_name;
        Storage::disk('local')->put($filePath, $image->encode());

    }
}
