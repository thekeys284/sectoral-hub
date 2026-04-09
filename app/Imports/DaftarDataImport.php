<?php

namespace App\Imports;

use App\Models\DaftarData;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class DaftarDataImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new DaftarData([
            'opd_id'           => $row['opd_id'],
            'nama_data'        => $row['nama_data'],
            'satuan'           => $row['satuan'],
            'periode'          => $row['periode'],
            'kedalaman_kabkot' => $row['kedalaman_kabkot'],
            'sifat_data'       => $row['sifat_data'],
            'sumber_data'      => $row['sumber_data'],
        ]);
    }
}
