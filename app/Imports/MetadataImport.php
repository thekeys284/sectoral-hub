<?php

namespace App\Imports;

use App\Models\Metadata;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class MetadataImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Metadata([
            'opd_id'            => $row['opd_id'],
            'judul_kegiatan'    => $row['judul_kegiatan'],
            'periode_submission'=> $row['periode_submission'],
            'tanggal_submission'=> $row['tanggal_submission'],
            'status'            => $row['status']
        ]);
    }
}
