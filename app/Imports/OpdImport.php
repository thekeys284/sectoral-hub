<?php

namespace App\Imports;

use App\Models\Opd;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class OpdImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Opd([
            'name'       => $row['name'],
            'alamat'       => $row['alamat'],
            'logo_path'    => $row['logo_path'],
            'email'        => $row['email'],
            'no_kantor'    => $row['no_kantor'],
            'pembina_id'   => $row['pembina_id'],
        ]);
    }
}
