<?php

use Livewire\Component;
use App\Models\Jukir;
use App\Models\Lokasi;
use App\Models\Area;
use App\Models\Merchant;
use App\Models\Korlap;
use App\Models\TransTunai;
use App\Models\TransNonTunai;
use App\Models\ParkirBerlangganan;
use Carbon\Carbon;

new class extends Component {
    public $selectedYear;

    public $totalJukir;
    public $jukirActive;
    public $jukirPending;
    public $jukirNonActive;
    public $totalLokasi;
    public $totalArea;
    public $totalMerchant;
    public $totalKorlap;
    public $totalBerlangganan;

    public $totalTransTunai;
    public $sumTransTunai;
    public $totalTransNonTunai;
    public $sumTransNonTunai;

    public $jukirTunai;
    public $jukirNonTunai;

    public $areaStats;

    // Monthly chart data
    public $monthlyTunaiLabels = [];
    public $monthlyTunaiData = [];
    public $monthlyNonTunaiData = [];

    public function mount()
    {
        $this->selectedYear = Carbon::now()->year;
        $this->loadStats();
    }

    public function updatedSelectedYear()
    {
        $this->loadStats();
        
        $this->dispatch('update-charts', 
            labels: $this->monthlyTunaiLabels,
            tunai: $this->monthlyTunaiData,
            nontunai: $this->monthlyNonTunaiData
        );
    }

    public function loadStats()
    {
        // Jukir stats
        $this->totalJukir = Jukir::count();
        $this->jukirActive = Jukir::where('ket_jukir', 'Active')->count();
        $this->jukirPending = Jukir::where('ket_jukir', 'Pending')->count();
        $this->jukirNonActive = Jukir::where('ket_jukir', 'Non Active')->count();

        // Jukir by status
        $this->jukirTunai = Jukir::where('status', 'Tunai')->count();
        $this->jukirNonTunai = Jukir::where('status', 'Non-Tunai')->count();

        // Core data stats
        $this->totalLokasi = Lokasi::count();
        $this->totalArea = Area::count();
        $this->totalMerchant = Merchant::count();
        $this->totalKorlap = Korlap::count();
        $this->totalBerlangganan = ParkirBerlangganan::count();

        // Transaction stats
        $this->totalTransTunai = TransTunai::whereYear('tgl_transaksi', $this->selectedYear)->count();
        $this->sumTransTunai = TransTunai::whereYear('tgl_transaksi', $this->selectedYear)->sum('jumlah_transaksi');
        $this->totalTransNonTunai = TransNonTunai::whereYear('tgl_transaksi', $this->selectedYear)->count();
        $this->sumTransNonTunai = TransNonTunai::whereYear('tgl_transaksi', $this->selectedYear)->sum('total_nilai');

        // Area stats (top 5 areas by jukir count)
        $this->areaStats = Area::withCount(['jukirs', 'lokasis', 'merchants'])
            ->orderBy('jukirs_count', 'desc')
            ->take(5)
            ->get();

        // Monthly chart data (12 months of selectedYear)
        $months = collect();
        for ($i = 1; $i <= 12; $i++) {
            $months->push(Carbon::createFromDate($this->selectedYear, $i, 1));
        }

        $this->monthlyTunaiLabels = $months->map(fn($m) => $m->translatedFormat('M'))->toArray();
        $this->monthlyTunaiData = $months->map(function($m) {
            return TransTunai::whereYear('tgl_transaksi', $this->selectedYear)
                ->whereMonth('tgl_transaksi', $m->month)
                ->sum('jumlah_transaksi');
        })->toArray();

        $this->monthlyNonTunaiData = $months->map(function($m) {
            return TransNonTunai::whereYear('tgl_transaksi', $this->selectedYear)
                ->whereMonth('tgl_transaksi', $m->month)
                ->sum('total_nilai');
        })->toArray();
    }

    public function render()
    {
        return $this->view()->title('Dashboard');
    }
};
?>

