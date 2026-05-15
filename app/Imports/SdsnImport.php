<?php

namespace App\Imports;

use App\Models\Sdsn;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // Gunakan ini jika baris pertama Excel adalah nama kolom

class SdsnImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Sdsn([
            // Jika pakai WithHeadingRow, indeks array memakai nama kolom di Excel (huruf kecil/slug)
            'kode_sdsn'             => $row['kode_sdsn'],
            'nama_data'             => $row['nama_data'],
            'konsep'                => $row['konsep'],
            'definisi'              => $row['definisi'],
            'klasifikasi_penyajian' => $row['klasifikasi_penyajian'] ?? null,
            'ukuran'                => $row['ukuran'] ?? null,
            'satuan'                => $row['satuan'] ?? null,
            'tahun_penetapan'       => $row['tahun_penetapan'],
        ]);
    }
}