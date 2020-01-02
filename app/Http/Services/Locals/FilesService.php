<?php
namespace App\Http\Services\Locals;
use App\Models\s_locals\LocalRefDocument;
use App\Models\s_sys\DocumentDataMain;
use Image;
use Storage;
class FilesService
{
    public function addLogo($id_local_data_main, $file){
        $file_name = uniqid().".png";


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

        $this->createThumbnail($filePath);
    }

    public function createThumbnail($file_path) {
        $file_path_orginal = "../storage/app/".$file_path;
        // download and create gd image
        $image = ImageCreateFromString(file_get_contents($file_path_orginal));

        // calculate resized ratio
        // Note: if $height is set to TRUE then we automatically calculate the height based on the ratio
        $width = 64;
        $height = 64;
        // create image 
        $output = ImageCreateTrueColor($width, $height);
        ImageCopyResampled($output, $image, 0, 0, 0, 0, $width, $height, ImageSX($image), ImageSY($image));

        // save image
        ob_start();
        imagepng($output, null, 80);
        $contents = ob_get_contents();
        ob_end_clean();
        // return resized image
        Storage::disk('local')->put($file_path, file_get_contents($contents));
    }
}
