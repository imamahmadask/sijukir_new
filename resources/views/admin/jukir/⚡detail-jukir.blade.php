<?php

use Livewire\Component;
use App\Models\Jukir;

new class extends Component {
    public $jukir;

    public function mount($id)
    {
        $this->jukir = Jukir::with('lokasi')->findOrFail($id);
    }

    public function render()
    {
        return $this->view()->title('Detail Jukir');
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

    <!-- Content Overhaul -->
    <div class="row">
        <!-- Profile Header -->
        <div class="col-lg-12">
            <div class="card overflow-hidden border-0 shadow-sm mb-4">
                <div class="card-body p-0">
                    <div class="profile-header-cover" style="height: 160px; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); position: relative;">
                        <!-- Optional Background Pattern or Shape -->
                        <div style="position: absolute; right: 20px; bottom: 20px; opacity: 0.1;">
                            <i class="ti ti-user-circle" style="font-size: 150px; color: #fff;"></i>
                        </div>
                    </div>
                    <div class="px-4 pb-4">
                        <div class="d-flex align-items-end flex-wrap" style="margin-top: -60px; position: relative; z-index: 1;">
                            <div class="me-4 mb-3">
                                <img src="{{ $jukir->foto ? asset('storage/' . $jukir->foto) : 'https://ui-avatars.com/api/?name='.urlencode($jukir->nama_jukir).'&background=3f51b5&color=fff&size=128' }}" 
                                     alt="User profile" 
                                     class="rounded-circle border border-4 border-white shadow" 
                                     style="width: 140px; height: 140px; object-fit: cover; background: #fff;">
                            </div>
                            <div class="flex-grow-1 mb-4">
                                <h3 class="mb-1 fw-bold text-dark">{{ $jukir->nama_jukir }}</h3>
                                <div class="d-flex align-items-center flex-wrap">
                                    <span class="text-muted me-3 mb-1"><i class="ti ti-id me-1"></i>{{ $jukir->kode_jukir ?? '-' }}</span>
                                    <span class="text-muted me-3 mb-1"><i class="ti ti-barcode me-1"></i>{{ $jukir->nik_jukir }}</span>
                                    <span class="text-muted me-3 mb-1"><i class="ti ti-phone me-1"></i>{{ $jukir->telepon ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="mb-4 text-end">
                                <div class="mb-2">
                                    @php
                                        $ketColor = 'secondary';
                                        if($jukir->ket_jukir == 'Active') $ketColor = 'success';
                                        elseif($jukir->ket_jukir == 'Pending') $ketColor = 'warning';
                                        elseif($jukir->ket_jukir == 'Non Active') $ketColor = 'danger';
                                    @endphp
                                    <span class="badge bg-light-{{ $ketColor }} fs-6 px-3 py-2 border border-{{ $ketColor }}">
                                        <i class="ti ti-circle-check me-1"></i>{{ $jukir->ket_jukir ?? 'Active' }}
                                    </span>
                                    
                                    @php
                                        $statusClass = $jukir->status == 'Non-Tunai' ? 'success' : 'warning';
                                    @endphp
                                    <span class="badge bg-light-{{ $statusClass }} fs-6 px-3 py-2 border border-{{ $statusClass }} ms-2">
                                        <i class="ti ti-wallet me-1"></i>{{ $jukir->status ?? 'Tunai' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white py-3 border-top border-light">
                    <div class="d-flex justify-content-end">
                         <a href="{{ route('jukir.edit', $jukir->id) }}" class="btn btn-warning me-2 px-4 shadow-sm">
                            <i class="ti ti-edit me-1"></i> Edit Profil
                        </a>
                        <a href="{{ route('jukir.index') }}" class="btn btn-secondary px-4">
                            <i class="ti ti-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Column -->
        <div class="col-md-8">
            <!-- Stats Summary -->
            <div class="row">
                 <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm overflow-hidden h-100 bg-primary text-white">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="avtar avtar-l bg-white bg-opacity-25 text-white me-3">
                                    <i class="ti ti-cash fs-1"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 text-white-50">Potensi Pedapatan (Bln)</h6>
                                    <h3 class="mb-0 text-white fw-bold">Rp {{ number_format($jukir->potensi_bulanan, 0, ',', '.') }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm overflow-hidden h-100 bg-info text-white">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="avtar avtar-l bg-white bg-opacity-25 text-white me-3">
                                    <i class="ti ti-map-2 fs-1"></i>
                                </div>
                                <div class="flex-grow-1 text-truncate">
                                    <h6 class="mb-0 text-white-50">Titik Penugasan</h6>
                                    <h4 class="mb-0 text-white fw-bold">{{ Str::limit($jukir->lokasi->titik_parkir ?? '-', 22) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Information Cards -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="mb-0 fw-bold"><i class="ti ti-user me-2 text-primary"></i>Data Personal & Alamat</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-sm-6 mb-4 text-truncate">
                            <p class="text-muted mb-1 small text-uppercase fw-bold">Jenis Kelamin</p>
                            <h6 class="mb-0">{{ $jukir->jenis_kelamin ?? '-' }}</h6>
                        </div>
                        <div class="col-sm-6 mb-4 text-truncate">
                            <p class="text-muted mb-1 small text-uppercase fw-bold">Agama</p>
                            <h6 class="mb-0">{{ $jukir->agama ?? '-' }}</h6>
                        </div>
                        <div class="col-sm-6 mb-4 text-truncate">
                            <p class="text-muted mb-1 small text-uppercase fw-bold">Tempat Lahir</p>
                            <h6 class="mb-0">{{ $jukir->tempat_lahir ?? '-' }}</h6>
                        </div>
                        <div class="col-sm-6 mb-4 text-truncate">
                            <p class="text-muted mb-1 small text-uppercase fw-bold">Tanggal Lahir</p>
                            <h6 class="mb-0">{{ $jukir->tgl_lahir ? \Carbon\Carbon::parse($jukir->tgl_lahir)->translatedFormat('d F Y') : '-' }}</h6>
                        </div>
                        
                        <div class="col-12"><hr class="my-2 opacity-50"></div>
                        
                        <div class="col-12 mb-4 mt-3">
                            <p class="text-muted mb-1 small text-uppercase fw-bold">Alamat Lengkap</p>
                            <h6 class="mb-0 lh-base">{{ $jukir->alamat ?? '-' }}</h6>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <p class="text-muted mb-1 small text-uppercase fw-bold">Kelurahan</p>
                            <h6 class="mb-0">{{ $jukir->kel_alamat ?? '-' }}</h6>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <p class="text-muted mb-1 small text-uppercase fw-bold">Kecamatan</p>
                            <h6 class="mb-0">{{ $jukir->kec_alamat ?? '-' }}</h6>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <p class="text-muted mb-1 small text-uppercase fw-bold">Kab/Kota</p>
                            <h6 class="mb-0">{{ $jukir->kab_kota_alamat ?? '-' }}</h6>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Work Information Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="mb-0 fw-bold"><i class="ti ti-briefcase me-2 text-primary"></i>Penugasan & Jadwal</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-sm-6 mb-4">
                            <p class="text-muted mb-1 small text-uppercase fw-bold">Area Parkir</p>
                            <h6 class="mb-0 lh-base">{{ $jukir->lokasi->lokasi_parkir ?? '-' }}</h6>
                        </div>
                        <div class="col-sm-6 mb-4">
                            <p class="text-muted mb-1 small text-uppercase fw-bold">Waktu Kerja</p>
                            <h6 class="mb-0 lh-base">{{ $jukir->waktu_kerja ?? '-' }}</h6>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <p class="text-muted mb-1 small text-uppercase fw-bold">Intensitas Kerja</p>
                            <h6 class="mb-0">{{ $jukir->jml_hari_kerja ?? '-' }} Hari/Minggu ({{ $jukir->hari_kerja_bulan ?? '-' }} Hari/Bulan)</h6>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <p class="text-muted mb-1 small text-uppercase fw-bold">Hari Libur</p>
                            <h6 class="mb-0 mt-1">
                                @if($jukir->hari_libur)
                                    @php $hl = json_decode($jukir->hari_libur); @endphp
                                    @if(is_array($hl))
                                        @foreach($hl as $day)
                                            <span class="badge bg-light-secondary border border-secondary text-secondary me-1">{{ $day }}</span>
                                        @endforeach
                                    @else
                                        <span class="badge bg-light-secondary border border-secondary text-secondary">{{ $jukir->hari_libur }}</span>
                                    @endif
                                @else
                                    <span class="text-muted">Tidak ada hari libur</span>
                                @endif
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="col-md-4">
            <!-- Legal & Agreement Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="mb-0 fw-bold"><i class="ti ti-file-certificate me-2 text-primary"></i>Legalitas & QR</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4 bg-light-primary p-3 rounded border-start border-4 border-primary shadow-sm">
                        <p class="text-muted mb-1 small text-uppercase fw-bold">No. Perjanjian</p>
                        <h5 class="mb-0 fw-bold text-primary">{{ $jukir->no_perjanjian ?? '-' }}</h5>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-6 border-end">
                            <p class="text-muted mb-1 small text-uppercase fw-bold">Tgl Mulai</p>
                            <h6 class="mb-0">{{ $jukir->tgl_perjanjian ? date('d/m/Y', strtotime($jukir->tgl_perjanjian)) : '-' }}</h6>
                        </div>
                        <div class="col-6 ps-4">
                            <p class="text-muted mb-1 small text-uppercase fw-bold">Tgl Akhir</p>
                            @php
                                $isExpired = $jukir->tgl_akhir_perjanjian && strtotime($jukir->tgl_akhir_perjanjian) < time();
                            @endphp
                            <h6 class="mb-0 {{ $isExpired ? 'text-danger fw-bold' : '' }}">
                                {{ $jukir->tgl_akhir_perjanjian ? date('d/m/Y', strtotime($jukir->tgl_akhir_perjanjian)) : '-' }}
                                @if($isExpired) <i class="ti ti-alert-circle ms-1"></i> @endif
                            </h6>
                        </div>
                    </div>

                    <div class="text-center p-4 bg-light rounded border border-dashed border-primary">
                        <div class="avtar avtar-xxl bg-white shadow-sm mb-3">
                            <i class="ti ti-qrcode fs-1"></i>
                        </div>
                        <p class="text-muted mb-1 small text-uppercase fw-bold">Tgl Terbit QR</p>
                        <h6 class="mb-0">{{ $jukir->tgl_terbit_qr ? date('d/m/Y', strtotime($jukir->tgl_terbit_qr)) : '-' }}</h6>
                    </div>
                </div>
            </div>

            <!-- Income Detail Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="mb-0 fw-bold"><i class="ti ti-report-money me-2 text-primary"></i>Rincian Potensi</h5>
                </div>
                <div class="card-body p-4 pt-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 py-3 bg-transparent d-flex justify-content-between">
                            <span class="text-muted">Potensi Harian</span>
                            <span class="fw-bold">Rp {{ number_format($jukir->potensi_harian, 0, ',', '.') }}</span>
                        </li>
                        <li class="list-group-item px-0 py-3 bg-transparent d-flex justify-content-between border-bottom-0">
                            <span class="text-muted">Potensi Bulanan</span>
                            <span class="fw-bold text-primary fs-5">Rp {{ number_format($jukir->potensi_bulanan, 0, ',', '.') }}</span>
                        </li>
                    </ul>

                    @if($jukir->tgl_pkh_upl)
                    <div class="mt-4 p-3 rounded" style="background-color: #f0fff4; border: 1px solid #c6f6d5;">
                        <div class="d-flex align-items-center mb-2">
                            <i class="ti ti-discount-check fs-4 text-success me-2"></i>
                            <h6 class="mb-0 text-success fw-bold">Hasil Uji Petik</h6>
                        </div>
                        <p class="text-muted small mb-3">Terakhir pada {{ date('d F Y', strtotime($jukir->tgl_pkh_upl)) }}</p>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="small text-muted">Harian</span>
                            <span class="small fw-bold text-success">Rp {{ number_format($jukir->uji_petik, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="small text-muted">Bulanan</span>
                            <span class="small fw-bold text-success font-monospace">Rp {{ number_format($jukir->potensi_bulanan_upl, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Documents Card -->
            @if($jukir->document)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avtar avtar-m bg-light-info text-info me-3">
                            <i class="ti ti-file-text fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0">Dokumen Lampiran</h6>
                            <span class="text-muted small">File format: PDF/Image</span>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ asset('storage/' . $jukir->document) }}" target="_blank" class="btn btn-light-info border border-info border-opacity-25 pb-2">
                            <i class="ti ti-eye me-2"></i> Lihat Dokumen
                        </a>
                        <a href="{{ asset('storage/' . $jukir->document) }}" download class="btn btn-outline-secondary btn-sm">
                            <i class="ti ti-download me-2"></i> Download
                        </a>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Notes -->
            <div class="card border-0 shadow-sm mb-4">
                 <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-2">
                        <i class="ti ti-note me-2 text-warning fs-4"></i>
                        <h6 class="mb-0">Keterangan Internal</h6>
                    </div>
                    <p class="text-muted small mb-0 lh-base">{{ $jukir->ket_jukir ?: 'Tidak ada keterangan internal tambahan untuk jukir ini.' }}</p>
                 </div>
            </div>
        </div>
    </div>
</div>
</div>
