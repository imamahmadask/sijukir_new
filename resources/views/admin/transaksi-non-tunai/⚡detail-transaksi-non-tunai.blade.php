<?php

use Livewire\Component;
use App\Models\TransNonTunai;
use Livewire\Attributes\On;

new class extends Component {
    public $transaction;

    #[On('show-non-tunai-detail')]
    public function show($id)
    {
        $this->transaction = TransNonTunai::with(['merchant', 'area'])->find($id);
        $this->dispatch('open-modal', name: 'modalDetailNonTunai');
    }

    public function render()
    {
        return view('admin.transaksi-non-tunai.⚡detail-transaksi-non-tunai');
    }
};
?>

<div>
    <div class="modal fade" id="modalDetailNonTunai" tabindex="-1" aria-labelledby="modalDetailNonTunaiLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title text-white" id="modalDetailNonTunaiLabel">Detail Transaksi Non-Tunai</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    @if($transaction)
                        <div class="p-4 bg-light border-bottom">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h4 class="mb-1 fw-bold text-dark">{{ $transaction->merchant_name }}</h4>
                                    <p class="text-muted mb-0"><i class="ti ti-id me-1"></i> Merchant ID: {{ $transaction->merchant_id }}</p>
                                </div>
                                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                    <h3 class="fw-bold text-primary mb-0">Rp {{ number_format($transaction->total_nilai, 0, ',', '.') }}</h3>
                                    <span class="badge bg-light-success text-success px-2">{{ $transaction->status }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Informasi Transaksi</label>
                                    <div class="card bg-light border-0 p-3 shadow-none">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Tanggal:</span>
                                            <span class="fw-bold text-dark">{{ date('d F Y', strtotime($transaction->tgl_transaksi)) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Periode:</span>
                                            <span class="fw-bold text-dark">{{ $transaction->bulan }} / {{ $transaction->tahun }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Issuer:</span>
                                            <span class="fw-bold text-dark">{{ $transaction->issuer_name }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Settlement:</span>
                                            <span class="fw-bold text-dark">{{ $transaction->settlement ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Data Wilayah</label>
                                    <div class="card bg-light border-0 p-3 shadow-none">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Kecamatan:</span>
                                            <span class="fw-bold text-dark">{{ $transaction->kecamatan ?? ($transaction->area->Kecamatan ?? '-') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Area ID:</span>
                                            <span class="fw-bold text-dark">{{ $transaction->area_id ?? '-' }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Sender:</span>
                                            <span class="fw-bold text-dark">{{ $transaction->sender_name ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mt-4">
                                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Log & Sistem</label>
                                    <div class="card bg-light border-0 p-3 shadow-none">
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <span class="text-muted d-block small">Nama File Import:</span>
                                                <span class="fw-medium text-dark">{{ $transaction->filename ?? '-' }}</span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <span class="text-muted d-block small">Syslog:</span>
                                                <span class="fw-medium text-dark">{{ $transaction->syslog ?? '-' }}</span>
                                            </div>
                                            <div class="col-12">
                                                <span class="text-muted d-block small">Info Tambahan:</span>
                                                <span class="fw-medium text-dark">{{ $transaction->info ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="p-5 text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Tutup</button>
                    @if($transaction)
                        <button type="button" class="btn btn-primary shadow-sm" onclick="window.print()">
                            <i class="ti ti-printer me-1"></i> Cetak Detail
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
