<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait Downloadable
{

    public function getDownload()
    {
        //PDF file is stored under project/public/download/info.pdf            
        //$file="./downloads/info.pdf";
        //return Response::download($file);
    }
}
