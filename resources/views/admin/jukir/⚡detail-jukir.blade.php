<?php

use Livewire\Component;
use App\Models\Jukir;
use App\Models\TransNonTunai;
use App\Models\HistoriJukir;
use App\Models\PembantuJukir;
use Livewire\WithPagination;
use Livewire\Attributes\On;

new class extends Component {
    use WithPagination;

    public $jukir;
    public $selectedYear;
    public $startDate;
    public $endDate;
    protected $paginationTheme = 'bootstrap';

    public function mount($id)
    {
        $this->jukir = Jukir::with('lokasi')->findOrFail($id);
        $this->selectedYear = date('Y');
        $this->endDate = date('Y-m-d');
        $this->startDate = date('Y-m-d', strtotime('-14 days'));
    }

    #[On('refresh-detail-jukir')]
    public function render()
    {
        $transactions = collect();
        if ($this->jukir->merchant_id) {
            $transactions = TransNonTunai::where('merchant_id', $this->jukir->merchant_id)
                ->whereBetween('tgl_transaksi', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
                ->orderBy('tgl_transaksi', 'desc')
                ->paginate(5);
        } else {
            $transactions = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 5);
        }

        $historiList = HistoriJukir::where('jukir_id', $this->jukir->id)
            ->orderBy('created_at', 'desc')
            ->orderBy('tgl_histori', 'desc')
            ->get();

        $pembantuList = PembantuJukir::where('jukir_id', $this->jukir->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $summaryMonths = \App\Models\SummaryJukirMonth::where('jukir_id', $this->jukir->id)
            ->where('tahun', $this->selectedYear)
            ->orderBy('bulan', 'desc')
            ->get();

        $summaryYears = \App\Models\SummaryJukirMonth::where('jukir_id', $this->jukir->id)
            ->selectRaw('tahun, sum(jml_trx) as total_trx, sum(non_tunai) as total_non_tunai, sum(potensi) as total_potensi, sum(kompensasi) as total_kompensasi, sum(kurang_setor) as total_kurang_setor')
            ->groupBy('tahun')
            ->orderBy('tahun', 'asc')
            ->get();

        $availableYears = \App\Models\SummaryJukirMonth::where('jukir_id', $this->jukir->id)
            ->select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        if ($availableYears->isEmpty()) {
            $availableYears = collect([date('Y')]);
        } elseif (!$availableYears->contains(date('Y'))) {
            $availableYears->push(date('Y'));
            $availableYears = $availableYears->sortDesc()->values();
        }

        return $this->view()->title('Detail Jukir')->with([
            'transactions'  => $transactions,
            'historiList'   => $historiList,
            'pembantuList'  => $pembantuList,
            'summaryMonths' => $summaryMonths,
            'summaryYears'  => $summaryYears,
            'availableYears'=> $availableYears,
        ]);
    }

    public function openCreateHistori()
    {
        $this->dispatch('open-create-histori', jukir_id: $this->jukir->id)->to('admin::jukir.histori.histori_jukir');
    }

    public function openEditHistori($id)
    {
        $this->dispatch('open-edit-histori', id: $id)->to('admin::jukir.histori.histori_jukir');
    }

    public function confirmDeleteHistori($id)
    {
        $this->dispatch('confirm-delete-histori', id: $id)->to('admin::jukir.histori.histori_jukir');
    }

    public function openCreatePembantu()
    {
        $this->dispatch('open-create-pembantu', jukir_id: $this->jukir->id)->to('admin::jukir.pembantu.pembantu_jukir');
    }

    public function openEditPembantu($id)
    {
        $this->dispatch('open-edit-pembantu', id: $id)->to('admin::jukir.pembantu.pembantu_jukir');
    }

    public function confirmDeletePembantu($id)
    {
        $this->dispatch('confirm-delete-pembantu', id: $id)->to('admin::jukir.pembantu.pembantu_jukir');
    }
};
?>

<div>
    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Jukir</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('jukir.index') }}">Jukir</a></li>
                        <li class="breadcrumb-item" aria-current="page">Detail</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <style>
        .dj-label {
            font-size: 0.72rem;
            color: #9ca3af;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            display: block;
            margin-bottom: 2px;
        }
        .dj-value {
            font-size: 0.9rem;
            color: #1f2937;
            font-weight: 500;
        }
        .dj-section-title {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #6b7280;
            padding-bottom: 10px;
            border-bottom: 1px solid #f3f4f6;
            margin-bottom: 16px;
        }
        .dj-card {
            background: #fff;
            border: 1px solid #f1f3f5;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            margin-bottom: 16px;
        }
        .dj-card-body {
            padding: 20px 24px;
        }
        .timeline-scroll {
            max-height: 360px;
            overflow-y: auto;
            padding-right: 6px;
        }
        .timeline-scroll::-webkit-scrollbar { width: 3px; }
        .timeline-scroll::-webkit-scrollbar-track { background: transparent; }
        .timeline-scroll::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
        .timeline-scroll::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
        .dj-info-item { padding: 10px 0; border-bottom: 1px solid #f9fafb; }
        .dj-info-item:last-child { border-bottom: none; padding-bottom: 0; }
        .dj-avatar {
            width: 72px; height: 72px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #f3f4f6;
        }
        .dj-stat-box {
            background: #f9fafb;
            border-radius: 10px;
            padding: 14px 16px;
        }
        .dj-stat-label { font-size: 0.72rem; color: #9ca3af; font-weight: 500; }
        .dj-stat-value { font-size: 1.05rem; font-weight: 700; color: #111827; margin-top: 2px; }
        .dj-stat-value.primary { color: #4f46e5; }
        .dj-back-btn {
            font-size: 0.82rem;
            color: #6b7280;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 0;
            transition: color 0.15s;
        }
        .dj-back-btn:hover { color: #111827; }
        .table > :not(caption) > * > * { padding: 11px 14px; }
        .dj-tbl thead th {
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #9ca3af;
            background: #f9fafb;
            border: none;
            padding: 10px 14px;
        }
        .dj-tbl tbody td {
            font-size: 0.875rem;
            color: #374151;
            border-color: #f3f4f6;
            vertical-align: middle;
        }
        .dj-tbl tfoot th {
            font-size: 0.82rem;
            background: #f3f4f6;
            border: none;
            color: #1f2937;
        }
        .dj-tbl tr:last-child td { border-bottom: none; }
        .dj-empty {
            padding: 40px 0;
            text-align: center;
            color: #9ca3af;
        }
        .dj-empty i { font-size: 2rem; display: block; margin-bottom: 8px; opacity: 0.4; }
        .dj-empty span { font-size: 0.82rem; }
    </style>

    {{-- ===== TOP ROW: Profile + Info Personal ===== --}}
    <div class="row g-3 mb-2">

        {{-- Profile Card --}}
        <div class="col-12 col-md-4 col-xl-3">
            <div class="dj-card h-100">
                <div class="dj-card-body">
                    {{-- Back + Edit Actions --}}
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <a href="{{ route('jukir.index') }}" class="dj-back-btn">
                            <i class="ti ti-arrow-left"></i> Kembali
                        </a>
                        @can('manageAdmin')
                        <a href="{{ route('jukir.edit', $jukir->id) }}" class="btn btn-sm btn-outline-primary" style="font-size:0.78rem; padding: 4px 12px;">
                            <i class="ti ti-edit me-1"></i> Edit
                        </a>
                        @endcan
                    </div>

                    {{-- Avatar + Name --}}
                    <div class="text-center mb-4">
                        <img src="{{ $jukir->foto ? asset('storage/' . $jukir->foto) : 'https://placehold.co/128x128?text=Jukir' }}"
                             onerror="this.onerror=null; this.src='https://placehold.co/128x128?text=Jukir';"
                             alt="{{ $jukir->nama_jukir }}" class="dj-avatar mb-3 rounded-circle d-block mx-auto">
                        <div class="fw-bold text-dark" style="font-size:1rem;">{{ $jukir->nama_jukir }}</div>
                        <div class="text-muted mt-1" style="font-size:0.78rem;">
                            {{ $jukir->lokasi->titik_parkir ?? '-' }} &bull; {{ $jukir->lokasi->lokasi_parkir ?? '-' }}
                        </div>

                        {{-- Status Badges --}}
                        <div class="d-flex justify-content-center flex-wrap gap-1 mt-3">
                            @php
                                $ketColor = match($jukir->ket_jukir) {
                                    'Active'     => 'success',
                                    'Pending'    => 'warning',
                                    'Non Active' => 'danger',
                                    default      => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-light-{{ $ketColor }} text-{{ $ketColor }}" style="font-size:0.7rem;">{{ $jukir->ket_jukir ?? 'Active' }}</span>

                            @if($jukir->ket_jukir != 'Non Active')
                                @php $statusClass = $jukir->status == 'Non-Tunai' ? 'success' : 'warning'; @endphp
                                <span class="badge bg-light text-{{ $statusClass }} border" style="font-size:0.7rem;">{{ $jukir->status ?? 'Tunai' }}</span>
                            @else
                                <span class="badge bg-light text-danger border" style="font-size:0.7rem;">
                                    {{ $jukir->tgl_nonactive ? date('d/m/Y', strtotime($jukir->tgl_nonactive)) : '-' }}
                                </span>
                            @endif
                        </div>

                        @if($jukir->status == 'Non-Tunai' && $jukir->merchant)
                            <div class="mt-3 px-3 py-2 rounded-2" style="background:#f9fafb; font-size:0.8rem; color:#374151;">
                                <i class="ti ti-id me-1 opacity-50"></i> {{ $jukir->merchant->merchant_name ?? '-' }}
                            </div>
                        @endif
                    </div>

                    {{-- Potensi Retribusi --}}
                    <div class="dj-section-title">Potensi Retribusi</div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <div class="dj-stat-box">
                                <div class="dj-stat-label">Harian</div>
                                <div class="dj-stat-value">Rp {{ number_format($jukir->potensi_harian, 0, ',', '.') }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="dj-stat-box">
                                <div class="dj-stat-label">Bulanan</div>
                                <div class="dj-stat-value primary">Rp {{ number_format($jukir->potensi_bulanan, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>

                    @if($jukir->tgl_pkh_upl)
                        <div class="mt-3 pt-3 border-top">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="ti ti-discount-check text-success" style="font-size:0.9rem;"></i>
                                <span style="font-size:0.78rem; color:#059669; font-weight:500;">Uji Petik — {{ date('d M Y', strtotime($jukir->tgl_pkh_upl)) }}</span>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="dj-stat-box">
                                        <div class="dj-stat-label">Harian</div>
                                        <div class="dj-stat-value" style="font-size:0.9rem;">Rp {{ number_format($jukir->uji_petik, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="dj-stat-box">
                                        <div class="dj-stat-label">Bulanan</div>
                                        <div class="dj-stat-value primary" style="font-size:0.9rem;">Rp {{ number_format($jukir->potensi_bulanan_upl, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Lampiran --}}
                    @if($jukir->document)
                        <div class="mt-4 pt-3 border-top">
                            <div class="dj-section-title">Lampiran</div>
                            <div class="d-grid gap-2">
                                <a href="{{ asset('storage/' . $jukir->document) }}" target="_blank" class="btn btn-light btn-sm border" style="font-size:0.78rem;">
                                    <i class="ti ti-file-text me-1"></i> Lihat Dokumen
                                </a>
                                @if($jukir->merchant && $jukir->merchant->qris)
                                <a href="{{ asset('storage/' . $jukir->merchant->qris) }}" target="_blank" class="btn btn-light btn-sm border" style="font-size:0.78rem;">
                                    <i class="ti ti-qrcode me-1"></i> QRIS Merchant
                                </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right column: personal + lokasi + legal --}}
        <div class="col-12 col-md-8 col-xl-9">
            <div class="row g-3">

                {{-- Informasi Personal --}}
                <div class="col-12">
                    <div class="dj-card">
                        <div class="dj-card-body">
                            <div class="dj-section-title">Informasi Personal</div>
                            <div class="row g-3">
                                <div class="col-6 col-sm-4 col-lg-3">
                                    <span class="dj-label">Nama Jukir</span>
                                    <span class="dj-value">{{ $jukir->nama_jukir ?? '-' }}</span>
                                </div>
                                <div class="col-6 col-sm-4 col-lg-3">
                                    <span class="dj-label">NIK</span>
                                    <span class="dj-value">{{ $jukir->nik_jukir ?? '-' }}</span>
                                </div>
                                <div class="col-6 col-sm-4 col-lg-3">
                                    <span class="dj-label">Tempat Lahir</span>
                                    <span class="dj-value">{{ $jukir->tempat_lahir ?? '-' }}</span>
                                </div>
                                <div class="col-6 col-sm-4 col-lg-3">
                                    <span class="dj-label">Tanggal Lahir</span>
                                    <span class="dj-value">{{ $jukir->tgl_lahir ? \Carbon\Carbon::parse($jukir->tgl_lahir)->translatedFormat('d M Y') : '-' }}</span>
                                </div>
                                <div class="col-6 col-sm-4 col-lg-3">
                                    <span class="dj-label">Jenis Kelamin</span>
                                    <span class="dj-value">{{ $jukir->jenis_kelamin ?? '-' }}</span>
                                </div>
                                <div class="col-6 col-sm-4 col-lg-3">
                                    <span class="dj-label">Agama</span>
                                    <span class="dj-value">{{ $jukir->agama ?? '-' }}</span>
                                </div>
                                <div class="col-6 col-sm-4 col-lg-3">
                                    <span class="dj-label">No. HP</span>
                                    <span class="dj-value">{{ $jukir->telepon ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="border-top mt-4 pt-4">
                                <div class="dj-section-title">Alamat Domisili</div>
                                <p class="mb-3" style="font-size:0.875rem; color:#374151;">{{ $jukir->alamat ?? '-' }}</p>
                                <div class="row g-3">
                                    <div class="col-6 col-sm-4">
                                        <span class="dj-label">Kelurahan</span>
                                        <span class="dj-value">{{ $jukir->kel_alamat ?? '-' }}</span>
                                    </div>
                                    <div class="col-6 col-sm-4">
                                        <span class="dj-label">Kecamatan</span>
                                        <span class="dj-value">{{ $jukir->kec_alamat ?? '-' }}</span>
                                    </div>
                                    <div class="col-6 col-sm-4">
                                        <span class="dj-label">Kab/Kota</span>
                                        <span class="dj-value">{{ $jukir->kab_kota_alamat ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Lokasi & Jadwal + Informasi Legal side by side on large screens --}}
                <div class="col-12 col-lg-7">
                    <div class="dj-card h-100">
                        <div class="dj-card-body">
                            <div class="d-flex justify-content-between align-items-start mb-0">
                                <div class="dj-section-title mb-0">Lokasi Parkir &amp; Jadwal</div>
                                <a href="{{ route('lokasi.detail', $jukir->lokasi->id) }}" class="text-primary text-decoration-none" style="font-size:0.75rem; white-space:nowrap;" target="_blank">
                                    Detail Lokasi <i class="ti ti-external-link"></i>
                                </a>
                            </div>
                            <div class="border-bottom mb-4 mt-2"></div>
                            <div class="row g-3">
                                <div class="col-6 col-sm-4">
                                    <span class="dj-label">Titik Parkir</span>
                                    <span class="dj-value">{{ $jukir->lokasi->titik_parkir ?? '-' }}</span>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <span class="dj-label">Lokasi Parkir</span>
                                    <span class="dj-value">{{ $jukir->lokasi->lokasi_parkir ?? '-' }}</span>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <span class="dj-label">Waktu Kerja</span>
                                    <span class="dj-value">{{ $jukir->waktu_kerja ?? '-' }}</span>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <span class="dj-label">Hari Kerja/Minggu</span>
                                    <span class="dj-value">{{ $jukir->jml_hari_kerja ?? '-' }} Hari</span>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <span class="dj-label">Hari Kerja/Bulan</span>
                                    <span class="dj-value">{{ $jukir->hari_kerja_bulan ?? '-' }} Hari</span>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <span class="dj-label">Hari Libur</span>
                                    @if($jukir->hari_libur)
                                        @php $hl = json_decode($jukir->hari_libur); @endphp
                                        @if(is_array($hl))
                                            <div class="d-flex flex-wrap gap-1 mt-1">
                                                @foreach($hl as $day)
                                                    <span class="badge bg-light text-secondary border" style="font-size:0.65rem; font-weight:400;">{{ $day }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="dj-value">{{ $jukir->hari_libur }}</span>
                                        @endif
                                    @else
                                        <span class="dj-value">-</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-5">
                    <div class="dj-card h-100">
                        <div class="dj-card-body">
                            <div class="dj-section-title">Informasi Legal</div>
                            <div class="row g-3">
                                <div class="col-6">
                                    <span class="dj-label">No. Perjanjian</span>
                                    <span class="dj-value">{{ $jukir->no_perjanjian ?? '-' }}</span>
                                </div>
                                <div class="col-6">
                                    <span class="dj-label">Tgl Perjanjian</span>
                                    <span class="dj-value">{{ $jukir->tgl_perjanjian ? date('d/m/Y', strtotime($jukir->tgl_perjanjian)) : '-' }}</span>
                                </div>
                                <div class="col-6">
                                    <span class="dj-label">Tgl Awal Taping</span>
                                    <span class="dj-value">{{ $jukir->tgl_terbit_qr ? date('d/m/Y', strtotime($jukir->tgl_terbit_qr)) : '-' }}</span>
                                </div>
                                <div class="col-6">
                                    <span class="dj-label">Korlap</span>
                                    <span class="dj-value">{{ $jukir->lokasi->korlap->nama ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>{{-- end right inner row --}}
        </div>
    </div>{{-- end top row --}}

    {{-- ===== SECOND ROW: Histori + Pembantu ===== --}}
    <div class="row g-3 mb-2">
        {{-- Histori Jukir --}}
        <div class="col-12 col-md-6">
            <div class="dj-card h-100">
                <div class="dj-card-body">
                    <div class="d-flex justify-content-between align-items-center mb-0">
                        <div class="dj-section-title mb-0"><i class="ti ti-history me-1"></i> Histori Jukir</div>
                        @can('manageAdmin')
                            <button type="button" class="btn btn-sm btn-primary" wire:click="openCreateHistori" style="font-size:0.75rem; padding:4px 12px;">
                                <i class="ti ti-plus me-1"></i> Tambah
                            </button>
                        @endcan
                    </div>
                    <div class="border-bottom mt-2 mb-4"></div>
                    <div class="timeline-container timeline-scroll">
                        @forelse($historiList as $h)
                            @php
                                $jenisColor = match($h->jenis_histori) {
                                    'Jukir Libur'        => 'success',
                                    'Ganti PKH'          => 'info',
                                    'Kompensasi PKH'     => 'warning',
                                    'Jukir Berhenti'     => 'danger',
                                    'Ganti Jukir'        => 'primary',
                                    default              => 'secondary',
                                };
                            @endphp
                            <div class="d-flex mb-4 position-relative">
                                @if(!$loop->last)
                                    <div class="position-absolute border-start border-2 border-light h-100" style="left: 6px; top: 20px; z-index: 0;"></div>
                                @endif
                                <div class="rounded-circle bg-{{ $jenisColor }} mt-1 flex-shrink-0" style="width:13px; height:13px; z-index:1; border: 2px solid #fff; box-shadow: 0 0 0 2px #f3f4f6;"></div>
                                <div class="ms-3 flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <span class="badge bg-light-{{ $jenisColor }} text-{{ $jenisColor }} fw-bold " style="font-size:0.8rem; letter-spacing:0.4px;">{{ strtoupper($h->jenis_histori ?? '-') }}</span>
                                            <div class="text-muted mt-1" style="font-size:0.72rem;">{{ $h->tgl_histori ? date('d M Y', strtotime($h->tgl_histori)) : '-' }}</div>
                                        </div>
                                        <div class="d-flex gap-1">
                                            <button type="button" class="btn btn-sm btn-icon btn-light-warning shadow-none" style="width:26px;height:26px;" wire:click="openEditHistori({{ $h->id }})">
                                                <i class="ti ti-edit" style="font-size:0.75rem;"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-icon btn-light-danger shadow-none" style="width:26px;height:26px;" wire:click="confirmDeleteHistori({{ $h->id }})">
                                                <i class="ti ti-trash" style="font-size:0.75rem;"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <p class="small text-muted mb-0 mt-2" style="line-height:1.5; font-size:0.8rem;">{{ $h->histori ?? '-' }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="dj-empty">
                                <i class="ti ti-history-off"></i>
                                <span>Belum ada data histori</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Jukir Pembantu --}}
        <div class="col-12 col-md-6">
            <div class="dj-card h-100">
                <div class="dj-card-body pb-0">
                    <div class="d-flex justify-content-between align-items-center mb-0">
                        <div class="dj-section-title mb-0"><i class="ti ti-users me-1"></i> Jukir Pembantu</div>
                        @can('manageAdmin')
                            <button type="button" class="btn btn-sm btn-primary" wire:click="openCreatePembantu" style="font-size:0.75rem; padding:4px 12px;">
                                <i class="ti ti-plus me-1"></i> Tambah
                            </button>
                        @endcan
                    </div>
                    <div class="border-bottom mt-2 mb-0"></div>
                </div>
                <div class="table-responsive">
                    <table class="table dj-tbl mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Nama</th>
                                <th class="text-center">Status</th>
                                <th class="pe-4 text-center" width="80">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pembantuList as $p)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $p->foto ? asset('storage/' . $p->foto) : 'https://placehold.co/40x40?text=U' }}"
                                             onerror="this.onerror=null; this.src='https://placehold.co/40x40?text=U';"
                                             class="rounded-circle flex-shrink-0" style="width:30px;height:30px;object-fit:cover;">
                                        <span class="fw-medium" style="font-size:0.85rem;">{{ $p->nama }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($p->status == '1')
                                        <span class="badge bg-light text-success border border-success border-opacity-10 fw-normal" style="font-size:0.65rem;">Active</span>
                                    @else
                                        <span class="badge bg-light text-danger border border-danger border-opacity-10 fw-normal" style="font-size:0.65rem;">{{ $p->status ?? 'Non' }}</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-center">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <button type="button" class="btn btn-sm btn-icon btn-light-warning" style="width:24px;height:24px;" wire:click="openEditPembantu({{ $p->id }})">
                                            <i class="ti ti-edit" style="font-size:0.7rem;"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-icon btn-light-danger" style="width:24px;height:24px;" wire:click="confirmDeletePembantu({{ $p->id }})">
                                            <i class="ti ti-trash" style="font-size:0.7rem;"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3">
                                    <div class="dj-empty">
                                        <i class="ti ti-users-minus"></i>
                                        <span>Belum ada data jukir pembantu</span>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== RINGKASAN DATA TAHUNAN ===== --}}
    <div class="dj-card mb-4">
        <div class="dj-card-body pb-0">
            <div class="dj-section-title mb-0"><i class="ti ti-calendar me-1"></i> Ringkasan Data Per Tahun</div>
            <div class="border-bottom mt-2 mb-0"></div>
        </div>
        <div class="table-responsive">
            <table class="table dj-tbl mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Tahun</th>
                        <th class="text-end">Jml. Transaksi</th>
                        <th class="text-end">Non Tunai</th>
                        <th class="text-end">Potensi</th>
                        <th class="text-end">Kompensasi</th>
                        <th class="pe-4 text-end">Kurang Setor</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($summaryYears as $sy)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $sy->tahun }}</td>
                            <td class="text-end">{{ number_format($sy->total_trx, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($sy->total_non_tunai, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($sy->total_potensi, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($sy->total_kompensasi, 0, ',', '.') }}</td>
                            <td class="pe-4 text-end {{ $sy->total_kurang_setor >= 0 ? 'text-success' : 'text-warning' }}">Rp {{ number_format($sy->total_kurang_setor, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="dj-empty">
                                    <i class="ti ti-calendar-off"></i>
                                    <span>Belum ada data ringkasan tahunan</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    @if($summaryYears->count() > 0)
                        @php
                            $totalTrx = $summaryYears->sum('total_trx');
                            $totalNonTunai = $summaryYears->sum('total_non_tunai');
                            $totalPotensi = $summaryYears->sum('total_potensi');
                            $totalKompensasi = $summaryYears->sum('total_kompensasi');
                            $totalKurangSetor = $summaryYears->sum('total_kurang_setor');
                        @endphp
                        <tr>
                            <th class="ps-4 py-3">Total</th>
                            <th class="text-end py-3">{{ number_format($totalTrx, 0, ',', '.') }}</th>
                            <th class="text-end py-3">Rp {{ number_format($totalNonTunai, 0, ',', '.') }}</th>
                            <th class="text-end py-3">Rp {{ number_format($totalPotensi, 0, ',', '.') }}</th>
                            <th class="text-end py-3">Rp {{ number_format($totalKompensasi, 0, ',', '.') }}</th>
                            <th class="pe-4 py-3 text-end {{ $totalKurangSetor >= 0 ? 'text-success' : 'text-warning' }}">Rp {{ number_format($totalKurangSetor, 0, ',', '.') }}</th>
                        </tr>
                    @endif
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ===== RINGKASAN DATA BULANAN ===== --}}
    <div class="dj-card mb-2">
        <div class="dj-card-body pb-0">
            <div class="d-flex justify-content-between align-items-center mb-0">
                <div class="dj-section-title mb-0"><i class="ti ti-chart-bar me-1"></i> Ringkasan Data Per Bulan</div>
                <select wire:model.live="selectedYear" class="form-select form-select-sm border-0 bg-light" style="width:100px; font-size:0.8rem;">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="border-bottom mt-2 mb-0"></div>
        </div>
        <div class="table-responsive">
            <table class="table dj-tbl mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Bulan</th>
                        <th class="text-end">Jml. Transaksi</th>
                        <th class="text-end">Non Tunai</th>
                        <th class="text-end">Potensi</th>
                        <th class="text-end">Kompensasi</th>
                        <th class="pe-4 text-end">Kurang Setor</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalTrx = 0; $totalNonTunai = 0;
                        $totalPotensi = 0; $totalKompensasi = 0; $totalKurangSetor = 0;
                    @endphp
                    @forelse($summaryMonths as $sm)
                        @php
                            $totalTrx          += $sm->jml_trx;
                            $totalNonTunai     += $sm->non_tunai;
                            $totalPotensi      += $sm->potensi;
                            $totalKompensasi   += $sm->kompensasi;
                            $totalKurangSetor  += $sm->kurang_setor;
                            $monthName = \Carbon\Carbon::createFromDate($sm->tahun, $sm->bulan, 1)->translatedFormat('F Y');
                        @endphp
                        <tr>
                            <td class="ps-4 fw-medium">{{ $monthName }}</td>
                            <td class="text-end">{{ number_format($sm->jml_trx, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($sm->non_tunai, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($sm->potensi, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($sm->kompensasi, 0, ',', '.') }}</td>
                            <td class="pe-4 text-end {{ $sm->kurang_setor >= 0 ? 'text-success' : 'text-warning' }}">Rp {{ number_format($sm->kurang_setor, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="dj-empty">
                                    <i class="ti ti-chart-bar-off"></i>
                                    <span>Belum ada data ringkasan untuk tahun {{ $selectedYear }}</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($summaryMonths->count() > 0)
                <tfoot>
                    <tr>
                        <th class="ps-4 py-3">Total</th>
                        <th class="py-3 text-end">{{ number_format($totalTrx, 0, ',', '.') }}</th>
                        <th class="py-3 text-end">Rp {{ number_format($totalNonTunai, 0, ',', '.') }}</th>
                        <th class="py-3 text-end">Rp {{ number_format($totalPotensi, 0, ',', '.') }}</th>
                        <th class="py-3 text-end">Rp {{ number_format($totalKompensasi, 0, ',', '.') }}</th>
                        <th class="pe-4 py-3 text-end {{ $totalKurangSetor >= 0 ? 'text-success' : 'text-warning' }}">Rp {{ number_format($totalKurangSetor, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- ===== RIWAYAT TRANSAKSI NON-TUNAI ===== --}}
    @if($jukir->merchant_id)
    <div class="dj-card mb-2" wire:key="trans-history">
        <div class="dj-card-body pb-0">
            <div class="d-flex justify-content-between align-items-center mb-0">
                <div class="dj-section-title mb-0"><i class="ti ti-receipt me-1"></i> Riwayat Transaksi Non-Tunai</div>
                <div class="d-flex gap-2 mb-2">
                    <div class="input-group input-group-sm" style="width: auto;">
                        <span class="input-group-text bg-light border-0">Dari</span>
                        <input type="date" wire:model.live="startDate" class="form-control border-0 bg-light" style="width: 130px; font-size: 0.8rem;">
                    </div>
                    <div class="input-group input-group-sm" style="width: auto;">
                        <span class="input-group-text bg-light border-0">Sampai</span>
                        <input type="date" wire:model.live="endDate" class="form-control border-0 bg-light" style="width: 130px; font-size: 0.8rem;">
                    </div>
                </div>
            </div>
            <div class="border-bottom mt-2 mb-0"></div>
        </div>
        <div class="table-responsive">
            <table class="table dj-tbl mb-0">
                <thead>
                    <tr>
                        <th>Merchant</th>
                        <th class="ps-4">Tanggal</th>
                        <th>Syslog</th>
                        <th>Issuer</th>
                        <th>Sender</th>
                        <th class="text-end">Total Nilai</th>
                        <th class="pe-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trans)
                    <tr>
                        <td>{{ $trans->merchant_name ?? '-' }}</td>
                        <td class="ps-4 fw-medium">{{ date('d M Y H:i:s', strtotime($trans->tgl_transaksi)) }}</td>
                        <td>{{ $trans->syslog ?? '-' }}</td>
                        <td>{{ $trans->issuer_name ?? '-' }}</td>
                        <td>{{ $trans->sender_name ?? '-' }}</td>
                        <td class="text-end fw-medium">Rp {{ number_format($trans->total_nilai, 0, ',', '.') }}</td>
                        <td class="pe-4 text-center">
                            @if($trans->status == 'SUCCEED')
                                <span class="badge bg-light text-success border border-success border-opacity-25 fw-normal" style="font-size:0.68rem;">{{ $trans->status }}</span>
                            @else
                                <span class="badge bg-light text-warning border border-warning border-opacity-25 fw-normal" style="font-size:0.68rem;">{{ $trans->status ?? 'Pending' }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4">
                            <div class="dj-empty">
                                <i class="ti ti-receipt-off"></i>
                                <span>Belum ada data transaksi</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
        <div class="px-4 py-3 border-top" style="background:#fafafa; border-radius: 0 0 12px 12px;">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
    @endif

    {{-- Modal Components --}}
    @livewire('admin::jukir.histori.histori_jukir')
    @livewire('admin::jukir.pembantu.pembantu_jukir')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.alert-dismissible').forEach(function (alert) {
                setTimeout(function () {
                    var bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                    bsAlert.close();
                }, 3000);
            });
        });
    </script>

    <script>
        document.addEventListener('livewire:initialized', () => {
           @this.on('open-modal', (event) => {
               const modal = new bootstrap.Modal(document.getElementById(event.name));
               modal.show();
           });

           @this.on('close-modal', (event) => {
               const modalElement = document.getElementById(event.name);
               const modal = bootstrap.Modal.getInstance(modalElement);
               if (modal) {
                   modal.hide();
               }
           });
        });
    </script>
</div>
