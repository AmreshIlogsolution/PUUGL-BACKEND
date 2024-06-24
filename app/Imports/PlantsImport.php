<?php

namespace App\Imports;

use App\Models\Plant;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Hash;



class PlantsImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        return new Plant([
            'name' => $row[0],
            'title' => $row[1],
        ]);
    }
}
