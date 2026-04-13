<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kegiatan;
use App\Models\Opd;
use App\Models\Metadata;
use App\Models\Romantik;

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
}
