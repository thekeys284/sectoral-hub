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

        $stats = [
            'total_kegiatan' => Kegiatan::count(),
            'total_opd'      => Opd::count(),
            'total_metadata' => Metadata::count(),
            'total_romantik' => Romantik::count(),
        ];

        return view('pages.dashboard', compact('monitoringData', 'stats'));
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
