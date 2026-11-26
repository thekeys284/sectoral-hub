<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kegiatan;
use App\Models\Opd;
use App\Models\Metadata;
use App\Models\Romantik;
use App\Models\Sdsn;

class DashboardController extends Controller
{ 
    public function index()
    {
        $kegStats = \Illuminate\Support\Facades\DB::table('kegiatan')
            ->selectRaw('COUNT(*) as total, COUNT(romantik_id) as with_romantik, COUNT(metadata_id) as with_metadata')
            ->first();

        $ddStats = \Illuminate\Support\Facades\DB::table('daftar_data')
            ->leftJoin('kegiatan', 'daftar_data.kegiatan_id', '=', 'kegiatan.id')
            ->selectRaw('COUNT(*) as total, COUNT(daftar_data.kegiatan_id) as eligible, COUNT(kegiatan.romantik_id) as with_romantik, COUNT(kegiatan.metadata_id) as with_metadata')
            ->first();

        $total_kegiatan = (int) ($kegStats->total ?? 0);
        $kegiatan_with_romantik = (int) ($kegStats->with_romantik ?? 0);
        $kegiatan_with_metadata = (int) ($kegStats->with_metadata ?? 0);

        $total_daftardata = (int) ($ddStats->total ?? 0);
        $daftardata_eligible = (int) ($ddStats->eligible ?? 0);
        $daftardata_with_romantik = (int) ($ddStats->with_romantik ?? 0);
        $daftardata_with_metadata = (int) ($ddStats->with_metadata ?? 0);

        $stats = [
            'total_kegiatan' => $total_kegiatan,
            'total_opd'      => Opd::whereNull('parent_id')->count(),
            'total_metadata' => Metadata::count(),
            'total_romantik' => Romantik::count(),
        ];

        $charts = [
            'kegiatan_romantik'   => [$kegiatan_with_romantik, max(0, $total_kegiatan - $kegiatan_with_romantik)],
            'kegiatan_metadata'   => [$kegiatan_with_metadata, max(0, $total_kegiatan - $kegiatan_with_metadata)],
            'daftardata_eligible' => [$daftardata_eligible, max(0, $total_daftardata - $daftardata_eligible)],
            'daftardata_romantik' => [$daftardata_with_romantik, max(0, $total_daftardata - $daftardata_with_romantik)],
            'daftardata_metadata' => [$daftardata_with_metadata, max(0, $total_daftardata - $daftardata_with_metadata)],
        ];

        return view('pages.dashboard', compact('stats', 'charts'));
    }
    
    public function rekapitulasi()
    {   
        $rekap_opd = Opd::where('name', '!=', 'Badan Pusat Statistik Provinsi Jawa Timur')
            ->whereNull('parent_id')
            ->withCount([
                'daftardata', 
                'kegiatan', 
                'romantik',
                'metadata as metadata_keg_count', 
            ])
            ->get();

        return view('pages.rekapitulasi', compact('rekap_opd'));
    }

    public function monitoring()
    {
        $rekap_opd = Opd::where('name', '!=', 'Badan Pusat Statistik Provinsi Jawa Timur')
            ->whereNull('parent_id')
            ->withCount([
                'daftardata',
                'kegiatan',
                'romantik',
                'metadata',
                'daftardata as sdsn_count' => function ($query) {
                    $query->whereNotNull('kode_sdsn'); // Menghitung SDSN berdasarkan daftar data yang sudah di-mapping
                }
            ])
            ->get();

        return view('pages.monitoring', compact('rekap_opd'));
    }

    public function detailMonitoring($opd_id, $type)
    {
        $opd = Opd::findOrFail($opd_id);
        $data = [];
        $title = '';

        if ($type === 'daftardata') {
            $data = $opd->daftardata()->pluck('nama_data');
            $title = 'Daftar Data';
        } elseif ($type === 'kegiatan') {
            $data = $opd->kegiatan()->pluck('nama_kegiatan');
            $title = 'Kegiatan';
        } elseif ($type === 'sdsn') {
            $data = $opd->daftardata()->whereNotNull('kode_sdsn')->get()->map(function($item) {
                return $item->kode_sdsn . ' - ' . $item->nama_data;
            });
            $title = 'Daftar Data (SDSN)';
        } elseif ($type === 'metadata') {
            $data = $opd->metadata()->pluck('judul_kegiatan');
            $title = 'Metadata';
        } elseif ($type === 'romantik') {
            $data = $opd->romantik()->pluck('judul_kegiatan');
            $title = 'Romantik';
        }

        return response()->json(['title' => $title . ' - ' . $opd->name, 'data' => $data]);
    }
}
