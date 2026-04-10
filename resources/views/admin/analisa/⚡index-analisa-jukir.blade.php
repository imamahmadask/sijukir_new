<?php

use Livewire\Component;
use App\Models\SummaryJukirMonth;
use App\Models\Korlap;

new class extends Component
{
    public $periode;
    public $korlapId = '';
    public $search = '';
    public $hijau = 0;
    public $kuning = 0;
    public $merah = 0;
    public $total = 0;
    public $ach_hijau = 0;
    public $ach_kuning = 0;
    public $ach_merah = 0;

    public function mount()
    {
        $this->periode = date('Y-m');
        $this->korlapId = Korlap::orderBy('nama', 'asc')->first()->id ?? '';
    }

    public function render()
    {
        $year = (int)date('Y', strtotime($this->periode));
        $month = (int)date('m', strtotime($this->periode));

        $query = SummaryJukirMonth::with(['jukir.merchant', 'jukir.lokasi.korlap'])
            ->where('bulan', $month)
            ->where('tahun', $year)
            ->whereHas('jukir', function($q) {
                $q->where('ket_jukir', 'Active')
                  ->where('status', 'Non-Tunai');
            })
            ->when($this->korlapId, function($q) {
                $q->whereHas('jukir.lokasi', function($q2) {
                    $q2->where('korlap_id', $this->korlapId);
                });
            })
            ->when($this->search, function($q) {
                $q->whereHas('jukir', function($q2) {
                    $q2->where('nama_jukir', 'like', '%' . $this->search . '%')
                       ->orWhereHas('lokasi', function($q3) {
                           $q3->where('titik_parkir', 'like', '%' . $this->search . '%')
                              ->orWhere('lokasi_parkir', 'like', '%' . $this->search . '%')
                              ->orWhereHas('korlap', function($q4) {
                                  $q4->where('nama', 'like', '%' . $this->search . '%');
                              });
                       });
                });
            });

        $summaries = $query->get();
        $korlaps = Korlap::orderBy('nama', 'asc')->get();

        // Menghitung jumlah jukir berdasarkan kondisi kurang_setor
        $this->hijau = $summaries->filter(function ($item) {
            return ($item->non_tunai + $item->kompensasi) >= $item->potensi && $item->potensi > 0;
        })->count();

        $this->kuning = $summaries->filter(function ($item) {
            $totalSetor = $item->non_tunai + $item->kompensasi;
            return $totalSetor < $item->potensi && $totalSetor > 0;
        })->count();

        $this->merah = $summaries->filter(function ($item) {
            $totalSetor = $item->non_tunai + $item->kompensasi;
            return $totalSetor <= 0;
        })->count();

        $this->total = $this->merah + $this->kuning + $this->hijau;

        if($this->total > 0){
            $this->ach_hijau = number_format(($this->hijau / $this->total) * 100, 2);
            $this->ach_kuning = number_format(($this->kuning / $this->total) * 100, 2);
            $this->ach_merah = number_format(($this->merah / $this->total) * 100, 2);
        }else{
            $this->ach_hijau = number_format(0, 2);
            $this->ach_kuning = number_format(0, 2);
            $this->ach_merah = number_format(0, 2);
        }
        
        return $this->view()->title('Analisa Jukir Per Korlap')->with([
            'summaries' => $summaries,
            'korlaps' => $korlaps
        ]);
    }
};
?>

