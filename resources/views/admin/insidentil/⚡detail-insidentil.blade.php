<?php

use Livewire\Component;
use App\Models\Insidentil;
use Livewire\Attributes\On;

new class extends Component {
    public $insidentil;

    #[On('show-insidentil-detail')]
    public function openModal($id)
    {
        $this->insidentil = Insidentil::findOrFail($id);
        $this->dispatch('open-modal', name: 'modal-detail-insidentil');
    }
};
?>

<div class="modal fade" id="modal-detail-insidentil" tabindex="-1" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Insidentil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                @if($insidentil)
                    <h6 class="text-primary border-bottom pb-2 fw-bold mb-3">Informasi Pendaftaran</h6>
                    <table class="table table-borderless table-sm mb-4">
                        <tr><th width="35%" class="text-muted">No Surat</th><td class="fw-bold">{{ $insidentil->no_surat ?? '-' }}</td></tr>
                        <tr><th class="text-muted">Tgl Pendaftaran</th><td class="fw-bold">{{ $insidentil->tgl_pendaftaran ? \Carbon\Carbon::parse($insidentil->tgl_pendaftaran)->format('d F Y') : '-' }}</td></tr>
                    </table>

                    <h6 class="text-primary border-bottom pb-2 fw-bold mb-3">Data Pemohon</h6>
                    <table class="table table-borderless table-sm mb-4">
                        <tr><th width="35%" class="text-muted">Nama Pengelola</th><td class="fw-bold">{{ $insidentil->nama ?? '-' }}</td></tr>
                        <tr><th class="text-muted">NIK</th><td>{{ $insidentil->nik ?? '-' }}</td></tr>
                        <tr><th class="text-muted">Alamat</th><td>{{ $insidentil->alamat ?? '-' }}</td></tr>
                        <tr><th class="text-muted">Tempat, Tgl Lahir</th>
                            <td>{{ $insidentil->tempat_lahir ?? '-' }}, {{ $insidentil->tgl_lahir ? \Carbon\Carbon::parse($insidentil->tgl_lahir)->format('d F Y') : '-' }}</td>
                        </tr>
                        <tr><th class="text-muted">Jenis Kelamin</th><td>{{ $insidentil->jk ?? '-' }}</td></tr>
                        <tr><th class="text-muted">Agama</th><td>{{ $insidentil->agama ?? '-' }}</td></tr>
                        <tr><th class="text-muted">Pekerjaan</th><td>{{ $insidentil->pekerjaan ?? '-' }}</td></tr>
                        <tr><th class="text-muted">Telepon</th><td>{{ $insidentil->telepon ?? '-' }}</td></tr>
                    </table>

                    <h6 class="text-primary border-bottom pb-2 fw-bold mb-3">Data Perusahaan</h6>
                    <table class="table table-borderless table-sm mb-4">
                        <tr><th width="35%" class="text-muted">Nama Perusahaan</th><td class="fw-bold">{{ $insidentil->nama_perusahaan ?? '-' }}</td></tr>
                        <tr><th class="text-muted">Alamat Perusahaan</th><td>{{ $insidentil->alamat_perusahaan ?? '-' }}</td></tr>
                        <tr><th class="text-muted">Akta Perusahaan</th><td>{{ $insidentil->akta_perusahaan ?? '-' }}</td></tr>
                        <tr><th class="text-muted">NPWP Perusahaan</th><td>{{ $insidentil->npwp_perusahaan ?? '-' }}</td></tr>
                    </table>

                    <h6 class="text-primary border-bottom pb-2 fw-bold mb-3">Data Acara & Parkir</h6>
                    <table class="table table-borderless table-sm mb-4">
                        <tr><th width="35%" class="text-muted">Nama Acara</th><td class="fw-bold">{{ $insidentil->nama_acara ?? '-' }}</td></tr>
                        <tr><th class="text-muted">Lokasi Acara</th><td>{{ $insidentil->lokasi_acara ?? '-' }}</td></tr>
                        <tr><th class="text-muted">Waktu Acara</th><td>{{ $insidentil->waktu_acara ?? '-' }}</td></tr>
                        <tr><th class="text-muted">Lama Acara</th><td>{{ $insidentil->jumlah_hari ?? '-' }} Hari</td></tr>
                        <tr><th class="text-muted">Periode Acara</th>
                            <td>
                                {{ $insidentil->tgl_awal_acara ? \Carbon\Carbon::parse($insidentil->tgl_awal_acara)->format('d F Y') : '-' }} 
                                s/d 
                                {{ $insidentil->tgl_akhir_acara ? \Carbon\Carbon::parse($insidentil->tgl_akhir_acara)->format('d F Y') : '-' }}
                            </td>
                        </tr>
                        <tr><th class="text-muted">Lokasi Parkir</th><td>{{ $insidentil->lokasi_parkir ?? '-' }}</td></tr>
                        <tr><th class="text-muted">Luas Lokasi</th><td>{{ $insidentil->luas_lokasi ?? '-' }}</td></tr>
                        <tr><th class="text-muted">Kriteria Lokasi</th><td>{{ $insidentil->kriteria_lokasi ?? '-' }}</td></tr>
                        <tr><th class="text-muted">Jenis Izin</th><td>{{ $insidentil->jenis_izin ?? '-' }}</td></tr>
                    </table>

                    <h6 class="text-primary border-bottom pb-2 fw-bold mb-3">Potensi & Lainnya</h6>
                    <table class="table table-borderless table-sm mb-0">
                        <tr><th width="35%" class="text-muted">Potensi Roda 2</th><td>{{ $insidentil->r2 ?? '-' }}</td></tr>
                        <tr><th class="text-muted">Potensi Roda 4</th><td>{{ $insidentil->r4 ?? '-' }}</td></tr>
                        <tr><th class="text-muted">Total Potensi</th><td class="fw-bold">Rp. {{ number_format($insidentil->potensi ?? 0, 0, ',', '.') }}</td></tr>
                        <tr><th class="text-muted">Total Setoran</th><td class="fw-bold text-success">Rp. {{ number_format($insidentil->setoran ?? 0, 0, ',', '.') }}</td></tr>
                        <tr><th class="text-muted">Keterangan</th><td>{{ $insidentil->keterangan ?? '-' }}</td></tr>
                        <tr>
                            <th class="text-muted align-middle">Dokumen File</th>
                            <td>
                                @if($insidentil->dokumen)
                                    <a href="{{ asset('storage/' . $insidentil->dokumen) }}" target="_blank" class="btn btn-sm btn-light-primary">
                                        <i class="ti ti-download me-1"></i> Buka / Download Dokumen
                                    </a>
                                @else
                                    <span class="fst-italic text-muted">Tidak ada dokumen</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
