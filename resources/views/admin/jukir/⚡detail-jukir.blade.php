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
    protected $paginationTheme = 'bootstrap';

    public function mount($id)
    {
        $this->jukir = Jukir::with('lokasi')->findOrFail($id);
    }

    #[On('refresh-detail-jukir')]
    public function render()
    {
        $transactions = collect();
        if ($this->jukir->merchant_id) {
            $transactions = TransNonTunai::where('merchant_id', $this->jukir->merchant_id)
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

        return $this->view()->title('Detail Jukir')->with([
            'transactions' => $transactions,
            'historiList'  => $historiList,
            'pembantuList' => $pembantuList,
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
    <!-- Header -->
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

    <!-- Simplified Content -->
    <div class="row">
        <!-- Sidebar Profile and Quick Stats -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center p-4">
                    <img src="{{ $jukir->foto ? asset('storage/' . $jukir->foto) : 'https://ui-avatars.com/api/?name='.urlencode($jukir->nama_jukir).'&background=eaeaea&color=333&size=128' }}" 
                         alt="User profile" 
                         class="rounded-circle mx-auto d-block mb-3 border border-3 border-white shadow-sm" 
                         style="width: 120px; height: 120px; object-fit: cover;">
                    <h3 class="mb-1 fw-bold text-uppercase">{{ $jukir->nama_jukir }}</h3>
                    <p class="text-muted mb-3 fs-6 text-uppercase fw-semibold">{{ $jukir->lokasi->titik_parkir ?? '-' }} <br>
                        {{ $jukir->lokasi->lokasi_parkir ?? '-' }}
                    </p>
                    
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        @php
                            $ketColor = 'secondary';
                            if($jukir->ket_jukir == 'Active') $ketColor = 'success';
                            elseif($jukir->ket_jukir == 'Pending') $ketColor = 'warning';
                            elseif($jukir->ket_jukir == 'Non Active') $ketColor = 'danger';
                        @endphp
                        <span class="badge bg-light-{{ $ketColor }} text-{{ $ketColor }} px-2 py-1">{{ $jukir->ket_jukir ?? 'Active' }}</span>
                        
                        @if($jukir->ket_jukir != 'Non Active')                            
                            @php
                                $statusClass = $jukir->status == 'Non-Tunai' ? 'success' : 'warning';
                            @endphp
                            <span class="badge bg-light text-{{ $statusClass }} border px-2 py-1">{{ $jukir->status ?? 'Tunai' }}</span>
                        @endif
                        
                        @if($jukir->ket_jukir == 'Non Active')
                            <span class="badge bg-light text-danger border px-2 py-1">{{ $jukir->tgl_nonactive ? date('d/m/Y', strtotime($jukir->tgl_nonactive)) : '-' }}</span>
                        @endif
                    </div>

                    @if($jukir->status == 'Non-Tunai')
                        <div class="bg-light rounded p-2 text-center mt-2">
                            <span class="fs-5 fw-medium text-dark"><i class="ti ti-id me-1 opacity-50"></i>{{ $jukir->merchant->merchant_name ?? '-' }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-grid gap-2 mb-4">
                <a href="{{ route('jukir.edit', $jukir->id) }}" class="btn btn-outline-primary">
                    <i class="ti ti-edit me-1"></i> Edit Detail Profil
                </a>
                <a href="{{ route('jukir.index') }}" class="btn btn-light border">
                    <i class="ti ti-arrow-left me-1"></i> Kembali ke Daftar
                </a>
            </div>

            <!-- Rincian Potensi -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h6 class="mb-0 fw-bold">Potensi Retribusi</h6>
                </div>
                <div class="card-body p-4 pt-3">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Harian</small>
                        <span class="fw-bold fs-5">Rp {{ number_format($jukir->potensi_harian, 0, ',', '.') }}</span>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted d-block mb-1">Bulanan</small>
                        <span class="fw-bold fs-5 text-primary">Rp {{ number_format($jukir->potensi_bulanan, 0, ',', '.') }}</span>
                    </div>

                    @if($jukir->tgl_pkh_upl)
                    <div class="mt-4 pt-3 border-top">
                        <div class="mb-3">
                            <i class="ti ti-discount-check text-success"></i>
                            <span class="fw-medium text-success">Uji Petik ({{ date('d M Y', strtotime($jukir->tgl_pkh_upl)) }})</span>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Harian</small>
                            <small class="fw-bold fs-5">Rp {{ number_format($jukir->uji_petik, 0, ',', '.') }}</small>
                        </div>
                        <div class="mb-0">
                            <small class="text-muted d-block mb-1">Bulanan</small>
                            <small class="fw-bold fs-5 text-primary">Rp {{ number_format($jukir->potensi_bulanan_upl, 0, ',', '.') }}</small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            @if($jukir->document)
            <div class="card border-0 shadow-sm mb-4">
                 <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h6 class="mb-0 fw-bold">Lampiran File</h6>
                </div>
                <div class="card-body p-4 pt-3 gap-2 d-grid">
                    <a href="{{ asset('storage/' . $jukir->document) }}" target="_blank" class="btn btn-light border btn-sm">Lihat Dokumen</a>
                    <a href="{{ asset('storage/' . $jukir->merchant->qris) }}" target="_blank" class="btn btn-light border btn-sm">QRIS Merchant</a>
                </div>
            </div>
            @endif            
        </div>

        <!-- Main Details (Right Column) -->
        <div class="col-md-8">
            <!-- Data Personal & Alamat -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-4 border-bottom pb-2">Informasi Personal</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-sm-4">
                            <small class="text-muted d-block mb-1">Nama Jukir</small>
                            <span class="fw-medium">{{ $jukir->nama_jukir ?? '-' }}</span>
                        </div>
                        <div class="col-sm-4">
                            <small class="text-muted d-block mb-1">NIK</small>
                            <span class="fw-medium">{{ $jukir->nik_jukir ?? '-' }}</span>
                        </div>
                        <div class="col-sm-4">
                            <small class="text-muted d-block mb-1">Tempat, Tanggal Lahir</small>
                            <span class="fw-medium">{{ $jukir->tempat_lahir ?? '-' }}, {{ $jukir->tgl_lahir ? \Carbon\Carbon::parse($jukir->tgl_lahir)->translatedFormat('d M Y') : '-' }}</span>
                        </div>
                        <div class="col-sm-4">
                            <small class="text-muted d-block mb-1">Jenis Kelamin</small>
                            <span class="fw-medium">{{ $jukir->jenis_kelamin ?? '-' }}</span>
                        </div>
                        <div class="col-sm-4">
                            <small class="text-muted d-block mb-1">Agama</small>
                            <span class="fw-medium">{{ $jukir->agama ?? '-' }}</span>
                        </div>
                        <div class="col-sm-4">
                            <small class="text-muted d-block mb-1">No. HP</small>
                            <span class="fw-medium">{{ $jukir->telepon ?? '-' }}</span>
                        </div>
                        
                    </div>
                    
                    <h6 class="fw-bold mb-3 mt-2 border-bottom pb-2">Alamat Domisili</h6>
                    <p class="mb-3 text-dark">{{ $jukir->alamat ?? '-' }}</p>
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <small class="text-muted d-block mb-1">Kelurahan</small>
                            <span class="fw-medium">{{ $jukir->kel_alamat ?? '-' }}</span>
                        </div>
                        <div class="col-sm-4">
                            <small class="text-muted d-block mb-1">Kecamatan</small>
                            <span class="fw-medium">{{ $jukir->kec_alamat ?? '-' }}</span>
                        </div>
                        <div class="col-sm-4">
                            <small class="text-muted d-block mb-1">Kab/Kota</small>
                            <span class="fw-medium">{{ $jukir->kab_kota_alamat ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Penugasan & Jadwal -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                        <h6 class="fw-bold mb-0">Lokasi Parkir & Jadwal</h6>
                        <a href="{{ route('lokasi.detail', $jukir->lokasi->id) }}" class="small link-primary text-decoration-none" target="_blank">Lihat Detail Lokasi <i class="ti ti-external-link"></i></a>
                    </div>
                    <div class="row g-4">
                        <div class="col-sm-4">
                            <small class="text-muted d-block mb-1">Titik Parkir</small>
                            <span class="fw-semibold d-block text-dark">{{ $jukir->lokasi->titik_parkir ?? '-' }}</span>
                        </div>
                        <div class="col-sm-4">
                            <small class="text-muted d-block mb-1">Lokasi Parkir</small>
                            <span class="fw-semibold d-block text-dark">{{ $jukir->lokasi->lokasi_parkir ?? '-' }}</span>
                        </div>
                        <div class="col-sm-4">
                            <small class="text-muted d-block mb-1">Waktu Kerja</small>
                            <span class="fw-medium">{{ $jukir->waktu_kerja ?? '-' }}</span>
                        </div>
                        <div class="col-sm-4">
                            <small class="text-muted d-block mb-1">Hari Kerja Perminggu</small>
                            <span class="fw-medium">{{ $jukir->jml_hari_kerja ?? '-' }} Hari/Minggu</span><br>                            
                        </div>
                        <div class="col-sm-4">
                            <small class="text-muted d-block mb-1">Hari Kerja Perbulan</small>
                            <span class="fw-medium">{{ $jukir->hari_kerja_bulan ?? '-' }} Hari/Bulan</span><br>                            
                        </div>
                        <div class="col-sm-4">
                            <small class="text-muted d-block mb-1">Hari Libur</small>
                            @if($jukir->hari_libur)
                                @php $hl = json_decode($jukir->hari_libur); @endphp
                                @if(is_array($hl))
                                    <div class="d-flex flex-wrap gap-1">
                                    @foreach($hl as $day)
                                        <span class="badge bg-light text-secondary border px-2 font-weight-normal">{{ $day }}</span>
                                    @endforeach
                                    </div>
                                @else
                                    <span class="badge bg-light text-secondary border px-2 font-weight-normal">{{ $jukir->hari_libur }}</span>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm mb-4 h-100">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-4 border-bottom pb-2">Informasi Legal</h6>
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <small class="text-muted d-block mb-1">No. Perjanjian</small>
                                    <span class="fw-medium">{{ $jukir->no_perjanjian ?? '-' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block mb-1">Tgl Perjanjian</small>
                                    <span class="fw-medium">{{ $jukir->tgl_perjanjian ? date('d/m/Y', strtotime($jukir->tgl_perjanjian)) : '-' }}</span>
                                </div>                                
                                <div class="col-6">
                                    <small class="text-muted d-block mb-1">Tgl Awal Taping</small>
                                    <span class="fw-medium">{{ $jukir->tgl_terbit_qr ? date('d/m/Y', strtotime($jukir->tgl_terbit_qr)) : '-' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block mb-1">Korlap</small>
                                    <span class="fw-medium">{{ $jukir->lokasi->korlap->nama ?? '-' }}</span>
                                </div>
                            </div>                            
                        </div>
                    </div>                
                </div>
            </div>             

            <div class="row">
                <div class="col-md-6">
                    <!-- Histori Jukir -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold"><i class="ti ti-history me-1"></i> Histori Jukir</h6>
                                @can('manageAdmin')
                                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreateHistori">
                                        <i class="ti ti-plus me-1"></i> Tambah
                                    </button>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body p-4 mt-1">
                            <style>
                                .timeline-scroll {
                                    max-height: 400px;
                                    overflow-y: auto;
                                    padding-right: 10px;
                                }
                                .timeline-scroll::-webkit-scrollbar {
                                    width: 4px;
                                }
                                .timeline-scroll::-webkit-scrollbar-track {
                                    background: transparent;
                                }
                                .timeline-scroll::-webkit-scrollbar-thumb {
                                    background: #e9ecef;
                                    border-radius: 10px;
                                }
                                .timeline-scroll::-webkit-scrollbar-thumb:hover {
                                    background: #dee2e6;
                                }
                            </style>
                            <div class="timeline-container timeline-scroll">
                                @forelse($historiList as $h)
                                    @php
                                        $jenisColor = match($h->jenis_histori) {
                                            'Mutasi' => 'primary',
                                            'Cuti' => 'info',
                                            'Peringatan' => 'warning',
                                            'Sanksi' => 'danger',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <div class="d-flex mb-4 position-relative">
                                        @if(!$loop->last)
                                            <div class="position-absolute border-start border-2 border-light h-100" style="left: 6px; top: 24px; z-index: 0;"></div>
                                        @endif
                                        <div class="rounded-circle bg-{{ $jenisColor }} mt-1 shadow-sm" style="width: 14px; height: 14px; z-index: 1; border: 3px solid #fff;"></div>
                                        <div class="ms-3 flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <span class="badge bg-light-{{ $jenisColor }} text-{{ $jenisColor }} mb-1 fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">{{ strtoupper($h->jenis_histori ?? '-') }}</span>
                                                    <div class="small text-muted fw-medium" style="font-size: 0.75rem;">{{ $h->tgl_histori ? date('d M Y', strtotime($h->tgl_histori)) : '-' }}</div>
                                                </div>
                                                <div class="d-flex gap-1">
                                                    <button type="button" class="btn btn-sm btn-icon btn-light-warning shadow-none" style="width: 26px; height: 26px;" title="Edit" wire:click="openEditHistori({{ $h->id }})">
                                                        <i class="ti ti-edit" style="font-size: 0.8rem;"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-icon btn-light-danger shadow-none" style="width: 26px; height: 26px;" title="Hapus" wire:click="confirmDeleteHistori({{ $h->id }})">
                                                        <i class="ti ti-trash" style="font-size: 0.8rem;"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <p class="small text-muted mb-0 mt-2 pe-2" style="line-height: 1.4;">{{ $h->histori ?? '-' }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5 text-muted small">
                                        <i class="ti ti-history-off fs-1 d-block mt-2 mb-2 text-muted opacity-50"></i>
                                        Belum ada data histori
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <!-- Jukir Pembantu -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold"><i class="ti ti-users me-1"></i> Jukir Pembantu</h6>
                                @can('manageAdmin')
                                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreatePembantu">
                                        <i class="ti ti-plus me-1"></i> Tambah
                                    </button>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body p-0 mt-3">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4 py-3 small fw-normal text-muted border-0">Nama</th>
                                            <th class="py-3 small fw-normal text-muted text-center border-0">Status</th>
                                            <th class="pe-4 py-3 small fw-normal text-muted text-center border-0" width="80">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pembantuList as $p)
                                        <tr>
                                            <td class="ps-4 border-0 border-bottom">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $p->foto ? asset('storage/' . $p->foto) : 'https://ui-avatars.com/api/?name='.urlencode($p->nama).'&background=eaeaea&color=333&size=40' }}" 
                                                         class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                                                    <span class="fw-medium small d-block">{{ $p->nama }}</span>
                                                </div>
                                            </td>
                                            <td class="text-center border-0 border-bottom">
                                                @if($p->status == '1')
                                                    <span class="badge bg-light text-success border border-success border-opacity-10 fw-normal" style="font-size: 0.65rem;">Active</span>
                                                @else
                                                    <span class="badge bg-light text-danger border border-danger border-opacity-10 fw-normal" style="font-size: 0.65rem;">{{ $p->status ?? 'Non' }}</span>
                                                @endif
                                            </td>
                                            <td class="pe-4 text-center border-0 border-bottom">
                                                <div class="d-flex gap-1 justify-content-center">
                                                    <button type="button" class="btn btn-sm btn-icon btn-light-warning" style="width: 24px; height: 24px;" title="Edit" wire:click="openEditPembantu({{ $p->id }})">
                                                        <i class="ti ti-edit" style="font-size: 0.75rem;"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-icon btn-light-danger" style="width: 24px; height: 24px;" title="Hapus" wire:click="confirmDeletePembantu({{ $p->id }})">
                                                        <i class="ti ti-trash" style="font-size: 0.75rem;"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-5 text-muted small">
                                                <i class="ti ti-users-minus fs-1 d-block mt-2 mb-2 text-muted opacity-50"></i>
                                                Belum ada data jukir pembantu
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

            <!-- List Transaksi Non Tunai -->
            @if($jukir->merchant_id)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                    <h6 class="mb-4 fw-bold">Riwayat Transaksi Non-Tunai</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 small fw-normal text-muted border-0">Tanggal</th>
                                    <th class="py-3 small fw-normal text-muted border-0">Issuer</th>
                                    <th class="py-3 small fw-normal text-muted text-end border-0">Total Nilai</th>
                                    <th class="pe-4 py-3 small fw-normal text-muted text-center border-0">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $trans)
                                <tr>
                                    <td class="ps-4 border-0 border-bottom">
                                        <span class="fw-medium">{{ date('d M Y', strtotime($trans->tgl_transaksi)) }}</span>
                                    </td>
                                    <td class="border-0 border-bottom">{{ $trans->issuer_name ?? '-' }}</td>
                                    <td class="text-end fw-medium text-dark border-0 border-bottom">Rp {{ number_format($trans->total_nilai, 0, ',', '.') }}</td>
                                    <td class="pe-4 text-center border-0 border-bottom">
                                        @if($trans->status == 'SUCCEED')
                                            <span class="badge bg-light text-success border border-success border-opacity-25 fw-normal">{{ $trans->status }}</span>
                                        @else                                        
                                            <span class="badge bg-light text-warning border border-warning border-opacity-25 fw-normal">{{ $trans->status ?? 'Pending' }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted small">
                                        <i class="ti ti-receipt-off fs-1 d-block mt-2 mb-2 text-muted opacity-50"></i>
                                        Belum ada data transaksi
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($transactions->hasPages())
                <div class="card-footer bg-white border-top-0 px-4 py-3">
                    {{ $transactions->links() }}
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>

    <!-- Modal Components -->
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
