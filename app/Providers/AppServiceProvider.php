<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::composer('layouts.navbars.auth.sidenav', function ($view) {
            
            $menuItems = [
                [
                    'header' => 'Dashboard',
                    'roles'  => ['admin', 'walidata', 'produsen','pembina'],
                    'items'  => [
                        [
                            'title' => 'Dashboard',
                            'icon'  => 'ni ni-chart-bar-32 text-dark',
                            'route' => 'dashboard',
                            'active' => request()->routeIs('dashboard')
                        ],
                        [
                            'title' => 'Monitoring OPD',
                            'icon'  => 'ni ni-tv-2 text-dark',
                            'route' => 'pages.monitoring',
                            'active' => request()->routeIs('pages.monitoring*')
                        ],
                        [
                            'title' => 'Whats Next',
                            'icon'  => 'ni ni-glasses-2 text-dark',
                            'route' => 'pages.whatsnext',
                            'active' => request()->routeIs('pages.whatsnext*')
                        ]
                    ]
                ],
                [
                    'header' => 'Pelaporan Data',
                    'roles'  => ['produsen', 'admin', 'walidata','pembina'],
                    'items'  => [
                        [
                            'title' => 'Metadata',
                            'icon'  => 'ni ni-folder-17 text-dark',
                            'route' => 'pelaporan.metadata.index',
                            'active' => Route::currentRouteName() == 'pelaporan.metadata.index'
                        ],
                        [
                            'title' => 'Romantik',
                            'icon'  => 'img/logo-romantik.svg',
                            'is_url' => 'true',
                            'url' => 'https://romantik.web.bps.go.id/',
                            'active' => false
                        ]
                    ]
                ],
                [
                    'header' => 'Monitoring Data',
                    'roles'  => ['admin', 'walidata', 'produsen','pembina'], 
                    'items'  => [
                        [
                            'title' => 'Metadata',
                            'icon'  => 'ni ni-book-bookmark text-dark',
                            'route' => match(auth()->user()->role) {
                                'admin'    => 'data.metadata.index',
                                'pembina'  => 'metadata.table',
                                'walidata' => 'metadata.table',
                                'produsen' => 'metadata.table',
                                default    => 'metadata.table', 
                            },
                            'active' => request()->routeIs('data.metadata.*') || request()->routeIs('metadata.table')
                        ],
                        [
                            'title' => 'Romantik',
                            'icon'  => 'ni ni-check-bold text-dark',
                            'route' => match(auth()->user()->role) {
                                'admin'    => 'data.romantik.index',
                                'pembina'  => 'romantik.table',
                                'walidata' => 'romantik.table',
                                'produsen' => 'romantik.table',
                                default    => 'romantik.table', 
                            },
                            'active' => request()->routeIs('data.romantik.*') || request()->routeIs('romantik.table')
                        ]
                    ]
                ],
                [
                    'header' => 'Master Data Sektoral',
                    'roles'  => ['produsen', 'admin', 'walidata','pembina'], 
                    'items'  => array_filter([
                        [
                            'title' => 'Kegiatan Statistik',
                            'icon'  => 'ni ni-bullet-list-67 text-warning',
                            'route' => 'master.kegiatan.index',
                            'active' => request()->routeIs('master.kegiatan.*')
                        ],
                        [
                            'title' => 'Daftar Data',
                            'icon'  => 'ni ni-check-bold text-dark',
                            'route' => match(auth()->user()->role) {
                                'admin'    => 'data.daftardata.index',
                                'pembina'  => 'daftardata.table',
                                'walidata' => 'daftardata.table',
                                'produsen' => 'daftardata.table',
                                default    => 'daftardata.table', 
                            },
                            'active' => request()->routeIs('data.daftardata.*') || request()->routeIs('daftardata.table')
                        ],
                    ])
                ],
                [
                    'header' => 'Master Data',
                    'roles'  => ['admin', 'walidata'],
                    'items'  => array_filter([
                        [
                            'title' => 'Master OPD',
                            'icon'  => 'ni ni-building text-dark',
                            'route' => 'master.opd.index',
                            'active' => request()->routeIs('master.opd.*')
                        ],
                        [
                            'title' => 'Manajemen User',
                            'icon'  => 'ni ni-single-02 text-dark',
                            'route' => 'master.users.index',
                            'active' => request()->routeIs('master.users.*')
                        ],
                        auth()->user()->role === 'admin' ? [
                            'title'  => 'Event BPS',
                            'icon'   => 'ni ni-calendar-grid-58 text-danger',
                            'route'  => 'data.event.index',
                            'active' => request()->routeIs('data.event.*')
                        ] : null,
                    ])
                ]
            ];

            $view->with('navigationMenu', $menuItems);
        });
    }
}