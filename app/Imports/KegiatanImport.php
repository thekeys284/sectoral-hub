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
            'nama_kegiatan'         => $row['nama_kegiatan'],
            'periode_kegiatan'      => $row['periode_kegiatan'],
            'tahun_kegiatan'        => $row['tahun_kegiatan'],
            'cara_pengumpulan_data' => $row['cara_pengumpulan_data'],
            'data_utama'            => $row['data_utama'],
            'data_prioritas'        => $row['data_prioritas'],
            'aksesbilitas'          => $row['aksesbilitas'],
            'opd_id'                => $row['opd_id'],
            'deskripsi'             => $row['deskripsi'],
            'metadata_id'           => $row['metadata_id'],
            'romantik_id'           => $row['romantik_id'],
        ]);
    }
}
