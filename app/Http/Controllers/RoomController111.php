<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreAsnRequest;
use App\Http\Requests\UpdateAsnRequest;
use App\Traits\UserWarehousesTrait;
use Laravel\Sanctum\PersonalAccessToken;

class AsnController extends Controller
{


    public function import(Request $request)
    {
        if ($request->hasFile('template')) {
            $path = $request->file('template')->getRealPath();
            $data = \Excel::load($path)->get();

            if ($data->count() > 0) {
                $rows = $data->toArray();
                foreach ($rows as $row) {
                    $inserts[] = [
                        'room_desc' => $row['room_desc'],
                        'bldg' => $row['bldg'],
                    ];
                }
            }
            $chuncked = array_chunk($inserts, 10);
            if (empty($inserts)) {
                dd('Request data does not have any files to import.');
            } else {
                foreach ($chuncked as $inserts) {
                    \DB::table('rooms')->insert($inserts);
                }
                dd('record inserted');
            }
        }
    }







}
