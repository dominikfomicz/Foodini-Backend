<?php
namespace App\Http\Services\Coupons;

use App\Models\s_coupons\CouponRefDocument;
use App\Models\s_locals\LocalRefDocument;
use App\Models\s_sys\DocumentDataMain;
use Image;
use Storage;
class FilesService
{
    public function addLogo($id_coupon_data_main, $file){
        $file_name = uniqid().".png";
        $doc = New DocumentDataMain();
        $doc->id_document_const_type = 1;
        $doc->file_name = $file_name;
        $doc->save();

        $ref = new CouponRefDocument();
        $ref->id_coupon_data_main = $id_coupon_data_main;
        $ref->id_document_data_main = $doc->id;
        $ref->save();
        $filePath = "public/coupons/".$id_coupon_data_main."/".$file_name;
        Storage::disk('local')->put($filePath, file_get_contents($file));

    }
}
