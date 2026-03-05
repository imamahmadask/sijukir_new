<?php

use Livewire\Component;
use App\Models\Merchant;
use Livewire\Attributes\On;

new class extends Component {
    public $merchant;

    #[On('show-merchant-detail')]
    public function show($id)
    {
        $this->merchant = Merchant::with(['area'])->findOrFail($id);
        $this->dispatch('open-modal', name: 'modalDetail');
    }

    public function render()
    {
        return view('admin.merchant.⚡detail-merchant');
    }
};
?>

<div>
    <!-- Modal Detail -->
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                @if($merchant)
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title text-white" id="modalDetailLabel">Detail Merchant: {{ $merchant->merchant_name }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="p-4 bg-light border-bottom">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                                        <i class="ti ti-building-store fs-1 text-primary"></i>
                                    </div>
                                </div>
                                <div class="col">
                                    <h4 class="mb-1 fw-bold">{{ $merchant->merchant_name }}</h4>
                                    <span class="text-muted">ID Merchant: {{ $merchant->id }} | Vendor: {{ $merchant->vendor }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="row g-4">
                                <div class="col-md-7">
                                    <h6 class="text-primary mb-3">Informasi Rekening</h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td width="150" class="text-muted">NMID</td>
                                            <td class="fw-bold">: {{ $merchant->nmid ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">No. Rekening</td>
                                            <td class="fw-bold">: {{ $merchant->no_rekening ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Tgl. Terdaftar</td>
                                            <td class="fw-bold">: {{ $merchant->tgl_terdaftar ? date('d F Y', strtotime($merchant->tgl_terdaftar)) : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Area</td>
                                            <td class="fw-bold">: {{ $merchant->area->nama_area ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-5 text-center">
                                    <h6 class="text-primary mb-3">QRIS Merchant (PDF)</h6>
                                    @if($merchant->qris)
                                        <div class="p-3 border rounded bg-light mb-2">
                                            <i class="ti ti-file-text fs-1 text-danger d-block mb-2"></i>
                                            <span class="small d-block text-truncate mb-2">{{ basename($merchant->qris) }}</span>
                                            <a href="{{ asset('storage/' . $merchant->qris) }}" target="_blank" class="btn btn-sm btn-primary w-100">
                                                <i class="ti ti-external-link me-1"></i> Buka PDF
                                            </a>
                                        </div>
                                    @else
                                        <div class="p-4 bg-light rounded text-muted">
                                            <i class="ti ti-qrcode fs-1 d-block mb-2"></i>
                                            Belum ada QRIS
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-warning" wire:click="$dispatch('edit-merchant-from-detail', { id: '{{ $merchant->id }}' })" data-bs-dismiss="modal">Edit Data</button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
