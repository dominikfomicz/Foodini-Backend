<?php

namespace App\Http\Services\Locals;

use App\Models\s_locals\LocalRefDocument;
use App\Models\s_sys\DocumentDataMain;

class FilesService
{
    public function addLogo($id_local_data_main, $file){
        $doc = New DocumentDataMain();
        $doc->id_document_const_type = 1;
        $doc->file_name = $file->getClientOrginalName();
        $doc->save();

        $ref = new LocalRefDocument();
        $ref->id_local_data_main = $id_local_data_main;
        $ref->id_document_data_main = $doc->id;
        $ref->save();
    }
}
