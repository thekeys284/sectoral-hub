<?php

namespace App\Imports;

use App\Models\Kegiatan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class KegiatanImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Kegiatan([
            'nama_kegiatan'         => $row['nama_kegiatan'] ?? null,
            'periode_kegiatan'      => $row['periode_kegiatan'] ?? null,
            'tahun_kegiatan'        => $row['tahun_kegiatan'] ?? null,
            'cara_pengumpulan_data' => $row['cara_pengumpulan_data'] ?? null,
            'data_utama'            => $row['data_utama'] ?? null,
            'data_prioritas'        => empty($row['data_prioritas']) ? null : $row['data_prioritas'],
            'aksesbilitas'          => $row['aksesbilitas'] ?? null,
            'opd_id'                => $row['opd_id'] ?? null,
            'deskripsi'             => $row['deskripsi'] ?? null,
            'metadata_id'           => empty($row['metadata_id']) ? null : $row['metadata_id'],
            'romantik_id'           => empty($row['romantik_id']) ? null : $row['romantik_id'],
        ]);
    }
}
