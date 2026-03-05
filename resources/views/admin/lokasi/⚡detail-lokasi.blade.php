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
    <!-- Header -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Titik Parkir</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('lokasi.index') }}">Titik Parkir</a></li>
                        <li class="breadcrumb-item" aria-current="page">Detail</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Detail Informasi Titik Parkir</h5>
                    <div>
                        <a href="{{ route('lokasi.edit', $lokasi->id) }}" class="btn btn-warning btn-sm me-2">
                            <i class="ti ti-edit"></i> Edit
                        </a>
                        <a href="{{ route('lokasi.index') }}" class="btn btn-secondary btn-sm">
                            <i class="ti ti-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-8">
                            <table class="table table-borderless table-striped">
                                <tbody>
                                    <!-- Informasi Utama -->
                                    <tr>
                                        <th style="width: 30%;">Nama Titik Parkir</th>
                                        <td>: {{ $lokasi->titik_parkir }}</td>
                                    </tr>
                                    <tr>
                                        <th>Lokasi (Alamat)</th>
                                        <td>: {{ $lokasi->lokasi_parkir }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tgl Registrasi</th>
                                        <td>: {{ \Carbon\Carbon::parse($lokasi->tgl_registrasi)->translatedFormat('d F Y') }}</td>
                                    </tr>

                                    <!-- Wilayah & Pengelola -->
                                    <tr>
                                        <td colspan="2"><h6 class="text-primary mt-3 mb-1">Wilayah & Pengelola</h6></td>
                                    </tr>
                                    <tr>
                                        <th>Area (Kecamatan)</th>
                                        <td>: {{ $lokasi->area->Kecamatan ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Kelurahan</th>
                                        <td>: {{ $lokasi->kelurahan->kelurahan ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Korlap</th>
                                        <td>: {{ $lokasi->korlap->nama ?? '-' }}</td>
                                    </tr>

                                    <!-- Detail Teknis -->
                                    <tr>
                                        <td colspan="2"><h6 class="text-primary mt-3 mb-1">Detail Teknis</h6></td>
                                    </tr>
                                    <tr>
                                        <th>Jenis Lokasi</th>
                                        <td>: {{ $lokasi->jenis_lokasi }}</td>
                                    </tr>
                                    <tr>
                                        <th>Kategori</th>
                                        <td>: {{ $lokasi->kategori }}</td>
                                    </tr>
                                    <tr>
                                        <th>Sisi</th>
                                        <td>: {{ $lokasi->sisi ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Waktu Pelayanan</th>
                                        <td>: {{ $lokasi->waktu_pelayanan }}</td>
                                    </tr>
                                    <tr>
                                        <th>Hari Buka</th>
                                        <td>: {{ $lokasi->hari_buka ? $lokasi->hari_buka . ' Hari' : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Panjang/Luas</th>
                                        <td>: {{ $lokasi->panjang_luas ?? '-' }}</td>
                                    </tr>

                                    <!-- Legalitas & Koordinat -->
                                    <tr>
                                        <td colspan="2"><h6 class="text-primary mt-3 mb-1">Legalitas & Koordinat</h6></td>
                                    </tr>
                                    <tr>
                                        <th>Dasar Ketetapan</th>
                                        <td>: {{ $lokasi->dasar_ketetapan ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>No. Ketetapan</th>
                                        <td>: {{ $lokasi->no_ketetapan ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tgl Ketetapan</th>
                                        <td>: {{ $lokasi->tgl_ketetapan ? \Carbon\Carbon::parse($lokasi->tgl_ketetapan)->translatedFormat('d F Y') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Koordinat</th>
                                        <td>: {{ $lokasi->kord_lat }}, {{ $lokasi->kord_long }}</td>
                                    </tr>
                                    <tr>
                                        <th>Google Maps Link</th>
                                        <td>: 
                                            @if($lokasi->google_maps)
                                                <a href="{{ $lokasi->google_maps }}" target="_blank">{{ $lokasi->google_maps }}</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>

                                    <!-- Status & Informasi Tambahan -->
                                    <tr>
                                        <td colspan="2"><h6 class="text-primary mt-3 mb-1">Informasi Tambahan</h6></td>
                                    </tr>
                                    <tr>
                                        <th>Pendaftaran</th>
                                        <td>: {{ $lokasi->pendaftaran ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            : <span class="badge bg-light-{{ $lokasi->status === 'Aktif' ? 'success' : 'danger' }}">
                                                {{ $lokasi->status ?? 'Aktif' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Keterangan</th>
                                        <td>: {{ $lokasi->keterangan ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Kolom Kanan (Gambar/Foto) -->
                        <div class="col-md-4">
                            <div class="card bg-light border-0">
                                <div class="card-body text-center p-3">
                                    <h6 class="mb-3 text-start text-primary">Foto Lokasi</h6>
                                    @if($lokasi->gambar)
                                        <a href="{{ asset('storage/' . $lokasi->gambar) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $lokasi->gambar) }}" alt="Foto {{ $lokasi->titik_parkir }}" class="img-fluid rounded shadow-sm" style="max-height: 400px; width: 100%; object-fit: cover;">
                                        </a>
                                        <small class="d-block mt-2 text-muted">Klik gambar untuk memperbesar</small>
                                    @else
                                        <div class="d-flex flex-column align-items-center justify-content-center bg-white rounded shadow-sm" style="height: 300px;">
                                            <i class="ti ti-photo text-muted mb-2" style="font-size: 3rem;"></i>
                                            <span class="text-muted">Belum ada foto lokasi</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if($lokasi->kord_lat && $lokasi->kord_long)
                            <div class="card bg-light border-0 mt-3 align-items-center">
                                <div class="card-body w-100 p-3">
                                    <h6 class="mb-3 text-start text-primary">Peta Lokasi</h6>
                                    <div class="rounded shadow-sm overflow-hidden" style="height: 300px; width: 100%;">
                                        <iframe 
                                            width="100%" 
                                            height="100%" 
                                            frameborder="0" 
                                            style="border:0;"
                                            src="https://maps.google.com/maps?q={{ $lokasi->kord_lat }},{{ $lokasi->kord_long }}&hl=id&z=15&output=embed"
                                            allowfullscreen>
                                        </iframe>
                                    </div>
                                    <div class="text-end mt-2">
                                        <a href="https://maps.google.com/?q={{ $lokasi->kord_lat }},{{ $lokasi->kord_long }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="ti ti-map-pin"></i> Buka di Google Maps
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
