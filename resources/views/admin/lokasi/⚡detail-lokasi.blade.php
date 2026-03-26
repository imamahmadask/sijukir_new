<?php

use Livewire\Component;
use App\Models\Lokasi;

new class extends Component {
    public $lokasi;

    public function mount($id)
    {
        $this->lokasi = Lokasi::with(['area', 'kelurahan', 'korlap'])->findOrFail($id);
    }

    public function render()
    {
        return $this->view()->title('Detail Titik Parkir');
    }
};
?>

<div>
    <style>
        /* Modern Minimalist Design System for Detail Lokasi */
        .dl-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #eef2f6;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            margin-bottom: 1.25rem;
            overflow: hidden;
            transition: all 0.2s ease;
        }
        .dl-card-body { padding: 1.5rem; }
        .dl-section-title {
            font-size: 0.88rem;
            font-weight: 700;
            color: #1a202c;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
        }
        .dl-section-title i { font-size: 1.1rem; color: #4361ee; margin-right: 8px; }

        .dl-label {
            display: block;
            font-size: 0.65rem;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }
        .dl-value {
            display: block;
            font-size: 0.9rem;
            font-weight: 500;
            color: #334155;
            word-break: break-word;
        }
        .dl-value.bold { font-weight: 700; color: #0f172a; }

        .dl-stat-box {
            background: #f8fafc;
            border-radius: 10px;
            padding: 1rem;
            border: 1px solid #f1f5f9;
        }

        .dl-badge-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .dl-header {
            margin-bottom: 1.5rem;
            background: #fff;
            padding: 1.25rem 1.5rem;
            border-radius: 12px;
            border: 1px solid #eef2f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        }

        .dl-img-container {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 1rem;
            background: #f1f5f9;
        }
        .dl-img-container img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            display: block;
            transition: transform 0.4s ease;
        }
        .dl-img-container img:hover { transform: scale(1.02); }

        .dl-map-container {
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #eef2f6;
            height: 300px;
        }

        /* Responsiveness */
        @media (max-width: 768px) {
            .dl-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
            .col-6 { width: 100%; }
        }
    </style>

    {{-- ===== HEADER ===== --}}
    <div class="dl-header">
        <div>
            <h5 class="mb-1 fw-bold text-dark">Detail Titik Parkir</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.78rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('lokasi.index') }}" class="text-decoration-none">Titik Parkir</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detail</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('lokasi.edit', $lokasi->id) }}" class="btn btn-warning shadow-sm" style="font-size:0.8rem; padding:8px 16px;">
                <i class="ti ti-edit me-1"></i> Edit Data
            </a>
            <a href="{{ route('lokasi.index') }}" class="btn btn-light border shadow-sm" style="font-size:0.8rem; padding:8px 16px;">
                <i class="ti ti-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    {{-- ===== CONTENT ===== --}}
    <div class="row g-3">
        
        {{-- LEFT COLUMN: Details --}}
        <div class="col-12 col-xl-8 order-2 order-xl-1">
            <div class="row g-3">

                {{-- Informasi Utama --}}
                <div class="col-12">
                    <div class="dl-card">
                        <div class="dl-card-body">
                            <div class="dl-section-title"><i class="ti ti-map-2"></i> Informasi Utama & Alamat</div>
                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <span class="dl-label">Nama Titik Parkir</span>
                                    <span class="dl-value bold" style="font-size: 1.1rem;">{{ $lokasi->titik_parkir }}</span>
                                </div>
                                <div class="col-12 col-md-6">
                                    <span class="dl-label">Lokasi (Alamat)</span>
                                    <span class="dl-value">{{ $lokasi->lokasi_parkir }}</span>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <span class="dl-label">Area (Kecamatan)</span>
                                    <span class="dl-value">{{ $lokasi->area->Kecamatan ?? '-' }}</span>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <span class="dl-label">Kelurahan</span>
                                    <span class="dl-value">{{ $lokasi->kelurahan->kelurahan ?? '-' }}</span>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <span class="dl-label">Tanggal Registrasi</span>
                                    <span class="dl-value">{{ \Carbon\Carbon::parse($lokasi->tgl_registrasi)->translatedFormat('d M Y') }}</span>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <span class="dl-label">Petugas Korlap</span>
                                    <span class="dl-value fw-bold text-primary">{{ $lokasi->korlap->nama ?? '-' }}</span>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <span class="dl-label">Status</span>
                                    <span class="dl-badge-status bg-light-{{ $lokasi->is_active === 1 ? 'success' : 'danger' }} text-{{ $lokasi->is_active === 1 ? 'success' : 'danger' }}">
                                        {{ $lokasi->is_active === 1 ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Detail Teknis --}}
                <div class="col-12">
                    <div class="dl-card">
                        <div class="dl-card-body">
                            <div class="dl-section-title"><i class="ti ti-tool"></i> Detail Teknis & Operasional</div>
                            <div class="row g-4">
                                <div class="col-6 col-md-3">
                                    <span class="dl-label">Jenis Lokasi</span>
                                    <span class="dl-value">{{ $lokasi->jenis_lokasi ?? '-' }}</span>
                                </div>
                                <div class="col-6 col-md-3">
                                    <span class="dl-label">Kategori</span>
                                    <span class="dl-value">{{ $lokasi->kategori ?? '-' }}</span>
                                </div>
                                <div class="col-6 col-md-3">
                                    <span class="dl-label">Sisi Parkir</span>
                                    <span class="dl-value">{{ $lokasi->sisi ?? '-' }}</span>
                                </div>
                                <div class="col-6 col-md-3">
                                    <span class="dl-label">Panjang/Luas</span>
                                    <span class="dl-value">{{ $lokasi->panjang_luas ?? '-' }}</span>
                                </div>
                                <div class="col-6 col-md-3">
                                    <span class="dl-label">Waktu Pelayanan</span>
                                    <span class="dl-value"><i class="ti ti-clock me-1 opacity-50"></i> {{ $lokasi->waktu_pelayanan ?? '-' }}</span>
                                </div>
                                <div class="col-6 col-md-3">
                                    <span class="dl-label">Hari Operasional</span>
                                    <span class="dl-value"><i class="ti ti-calendar-event me-1 opacity-50"></i> {{ $lokasi->hari_buka ?? '-' }} Hari / Minggu</span>
                                </div>
                                <div class="col-12 col-md-6">
                                    <span class="dl-label">Keterangan Tambahan</span>
                                    <span class="dl-value text-muted" style="font-size:0.85rem;">{{ $lokasi->keterangan ?? 'Tidak ada keterangan tambahan.' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Legalitas & Koordinat --}}
                <div class="col-12">
                    <div class="dl-card">
                        <div class="dl-card-body">
                            <div class="dl-section-title"><i class="ti ti-file-certificate"></i> Legalitas & Administrasi</div>
                            <div class="row g-4">
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="dl-stat-box">
                                        <span class="dl-label">Dasar Ketetapan</span>
                                        <span class="dl-value fw-bold text-dark">{{ $lokasi->dasar_ketetapan ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="dl-stat-box">
                                        <span class="dl-label">Nomor Ketetapan</span>
                                        <span class="dl-value">{{ $lokasi->no_ketetapan ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="dl-stat-box">
                                        <span class="dl-label">Tanggal Penetapan</span>
                                        <span class="dl-value">{{ $lokasi->tgl_ketetapan ? \Carbon\Carbon::parse($lokasi->tgl_ketetapan)->translatedFormat('d M Y') : '-' }}</span>
                                    </div>
                                </div>                                
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- RIGHT COLUMN: Photo + Map --}}
        <div class="col-12 col-xl-4 order-1 order-xl-2">
            
            {{-- Foto Lokasi --}}
            <div class="dl-card">
                <div class="dl-card-body">
                    <div class="dl-section-title"><i class="ti ti-camera"></i> Foto Lokasi</div>
                    <div class="dl-img-container">
                        @if($lokasi->gambar)
                            <a href="{{ asset('storage/' . $lokasi->gambar) }}" target="_blank">
                                <img src="{{ asset('storage/' . $lokasi->gambar) }}" 
                                     onerror="this.onerror=null; this.src='https://placehold.co/600x400?text=Foto+Tidak+Ditemukan';"
                                     alt="Foto {{ $lokasi->titik_parkir }}">
                            </a>
                        @else
                            <div class="d-flex flex-column align-items-center justify-content-center h-100 bg-light">
                                <i class="ti ti-photo-off text-muted mb-2" style="font-size: 3rem;"></i>
                                <span class="text-muted small">Belum ada foto lokasi</span>
                            </div>
                        @endif
                    </div>
                    <div class="text-center">
                        <small class="text-muted" style="font-size: 0.72rem;">
                            <i class="ti ti-info-circle me-1"></i> Klik gambar untuk memvisualisasikan dalam ukuran penuh.
                        </small>
                    </div>
                </div>
            </div>

            {{-- Peta Lokasi --}}
            @if($lokasi->kord_lat && $lokasi->kord_long)
            <div class="dl-card">
                <div class="dl-card-body">
                    <div class="dl-section-title"><i class="ti ti-map-pin"></i> Peta Lokasi</div>
                    <div class="dl-map-container shadow-sm mb-3">
                        <iframe 
                            width="100%" 
                            height="100%" 
                            frameborder="0" 
                            style="border:0;"
                            src="https://maps.google.com/maps?q={{ $lokasi->kord_lat }},{{ $lokasi->kord_long }}&hl=id&z=15&output=embed"
                            allowfullscreen>
                        </iframe>
                    </div>
                    <a href="https://maps.google.com/?q={{ $lokasi->kord_lat }},{{ $lokasi->kord_long }}" target="_blank" class="btn btn-primary w-100 shadow-sm" style="font-size:0.85rem;">
                        <i class="ti ti-navigation me-1"></i> Petunjuk Arah
                    </a>
                </div>
            </div>
            @endif

        </div>

    </div>
</div>

