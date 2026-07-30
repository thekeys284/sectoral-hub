<?php

use App\Http\Controllers\{
    ProfileController, UserController, OpdController, 
    KegiatanController, MetadataController, RomantikController, 
    DaftardataController, EventController, DashboardController, PelaporanController, SdsnController
};
use App\Http\Controllers\Admin\{
    AdminEventController, AdminQuestionController, AdminEvaluationController
};
use App\Http\Controllers\User\{
    UserEventController, UserEvaluationController, UserExamController
};
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// Route untuk callback token Majapahit
Route::get('/majapahit', [AuthenticatedSessionController::class, 'loginMajapahit'])
    ->middleware('guest')
    ->name('login.majapahit');

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
            // Route::resource('event', EventController::class);
            
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

// Manajemen pelatihan
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // CRUD Event
    Route::resource('events', AdminEventController::class);
    Route::resource('events.questions', AdminQuestionController::class);
    Route::patch('events/{id}/evaluations/status', [AdminEvaluationController::class, 'updateStatus'])
        ->name('events.evaluations.status');
    Route::resource('events.evaluations', AdminEvaluationController::class);

    // Custom Route untuk Rekap Penilaian Peserta Pelatihan
    Route::get('events/{id}/rekap', [AdminEventController::class, 'rekap'])->name('events.rekap');
});

Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    
    // 1. Menu "Pelatihan Saya" (Pelatihan yang Diikuti)
    Route::get('/my-events', [UserEventController::class, 'index'])->name('events.index');
    
    // 2. Menu "Whats Next" (Katalog Event & Pendaftaran)
    Route::get('/whatsnext', [UserEventController::class, 'whatsnext'])->name('whatsnext');

    // 3. Detail Event & Pendaftaran
    Route::get('/events/{id}', [UserEventController::class, 'show'])->name('events.show');
    Route::post('/events/{id}/register', [UserEventController::class, 'register'])->name('events.register');

    // 4. Presensi / Daftar Hadir
    Route::post('/events/{id}/absensi', [UserEventController::class, 'absensi'])->name('events.absensi');

    // 5. Ujian (Pretest & Posttest)
    Route::get('/events/{id}/exam/{type}/confirm', [UserExamController::class, 'confirm'])->name('exams.confirm');
    Route::post('/events/{id}/verify-posttest', [UserExamController::class, 'verifyPassword'])->name('exams.verify_password');
    Route::get('/events/{id}/exam/{type}', [UserExamController::class, 'show'])->name('exams.show');
    Route::post('/events/{id}/exam/{type}', [UserExamController::class, 'submit'])->name('exams.submit');

    // 6. Kuesioner Evaluasi
    Route::get('/events/{id}/evaluations', [UserEvaluationController::class, 'create'])->name('evaluations.create');
    Route::post('/events/{id}/evaluations', [UserEvaluationController::class, 'store'])->name('evaluations.store');

});
require __DIR__.'/auth.php';
