<?php

use App\Http\Controllers\{
    ProfileController, UserController, OpdController, 
    KegiatanController, MetadataController, RomantikController, 
    DaftardataController, EventController, DashboardController, PelaporanController, SdsnController
};
use Illuminate\Support\Facades\Route;

// 1. PUBLIC / DASHBOARD (Semua yang login bisa akses)
Route::get('/', function (){
    return redirect()->route('login');
})->name('home');
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// 2. AUTH GENERAL (Profil & Event - Semua Role Bisa Akses)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/role/switch', [ProfileController::class, 'switchRole'])->name('role.switch');
    Route::get('/metadata-list', [MetadataController::class, 'table'])->name('metadata.table');  
    Route::get('/romantik-list', [RomantikController::class, 'table'])->name('romantik.table');   
    Route::get('/daftardata-list', [DaftardataController::class, 'table'])->name('daftardata.table');         
    Route::get('/whatsnext', [EventController::class, 'whatsnext'])->name('pages.whatsnext'); 
    Route::get('/rekapitulasi', [DashboardController::class, 'rekapitulasi'])->name('pages.rekapitulasi'); 
    Route::get('/monitoring', [DashboardController::class, 'monitoring'])->name('pages.monitoring'); 
    Route::get('/monitoring/detail/{opd_id}/{type}', [DashboardController::class, 'detailMonitoring'])->name('monitoring.detail');

});

// 3. MASTER & DATA (MULTI-ROLE)
Route::middleware(['auth'])->group(function () {
    // Master
    Route::prefix('master')->name('master.')->group(function () {
        Route::resource('kegiatan', KegiatanController::class);
        Route::resource('sdsn', SdsnController::class);
        Route::post('sdsn/import', [SdsnController::class, 'import'])->name('sdsn.import');


        // Role Admin dan walidata
        Route::middleware(['role:admin,walidata'])->group(function() {
            Route::resource('opd', OpdController::class);
            Route::post('opd/import', [OpdController::class, 'import'])->name('opd.import');
            Route::resource('users', UserController::class);
            Route::post('user/import', [UserController::class, 'import'])->name('users.import');
            Route::post('kegiatan/import', [KegiatanController::class, 'import'])->name('kegiatan.import');
        });
    });

    // Data
    Route::prefix('data')->name('data.')->group(function () {    
        Route::resource('daftardata', DaftardataController::class)->only(['index', 'show']);
        Route::resource('metadata', MetadataController::class)->only(['index', 'show']);
        Route::resource('romantik', RomantikController::class)->only(['index', 'show']);

        // Role Admin
        Route::middleware(['role:admin'])->group(function () {
            Route::resource('daftardata', DaftardataController::class)->except(['index', 'show']);
            Route::resource('metadata', MetadataController::class)->except(['index', 'show']);
            Route::resource('romantik', RomantikController::class)->except(['index', 'show']);
            Route::resource('event', EventController::class);
            
            Route::post('daftardata/import', [DaftardataController::class, 'import'])->name('daftardata.import');
            Route::post('metadata/import', [MetadataController::class, 'import'])->name('metadata.import');
            Route::post('romantik/import', [RomantikController::class, 'import'])->name('romantik.import');
        });
    });
});

// 4. PELAPORAN
Route::middleware(['auth'])->group(function () {
    Route::prefix('pelaporan')->name('pelaporan.')->group(function () {
        Route::resource('metadata', PelaporanController::class);
    });
});


require __DIR__.'/auth.php';