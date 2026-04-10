<?php

use Livewire\Component;
use App\Models\SummaryKorlap;
use App\Models\Korlap;
use App\Models\Area;

new class extends Component
{
    public $periode;
    public $areaId = '';
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
    }

    public function render()
    {
        $year = (int)date('Y', strtotime($this->periode));
        $month = (int)date('m', strtotime($this->periode));

        $areas = Area::orderBy('Kecamatan', 'asc')->get();

        $korlapStatsQuery = SummaryKorlap::with(['korlap.lokasis.area'])
            ->where('bulan', $month)
            ->where('tahun', $year)
            ->when($this->areaId, function ($q) {
                $q->whereHas('korlap.lokasis', function($q2) {
                    $q2->where('area_id', $this->areaId);
                });
            })
            ->when($this->search, function ($q) {
                $q->whereHas('korlap', function($q2) {
                    $q2->where('nama', 'like', '%' . $this->search . '%');
                });
            });

        $korlapStatsList = $korlapStatsQuery->get();

        $this->hijau = $korlapStatsList->sum('hijau');
        $this->kuning = $korlapStatsList->sum('kuning');
        $this->merah = $korlapStatsList->sum('merah');
        $this->total = $korlapStatsList->sum('jml_jukir');

        if($this->total > 0){
            $this->ach_hijau = number_format(($this->hijau / $this->total) * 100, 2);
            $this->ach_kuning = number_format(($this->kuning / $this->total) * 100, 2);
            $this->ach_merah = number_format(($this->merah / $this->total) * 100, 2);
        }else{
            $this->ach_hijau = number_format(0, 2);
            $this->ach_kuning = number_format(0, 2);
            $this->ach_merah = number_format(0, 2);
        }

        // Mengurutkan secara sekuensial: Persentase Hijau > Kuning > Merah
        $korlapStatsList = $korlapStatsList->sort(function ($a, $b) {
            // 1. Hijau persentase terbesar
            if ($a->ach_hijau != $b->ach_hijau) {
                return $b->ach_hijau <=> $a->ach_hijau;
            }
            // 2. Jika Hijau sama, Kuning persentase terbesar
            if ($a->ach_kuning != $b->ach_kuning) {
                return $b->ach_kuning <=> $a->ach_kuning;
            }
            // 3. Jika Kuning juga sama, Merah persentase terkecil
            if ($a->ach_merah != $b->ach_merah) {
                return $a->ach_merah <=> $b->ach_merah;
            }
            // 4. Jika semua persentase sama persis, urutkan berdasarkan jumlah jukir terbanyak
            return $b->jml_jukir <=> $a->jml_jukir;
        })->values();

        return $this->view()->title('Analisa Bulanan Korlap')->with([
            'korlapStatsList' => $korlapStatsList,
            'areas' => $areas
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
                        <h5 class="m-b-10">Analisa Bulanan Korlap</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Analisa</a></li>
                        <li class="breadcrumb-item" aria-current="page">Korlap</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-3">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-success mb-1">Hijau</h6>
                    <h3 class="mb-0">{{ number_format($hijau, 0, ',', '.') }}</h3>
                    <small class="text-muted">{{ $ach_hijau }}% dari total jukir</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-warning mb-1">Kuning</h6>
                    <h3 class="mb-0">{{ number_format($kuning, 0, ',', '.') }}</h3>
                    <small class="text-muted">{{ $ach_kuning }}% dari total jukir</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-danger mb-1">Merah</h6>
                    <h3 class="mb-0">{{ number_format($merah, 0, ',', '.') }}</h3>
                    <small class="text-muted">{{ $ach_merah }}% dari total jukir</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0 bg-primary text-white">
                <div class="card-body">
                    <h6 class="text-white-50 mb-1">Total Jukir</h6>
                    <h3 class="mb-0 text-white">{{ number_format($total, 0, ',', '.') }}</h3>
                    <small class="text-white-50">Keseluruhan Aktif Non-Tunai</small>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card tbl-card">
                <div class="card-header bg-transparent border-0 px-4 pb-3 pt-3">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label text-muted small mb-1">Periode (Bulan - Tahun)</label>
                            <input type="month" class="form-control" wire:model.live="periode">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small mb-1">Area / Kecamatan</label>
                            <select class="form-select" wire:model.live="areaId">
                                <option value="">Semua Kecamatan</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}">{{ $area->Kecamatan }}</option>
                                @endforeach
                            </select>
                        </div>                        
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Cari</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="ti ti-search text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari Nama Korlap..." wire:model.live="search">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless mb-0 align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">No</th>
                                    <th>Nama Korlap</th>
                                    <th>Area</th>
                                    <th class="text-center">Total Jukir</th>
                                    <th class="text-center">Hijau</th>
                                    <th class="text-center">Kuning</th>
                                    <th class="text-center">Merah</th>
                                    <th>Pencapaian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($korlapStatsList as $index => $item)
                                    <tr wire:key="korlap-{{ $item->korlap->id }}">
                                        <td class="ps-4">{{ $index + 1 }}</td>
                                        <td>
                                            <span class="fw-bold d-block text-truncate" style="max-width: 200px;" title="{{ $item->korlap->nama ?? '-' }}">
                                                {{ $item->korlap->nama ?? '-' }}
                                            </span>
                                            <small class="text-muted fst-italic">{{ $item->korlap->status ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <span class="fw-bold d-block text-truncate" style="max-width: 200px;" title="{{ $item->korlap->lokasis->first()->area->Kecamatan ?? '-' }}">
                                                {{ $item->korlap->lokasis->first()->area->Kecamatan ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light-primary text-primary fs-6">
                                                {{ $item->jml_jukir }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="d-block text-success fw-bold badge bg-light-success fs-6">{{ $item->hijau }}</span>
                                            <small class="text-muted" style="font-size: 0.7em">{{ $item->ach_hijau }}%</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="d-block text-warning fw-bold badge bg-light-warning fs-6">{{ $item->kuning }}</span>
                                            <small class="text-muted" style="font-size: 0.7em">{{ $item->ach_kuning }}%</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="d-block text-danger fw-bold badge bg-light-danger fs-6">{{ $item->merah }}</span>
                                            <small class="text-muted" style="font-size: 0.7em">{{ $item->ach_merah }}%</small>
                                        </td>
                                        <td style="width: 200px">
                                            @if($item->jml_jukir > 0)
                                                <div class="progress" style="height: 10px;">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $item->ach_hijau }}%" aria-valuenow="{{ $item->ach_hijau }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $item->ach_kuning }}%" aria-valuenow="{{ $item->ach_kuning }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                    <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $item->ach_merah }}%" aria-valuenow="{{ $item->ach_merah }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            @else
                                                <span class="text-muted fst-italic small">Tidak ada data</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="ti ti-database-off fs-1 d-block mb-2"></i>
                                            Belum ada data Korlap atau Jukir
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