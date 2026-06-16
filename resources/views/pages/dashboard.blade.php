@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Dashboard'])
    <div class="container-fluid py-4">
        <h1 class="font-weight-bolder">Kegiatan Statistik Sektoral</h1>
        <div class="row">
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-lg mb-0 text-uppercase font-weight-bold">Jumlah OPD</p>
                                    <h2 class="font-weight-bolder">
                                        {{ $stats['total_opd'] ?? 0 }} 
                                    </h2>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                    <i class="ni ni-building text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-lg mb-0 text-uppercase font-weight-bold">Kegiatan Statistik</p>
                                    <h2 class="font-weight-bolder">
                                        {{ $stats['total_kegiatan'] ?? 0 }}
                                    </h2>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-danger shadow-danger text-center rounded-circle">
                                    <i class="ni ni-books text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-lg mb-0 text-uppercase font-weight-bold">Pengajuan Romantik</p>
                                    <h2 class="font-weight-bolder">
                                        {{ $stats['total_romantik'] ?? 0 }}
                                    </h2>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                    <i class="ni ni-check-bold text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-lg mb-0 text-uppercase font-weight-bold">Jumlah Metadata</p>
                                    <h2 class="font-weight-bolder">
                                        {{ $stats['total_metadata'] ?? 0 }}
                                    </h2>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                    <i class="ni ni-folder-17 text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-lg-6 mb-lg-0 mb-4">
                <div class="card z-index-2 h-100">
                    <div class="card-header pb-0 pt-3 bg-transparent">
                        <h6 class="text-capitalize">Persentase Kegiatan Statistik Mempunyai Romantik</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="chart">
                            <canvas id="chart-kegiatan-romantik" class="chart-canvas" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-lg-0 mb-4">
                <div class="card z-index-2 h-100">
                    <div class="card-header pb-0 pt-3 bg-transparent">
                        <h6 class="text-capitalize">Persentase Kegiatan Statistik Mempunyai Metadata</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="chart">
                            <canvas id="chart-kegiatan-metadata" class="chart-canvas" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-lg-4 mb-lg-0 mb-4">
                <div class="card z-index-2 h-100">
                    <div class="card-header pb-0 pt-3 bg-transparent">
                        <h6 class="text-capitalize">Persentase Daftar Data Eligible</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="chart">
                            <canvas id="chart-daftardata-eligible" class="chart-canvas" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-lg-0 mb-4">
                <div class="card z-index-2 h-100">
                    <div class="card-header pb-0 pt-3 bg-transparent">
                        <h6 class="text-capitalize">Persentase Daftar Data Mempunyai Romantik</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="chart">
                            <canvas id="chart-daftardata-romantik" class="chart-canvas" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-lg-0 mb-4">
                <div class="card z-index-2 h-100">
                    <div class="card-header pb-0 pt-3 bg-transparent">
                        <h6 class="text-capitalize">Persentase Daftar Data Mempunyai Metadata</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="chart">
                            <canvas id="chart-daftardata-metadata" class="chart-canvas" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        @include('layouts.footers.auth.footer')
    </div>
@endsection

@push('js')
    <script src="./assets/js/plugins/chartjs.min.js"></script>
    <script>
        // Plugin kustom untuk menampilkan persentase di tengah masing-masing slice doughnut
        const doughnutLabelPlugin = {
            id: 'doughnutLabel',
            afterDraw(chart, args, options) {
                const { ctx } = chart;
                chart.data.datasets.forEach((dataset, i) => {
                    chart.getDatasetMeta(i).data.forEach((datapoint, index) => {
                        let total = dataset.data.reduce((a, b) => a + b, 0);
                        let value = dataset.data[index];
                        if (value === 0) return; // Jangan tampilkan teks jika nilainya 0
                        
                        let percentage = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '0%';
                        percentage = percentage.replace('.0%', '%'); // Hapus .0 jika bilangannya bulat

                        const position = datapoint.tooltipPosition();
                        
                        ctx.save();
                        ctx.font = 'bold 12px "Open Sans"';
                        ctx.fillStyle = '#ffffff';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        // Menambahkan shadow agar teks putih tetap terbaca di warna cerah
                        ctx.shadowColor = 'rgba(0, 0, 0, 0.5)';
                        ctx.shadowBlur = 4;
                        ctx.fillText(percentage, position.x, position.y);
                        ctx.restore();
                    });
                });
            }
        };

        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        font: {
                            size: 11,
                            family: "Open Sans",
                            style: 'normal',
                            lineHeight: 2
                        },
                    }
                },
                // Menambahkan informasi persentase pada tooltip saat chart di-hover
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let value = context.parsed || context.raw;
                            let percentage = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '0%';
                            percentage = percentage.replace('.0%', '%');
                            label += value + ' (' + percentage + ')';
                            return label;
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
        };

        const createDoughnutChart = (ctxId, labels, data, colors) => {
            const ctx = document.getElementById(ctxId).getContext("2d");
            new Chart(ctx, {
                type: "doughnut",
                data: {
                    labels: labels,
                    datasets: [{
                        backgroundColor: colors,
                        data: data,
                    }],
                },
                options: chartOptions,
                plugins: [doughnutLabelPlugin] // Daftarkan plugin kustom di sini
            });
        };

        createDoughnutChart("chart-kegiatan-romantik", ["Mempunyai Romantik", "Belum/Tidak Mempunyai"], @json($charts['kegiatan_romantik'] ?? [0, 0]), ['#2dce89', '#f5365c']);
        createDoughnutChart("chart-kegiatan-metadata", ["Mempunyai Metadata", "Belum/Tidak Mempunyai"], @json($charts['kegiatan_metadata'] ?? [0, 0]), ['#11cdef', '#f5365c']);
        createDoughnutChart("chart-daftardata-eligible", ["Eligible", "Tidak Eligible"], @json($charts['daftardata_eligible'] ?? [0, 0]), ['#5e72e4', '#f5365c']);
        createDoughnutChart("chart-daftardata-romantik", ["Mempunyai Romantik", "Belum/Tidak Mempunyai"], @json($charts['daftardata_romantik'] ?? [0, 0]), ['#2dce89', '#f5365c']);
        createDoughnutChart("chart-daftardata-metadata", ["Mempunyai Metadata", "Belum/Tidak Mempunyai"], @json($charts['daftardata_metadata'] ?? [0, 0]), ['#11cdef', '#f5365c']);
    </script>
@endpush
