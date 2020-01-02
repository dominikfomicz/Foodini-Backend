<?php

namespace App\Http\Services\Locals;

use App\Models\s_locals\LocalRefDocument;
use App\Models\s_sys\DocumentDataMain;
use Storage;

class FilesService
{
    public function addLogo($id_local_data_main, $file){

        $file_name = uniqid().".png";

        $image = imagepng(imagecreatefromstring(file_get_contents($file)), $file_name);

        $image = $this->resize_image($image, 128, 128);

        $doc = New DocumentDataMain();
        $doc->id_document_const_type = 1;
        $doc->file_name = $file->getClientOriginalName();
        $doc->save();

        $ref = new LocalRefDocument();
        $ref->id_local_data_main = $id_local_data_main;
        $ref->id_document_data_main = $doc->id;
        $ref->save();

        $filePath = "locals/".$id_local_data_main."/".$file_name;
        Storage::disk('local')->put($filePath, file_get_contents($file));
        return $filePath;
    }

    public function resize_image($file, $w, $h, $crop=FALSE) {
        list($width, $height) = getimagesize($file);
        $r = $width / $height;
        if ($crop) {
            if ($width > $height) {
                $width = ceil($width-($width*abs($r-$w/$h)));
            } else {
                $height = ceil($height-($height*abs($r-$w/$h)));
            }
            $newwidth = $w;
            $newheight = $h;
        } else {
            if ($w/$h > $r) {
                $newwidth = $h*$r;
                $newheight = $h;
            } else {
                $newheight = $w/$r;
                $newwidth = $w;
            }
        }
        $src = imagecreatefromjpeg($file);
        $dst = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        return $dst;
    }
}
