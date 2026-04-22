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
            'opd_id'           => $row['opd_id'] ?? null,
            'nama_data'        => $row['nama_data'] ?? null,
            'satuan'           => $row['satuan'] ?? null,
            'periode'          => $row['periode'] ?? null,
            'kedalaman_kabkot' => $row['kedalaman_kabkot'] ?? null,
            'sifat_data'       => $row['sifat_data'] ?? null,
            'sumber_data'      => $row['sumber_data'] ?? null,
            'kegiatan_id'      => empty($row['kegiatan_id']) ? null : $row['kegiatan_id'],
            'aliran_data'      => $row['aliran_data'] ?? null,
            'nama_aliran_data' => $row['nama_aliran_data'] ?? null,
        ]);
    }
}
