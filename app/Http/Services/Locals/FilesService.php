<?php
namespace App\Http\Services\Locals;
use App\Models\s_locals\LocalRefDocument;
use App\Models\s_sys\DocumentDataMain;
use Storage;
class FilesService
{
    public function addLogo($id_local_data_main, $file){
        $file_name = uniqid().".png";

        //$image = imagepng(imagecreatefromstring(file_get_contents($file)), $file_name);
        //$image = resize_image($image, 128, 128);
        $doc = New DocumentDataMain();
        $doc->id_document_const_type = 1;
        $doc->file_name = $file_name;
        $doc->save();
        $ref = new LocalRefDocument();
        $ref->id_local_data_main = $id_local_data_main;
        $ref->id_document_data_main = $doc->id;
        $ref->save();
        $filePath = "public/locals/".$id_local_data_main."/".$file_name;
        Storage::disk('local')->put($filePath, file_get_contents($file));
    }
}
