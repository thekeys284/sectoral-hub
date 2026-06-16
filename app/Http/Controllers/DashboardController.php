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
        $monitoringData = Kegiatan::with(['opd', 'metadata', 'romantik'])->latest()->get();

        $total_kegiatan = Kegiatan::count();
        $kegiatan_with_romantik = Kegiatan::whereNotNull('romantik_id')->count();
        $kegiatan_with_metadata = Kegiatan::whereNotNull('metadata_id')->count();

        $total_daftardata = \App\Models\DaftarData::count();
        $daftardata_eligible = \App\Models\DaftarData::whereNotNull('kegiatan_id')->count();
        $daftardata_with_romantik = \App\Models\DaftarData::whereHas('kegiatan', function($q) {
            $q->whereNotNull('romantik_id');
        })->count();
        $daftardata_with_metadata = \App\Models\DaftarData::whereHas('kegiatan', function($q) {
            $q->whereNotNull('metadata_id');
        })->count();

        $stats = [
            'total_kegiatan' => $total_kegiatan,
            'total_opd'      => Opd::count(),
            'total_metadata' => Metadata::count(),
            'total_romantik' => Romantik::count(),
        ];

        $charts = [
            'kegiatan_romantik' => [$kegiatan_with_romantik, $total_kegiatan - $kegiatan_with_romantik],
            'kegiatan_metadata' => [$kegiatan_with_metadata, $total_kegiatan - $kegiatan_with_metadata],
            'daftardata_eligible' => [$daftardata_eligible, $total_daftardata - $daftardata_eligible],
            'daftardata_romantik' => [$daftardata_with_romantik, $total_daftardata - $daftardata_with_romantik],
            'daftardata_metadata' => [$daftardata_with_metadata, $total_daftardata - $daftardata_with_metadata],
        ];

        return view('pages.dashboard', compact('monitoringData', 'stats', 'charts'));
    }
    
    public function rekapitulasi()
    {   
        $rekap_opd = Opd::where('name', '!=', 'Badan Pusat Statistik Provinsi Jawa Timur') 
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
            $data = $opd->romantik()->pluck('nomor_rekomendasi');
            $title = 'Romantik';
        }

        return response()->json(['title' => $title . ' - ' . $opd->name, 'data' => $data]);
    }
}
