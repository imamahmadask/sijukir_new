<?php

use Livewire\Component;
use App\Models\ParkirBerlangganan;
use Livewire\Attributes\On;

new class extends Component {
    public $parkir_id;
    public $parkir;

    #[On('show-berlangganan-detail')]
    public function openModal($id)
    {
        $this->parkir_id = $id;
        $this->parkir = ParkirBerlangganan::find($id);
        $this->dispatch('open-modal', name: 'detailBerlanggananModal');
    }
};
?>

<div>
    <div class="modal fade" id="detailBerlanggananModal" tabindex="-1" aria-labelledby="detailBerlanggananModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailBerlanggananModalLabel">Detail Parkir Berlangganan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($parkir)
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Nomor</div>
                        <div class="col-md-8 fw-bold">{{ $parkir->nomor ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Status</div>
                        <div class="col-md-8 fw-bold">
                            @if($parkir->status)
                                <span class="badge bg-light-primary">{{ $parkir->status }}</span>
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Jenis Kendaraan</div>
                        <div class="col-md-8 fw-bold">{{ $parkir->jenis ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Nama Pemilik</div>
                        <div class="col-md-8 fw-bold">{{ $parkir->nama ?: $parkir->nama_pemilik }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">No Polisi</div>
                        <div class="col-md-8 fw-bold">{{ $parkir->no_pol ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Alamat</div>
                        <div class="col-md-8 fw-bold">{{ $parkir->alamat ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Jumlah</div>
                        <div class="col-md-8 fw-bold">Rp {{ number_format($parkir->jumlah, 0, ',', '.') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Masa Berlaku</div>
                        <div class="col-md-8 fw-bold">{{ $parkir->masa_berlaku ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Tanggal Kwitansi</div>
                        <div class="col-md-8 fw-bold">
                            {{ $parkir->tgl_dikeluarkan ? \Carbon\Carbon::parse($parkir->tgl_dikeluarkan)->format('d M Y') : '-' }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Periode Berlaku</div>
                        <div class="col-md-8 fw-bold">
                            @if($parkir->awal_berlaku && $parkir->akhir_berlaku)
                                {{ \Carbon\Carbon::parse($parkir->awal_berlaku)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($parkir->akhir_berlaku)->format('d M Y') }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Keterangan</div>
                        <div class="col-md-8 fw-bold">{{ $parkir->keterangan ?? '-' }}</div>
                    </div>
                    @else
                    <div class="text-center py-4 text-muted">
                        Sedang memuat data...
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    @if($parkir)
                    <button type="button" class="btn btn-warning" 
                        wire:click="$dispatch('open-edit-berlangganan', { id: {{ $parkir->id }} })"
                        data-bs-dismiss="modal">
                        <i class="ti ti-edit me-1"></i> Edit
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