<div>
    <!-- [ breadcrumb ] start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Dashboard</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item" aria-current="page">Dashboard</li>
                    </ul>
                </div>
                <div class="col-md-4 text-md-end text-start mt-3 mt-md-0">
                    <div class="d-inline-flex align-items-center bg-white border px-3 py-2 rounded shadow-sm">
                        <i class="ti ti-calendar text-primary me-2 f-18"></i>
                        <select wire:model.live="selectedYear" class="form-select border-0 shadow-none p-0 py-1 pe-4 cursor-pointer bg-transparent fw-bold text-dark w-auto">
                            @php $currentYear = \Carbon\Carbon::now()->year; @endphp
                            @for($i = $currentYear; $i >= $currentYear - 5; $i--)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ breadcrumb ] end -->

    <!-- [ Main Content ] start -->
    <div class="row">

        <!-- ============ Summary Cards Row 1 ============ -->
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="mb-0 f-w-400 text-muted">Total Jukir</h6>
                        <div class="avtar avtar-s rounded-circle text-primary bg-light-primary">
                            <i class="ti ti-user f-18"></i>
                        </div>
                    </div>
                    <h3 class="mb-2 fw-bold">{{ number_format($totalJukir) }}</h3>
                    <div class="d-flex gap-2">
                        <span class="badge bg-light-success border border-success"><i class="ti ti-check me-1"></i>{{ $jukirActive }} Active</span>
                        <span class="badge bg-light-warning border border-warning"><i class="ti ti-clock me-1"></i>{{ $jukirPending }} Pending</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="mb-0 f-w-400 text-muted">Titik Parkir</h6>
                        <div class="avtar avtar-s rounded-circle text-success bg-light-success">
                            <i class="ti ti-map-pin f-18"></i>
                        </div>
                    </div>
                    <h3 class="mb-2 fw-bold">{{ number_format($totalLokasi) }}</h3>
                    <p class="mb-0 text-muted text-sm">Tersebar di <span class="text-success fw-bold">{{ $totalArea }}</span> Kecamatan</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="mb-0 f-w-400 text-muted">Merchant</h6>
                        <div class="avtar avtar-s rounded-circle text-warning bg-light-warning">
                            <i class="ti ti-shopping-cart f-18"></i>
                        </div>
                    </div>
                    <h3 class="mb-2 fw-bold">{{ number_format($totalMerchant) }}</h3>
                    <p class="mb-0 text-muted text-sm">Terdaftar <span class="text-warning fw-bold">{{ $totalKorlap }}</span> Korlap</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="mb-0 f-w-400 text-muted">Berlangganan</h6>
                        <div class="avtar avtar-s rounded-circle text-danger bg-light-danger">
                            <i class="ti ti-ticket f-18"></i>
                        </div>
                    </div>
                    <h3 class="mb-2 fw-bold">{{ number_format($totalBerlangganan) }}</h3>
                    <p class="mb-0 text-muted text-sm">Total kendaraan berlangganan</p>
                </div>
            </div>
        </div>

        <!-- ============ Transaction Summary Cards ============ -->
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h6 class="mb-1 text-muted">Transaksi Tunai</h6>
                            <h4 class="mb-0 fw-bold">Rp {{ number_format($sumTransTunai, 0, ',', '.') }}</h4>
                        </div>
                        <div class="avtar avtar-l rounded-circle text-success bg-light-success">
                            <i class="ti ti-cash f-24"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="text-muted text-sm">{{ number_format($totalTransTunai) }} Transaksi</span>
                        <a href="{{ route('transaksi.tunai.index') }}" class="text-primary text-sm fw-bold">Lihat Semua <i class="ti ti-chevron-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h6 class="mb-1 text-muted">Transaksi Non-Tunai</h6>
                            <h4 class="mb-0 fw-bold">Rp {{ number_format($sumTransNonTunai, 0, ',', '.') }}</h4>
                        </div>
                        <div class="avtar avtar-l rounded-circle text-primary bg-light-primary">
                            <i class="ti ti-wallet f-24"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="text-muted text-sm">{{ number_format($totalTransNonTunai) }} Transaksi</span>
                        <a href="{{ route('transaksi.non-tunai.index') }}" class="text-primary text-sm fw-bold">Lihat Semua <i class="ti ti-chevron-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h6 class="mb-1 text-muted">Total Pendapatan</h6>
                            <h4 class="mb-0 fw-bold">Rp {{ number_format($sumTransTunai + $sumTransNonTunai, 0, ',', '.') }}</h4>
                        </div>
                        <div class="avtar avtar-l rounded-circle text-warning bg-light-warning">
                            <i class="ti ti-chart-bar f-24"></i>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <span class="badge bg-light-success border border-success">
                            <i class="ti ti-cash me-1"></i>Tunai {{ $totalTransTunai }}
                        </span>
                        <span class="badge bg-light-primary border border-primary">
                            <i class="ti ti-wallet me-1"></i>Non-Tunai {{ $totalTransNonTunai }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============ Chart: Monthly Transactions ============ -->
        <div class="col-md-12 col-xl-8">
            <h5 class="mb-3">Grafik Pendapatan (Tahun {{ $selectedYear }})</h5>
            <div class="card border-0 shadow-sm" wire:ignore>
                <div class="card-body">
                    <div id="monthly-transaction-chart"></div>
                </div>
            </div>
        </div>

        <!-- ============ Jukir Distribution ============ -->
        <div class="col-md-12 col-xl-4">
            <h5 class="mb-3">Distribusi Jukir</h5>
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div id="jukir-distribution-chart"></div>
                    <div class="mt-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-success rounded-circle p-1" style="width: 10px; height: 10px;"></span>
                                <span class="text-muted">Active</span>
                            </div>
                            <span class="fw-bold">{{ $jukirActive }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-warning rounded-circle p-1" style="width: 10px; height: 10px;"></span>
                                <span class="text-muted">Pending</span>
                            </div>
                            <span class="fw-bold">{{ $jukirPending }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-danger rounded-circle p-1" style="width: 10px; height: 10px;"></span>
                                <span class="text-muted">Non Active</span>
                            </div>
                            <span class="fw-bold">{{ $jukirNonActive }}</span>
                        </div>
                        <hr>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-info rounded-circle p-1" style="width: 10px; height: 10px;"></span>
                                <span class="text-muted">Tunai</span>
                            </div>
                            <span class="fw-bold">{{ $jukirTunai }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary rounded-circle p-1" style="width: 10px; height: 10px;"></span>
                                <span class="text-muted">Non-Tunai</span>
                            </div>
                            <span class="fw-bold">{{ $jukirNonTunai }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============ Area Statistics ============ -->
        <div class="col-md-12 col-xl-6">
            <h5 class="mb-3">Statistik per Area (Top 5)</h5>
            <div class="card border-0 shadow-sm tbl-card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless mb-0 align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Kecamatan</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted text-center">Jukir</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted text-center">Lokasi</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted text-center">Merchant</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($areaStats as $area)
                                <tr>
                                    <td class="ps-4 fw-bold"><i class="ti ti-map-pin text-primary me-1"></i> {{ $area->Kecamatan }}</td>
                                    <td class="text-center"><span class="badge bg-light-primary">{{ $area->jukirs_count }}</span></td>
                                    <td class="text-center"><span class="badge bg-light-success">{{ $area->lokasis_count }}</span></td>
                                    <td class="text-center"><span class="badge bg-light-warning">{{ $area->merchants_count }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============ Quick Links ============ -->
        <div class="col-md-12 col-xl-6">
            <h5 class="mb-3">Menu Cepat</h5>
            <div class="row g-3">
                <div class="col-6">
                    <a href="{{ route('jukir.index') }}" class="card border-0 shadow-sm text-decoration-none h-100">
                        <div class="card-body text-center py-4">
                            <div class="avtar avtar-l rounded-circle text-primary bg-light-primary mx-auto mb-3">
                                <i class="ti ti-user f-24"></i>
                            </div>
                            <h6 class="mb-1">Juru Parkir</h6>
                            <small class="text-muted">{{ number_format($totalJukir) }} data</small>
                        </div>
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('lokasi.index') }}" class="card border-0 shadow-sm text-decoration-none h-100">
                        <div class="card-body text-center py-4">
                            <div class="avtar avtar-l rounded-circle text-success bg-light-success mx-auto mb-3">
                                <i class="ti ti-map-pin f-24"></i>
                            </div>
                            <h6 class="mb-1">Titik Parkir</h6>
                            <small class="text-muted">{{ number_format($totalLokasi) }} lokasi</small>
                        </div>
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('merchant.index') }}" class="card border-0 shadow-sm text-decoration-none h-100">
                        <div class="card-body text-center py-4">
                            <div class="avtar avtar-l rounded-circle text-warning bg-light-warning mx-auto mb-3">
                                <i class="ti ti-shopping-cart f-24"></i>
                            </div>
                            <h6 class="mb-1">Merchant</h6>
                            <small class="text-muted">{{ number_format($totalMerchant) }} merchant</small>
                        </div>
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('berlangganan.index') }}" class="card border-0 shadow-sm text-decoration-none h-100">
                        <div class="card-body text-center py-4">
                            <div class="avtar avtar-l rounded-circle text-danger bg-light-danger mx-auto mb-3">
                                <i class="ti ti-ticket f-24"></i>
                            </div>
                            <h6 class="mb-1">Berlangganan</h6>
                            <small class="text-muted">{{ number_format($totalBerlangganan) }} kendaraan</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>



    </div>
    <!-- [ Main Content ] end -->

    <!-- [Page Specific JS] start -->
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
    <script>
        document.addEventListener('livewire:navigated', initDashboardCharts);
        document.addEventListener('DOMContentLoaded', initDashboardCharts);

        let monthlyChart;

        function initDashboardCharts() {
            // ========== Monthly Transaction Chart ==========
            var monthlyEl = document.querySelector("#monthly-transaction-chart");
            if (monthlyEl && !monthlyEl.classList.contains('rendered')) {
                monthlyEl.classList.add('rendered');
                var monthlyOptions = {
                    series: [{
                        name: 'Tunai',
                        data: @json($monthlyTunaiData)
                    }, {
                        name: 'Non-Tunai',
                        data: @json($monthlyNonTunaiData)
                    }],
                    chart: {
                        type: 'bar',
                        height: 360,
                        toolbar: { show: false },
                        fontFamily: 'Public Sans, sans-serif'
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '55%',
                            borderRadius: 6,
                        },
                    },
                    dataLabels: { enabled: false },
                    stroke: { show: true, width: 2, colors: ['transparent'] },
                    xaxis: {
                        categories: @json($monthlyTunaiLabels),
                        labels: {
                            style: { colors: '#8c8c8c', fontSize: '12px' }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: { colors: '#8c8c8c' },
                            formatter: function(val) {
                                if (val >= 1000000) return 'Rp ' + (val / 1000000).toFixed(1) + 'jt';
                                if (val >= 1000) return 'Rp ' + (val / 1000).toFixed(0) + 'rb';
                                return 'Rp ' + val;
                            }
                        }
                    },
                    fill: { opacity: 1 },
                    colors: ['#2ca87f', '#4680ff'],
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return 'Rp ' + val.toLocaleString('id-ID');
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                        fontWeight: 600,
                        markers: { radius: 12, width: 10, height: 10 }
                    },
                    grid: {
                        borderColor: '#f1f1f1',
                        strokeDashArray: 4,
                    }
                };

                monthlyChart = new ApexCharts(monthlyEl, monthlyOptions);
                monthlyChart.render();
            }

            // ========== Jukir Distribution Donut Chart ==========
            var jukirEl = document.querySelector("#jukir-distribution-chart");
            if (jukirEl && !jukirEl.classList.contains('rendered')) {
                jukirEl.classList.add('rendered');
                var jukirOptions = {
                    series: [{{ $jukirActive }}, {{ $jukirPending }}, {{ $jukirNonActive }}],
                    chart: {
                        type: 'donut',
                        height: 260,
                        fontFamily: 'Public Sans, sans-serif'
                    },
                    labels: ['Active', 'Pending', 'Non Active'],
                    colors: ['#2ca87f', '#e58a00', '#dc2626'],
                    legend: { show: false },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Total',
                                        fontSize: '14px',
                                        fontWeight: 600,
                                        formatter: function(w) {
                                            return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                        }
                                    }
                                }
                            }
                        }
                    },
                    dataLabels: { enabled: false },
                    stroke: { width: 2 },
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: { height: 200 }
                        }
                    }]
                };

                var jukirChart = new ApexCharts(jukirEl, jukirOptions);
                jukirChart.render();
            }
        }

        document.addEventListener('livewire:initialized', () => {
            Livewire.on('update-charts', ({ labels, tunai, nontunai }) => {
                if(monthlyChart) {
                    monthlyChart.updateSeries([
                        { name: 'Tunai', data: tunai },
                        { name: 'Non-Tunai', data: nontunai }
                    ]);
                    monthlyChart.updateOptions({
                        xaxis: { categories: labels }
                    });
                }
            });
        });
    </script>
    <!-- [Page Specific JS] end -->
</div>