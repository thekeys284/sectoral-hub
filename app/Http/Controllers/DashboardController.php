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
}