<div>
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Analisa Jukir per Korlap</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Analisa</a></li>
                        <li class="breadcrumb-item" aria-current="page">Jukir</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-success mb-1">Hijau</h6>
                    <h3 class="mb-0">{{ number_format($hijau, 0, ',', '.') }}</h3>
                    <small class="text-muted">{{ $ach_hijau }}% dari total</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-warning mb-1">Kuning</h6>
                    <h3 class="mb-0">{{ number_format($kuning, 0, ',', '.') }}</h3>
                    <small class="text-muted">{{ $ach_kuning }}% dari total</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-danger mb-1">Merah</h6>
                    <h3 class="mb-0">{{ number_format($merah, 0, ',', '.') }}</h3>
                    <small class="text-muted">{{ $ach_merah }}% dari total</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0 bg-primary text-white">
                <div class="card-body">
                    <h6 class="text-white-50 mb-1">Total Jukir</h6>
                    <h3 class="mb-0 text-white">{{ number_format($total, 0, ',', '.') }}</h3>
                    <small class="text-white-50">Keseluruhan</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">        
        <div class="col-12">
            <div class="card tbl-card">
                <div class="card-header bg-transparent border-0 px-4 pb-3 pt-3">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label text-muted small mb-1">Periode (Bulan - Tahun)</label>
                            <input type="month" class="form-control" wire:model.live="periode">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small mb-1">Korlap</label>
                            <select class="form-select" wire:model.live="korlapId">
                                @foreach($korlaps as $korlap)
                                    <option value="{{ $korlap->id }}">{{ $korlap->nama }}</option>
                                @endforeach
                            </select>
                        </div>                        
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Cari</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="ti ti-search text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari Jukir atau Lokasi..." wire:model.live="search">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive scroll-y" style="max-height: 500px;">
                        <table class="table table-hover table-borderless mb-0 align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">#</th>
                                    <th>Nama Jukir</th>                                    
                                    <th>Titik & Lokasi</th>
                                    <th>Korlap</th>
                                    <th class="text-end">Potensi Harian</th>
                                    <th class="text-end">Potensi Bulanan</th>
                                    <th class="text-end">Setoran</th>
                                    <th class="text-end">Kompensasi</th>
                                    <th class="text-end pe-4">Kurang Setor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($summaries as $index => $item)
                                    <tr wire:key="summary-{{ $item->id }}">
                                        <td class="ps-4">{{ $index + 1 }}</td>
                                        <td>
                                            <span class="fw-bold d-block text-truncate" style="max-width: 150px;" title="{{ $item->jukir->nama_jukir ?? '-' }}">
                                                <a href="{{ route('jukir.detail', $item->jukir->id) }}">{{ $item->jukir->nama_jukir ?? '-' }}</a>
                                            </span>
                                            <small class="text-muted">{{ $item->jukir->merchant->merchant_name ?? '-' }}</small>
                                        </td>                                       
                                        <td>
                                            <span class="d-block text-truncate" style="max-width: 200px;" title="{{ $item->jukir->lokasi->titik_parkir ?? '-' }}">
                                                {{ $item->jukir->lokasi->titik_parkir ?? '-' }}
                                            </span>
                                            <small class="text-muted fst-italic d-block text-truncate" style="max-width: 200px;" title="{{ $item->jukir->lokasi->lokasi_parkir ?? '-' }}">
                                                {{ $item->jukir->lokasi->lokasi_parkir ?? '-' }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="d-block text-truncate" style="max-width: 150px;" title="{{ $item->jukir->lokasi->korlap->nama ?? '-' }}">
                                                {{ $item->jukir->lokasi->korlap->nama ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            Rp. {{ number_format($item->jukir->potensi_harian ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end">
                                            Rp. {{ number_format($item->potensi ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end">
                                            Rp. {{ number_format($item->non_tunai ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end">
                                            Rp. {{ number_format($item->kompensasi ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end fs-5 pe-4">
                                            @php
                                                $colorClass = 'bg-light-success text-success'; // default green for >= 0
                                                if (($item->kurang_setor + $item->potensi) == 0) {
                                                    $colorClass = 'bg-light-danger text-danger';
                                                } elseif ($item->kurang_setor < 0) {
                                                    $colorClass = 'bg-light-warning text-warning';
                                                }
                                            @endphp
                                            <span class="badge {{ $colorClass }}">
                                                Rp. {{ number_format($item->kurang_setor ?? 0, 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5 text-muted">
                                            <i class="ti ti-database-off fs-1 d-block mb-2"></i>
                                            Belum ada data untuk periode ini
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>