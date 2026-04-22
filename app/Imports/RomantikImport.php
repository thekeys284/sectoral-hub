<?php

namespace App\Imports;

use App\Models\Romantik;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class RomantikImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Romantik([
            'opd_id'            => $row['opd_id'],
            'judul_kegiatan'    => $row['judul_kegiatan'],
            'tahun_kegiatan'    => $row['tahun_kegiatan'],
            'nomor_rekomendasi' => $row['nomor_rekomendasi'],
            'tgl_pengajuan'     => $row['tgl_pengajuan'],
            'tgl_perbaikan_terakhir' => $row['tgl_perbaikan_terakhir'],
            'tgl_selesai'       => $row['tgl_selesai'],
            'lama_pemeriksaan'  => $row['lama_pemeriksaan'],
            'status_pengajuan'  => $row['status_pengajuan'],
            'status_rekomendasi'=> $row['status_rekomendasi']   
        ]);
    }
}
