<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\PlantsImport;
use Maatwebsite\Excel\Facades\Excel;

class PlantController extends Controller
{
    public function import(Request $request)
    {
        try {

            Excel::import(new PlantsImport, request()->file('PlantXlsfile'));
            $jResponse = ["success" => "Sucessfully Uploaded", 'status' => '200'];
            return \Response::json($jResponse, 200);
        } catch (\Exception $error) {
            return $error->getMessage();
        }


    }
}
