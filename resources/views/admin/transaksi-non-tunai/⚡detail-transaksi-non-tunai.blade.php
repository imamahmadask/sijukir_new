<?php

use Livewire\Component;
use App\Models\TransNonTunai;
use App\Models\Merchant;
use Livewire\Attributes\On;

new class extends Component {
    public $merchant;
    public $transactions = [];
    public $startDate;
    public $endDate;
    public $totalAmount = 0;

    #[On('show-non-tunai-detail')]
    public function show($merchantId, $startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->merchant = Merchant::where('id', $merchantId)->first();
        
        // If merchant not found in merchants table, fallback to first transaction's name
        $query = TransNonTunai::where('merchant_id', $merchantId)
            ->when($this->startDate, fn($q) => $q->whereDate('tgl_transaksi', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('tgl_transaksi', '<=', $this->endDate))
            ->orderBy('tgl_transaksi', 'desc');

        $this->transactions = $query->get();
        $this->totalAmount = $this->transactions->sum('total_nilai');
        
        if (!$this->merchant && $this->transactions->count() > 0) {
            $this->merchant = (object)[
                'id' => $merchantId,
                'merchant_name' => $this->transactions->first()->merchant_name
            ];
        }

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
                    <h5 class="modal-title text-white" id="modalDetailNonTunaiLabel">Rincian Transaksi Merchant</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    @if($merchant)
                        <div class="p-4 bg-light border-bottom">
                            <div class="row align-items-center">
                                <div class="col-md-7">
                                    <h4 class="mb-1 fw-bold text-dark">{{ $merchant->merchant_name ?? 'Unknown Merchant' }}</h4>
                                    <p class="text-muted mb-0 small"><i class="ti ti-id me-1"></i> ID: {{ $merchant->id ?? '-' }}</p>
                                    @if($startDate && $endDate)
                                        <p class="text-muted mb-0 small mt-1">
                                            <i class="ti ti-calendar me-1"></i> {{ date('d/m/Y', strtotime($startDate)) }} - {{ date('d/m/Y', strtotime($endDate)) }}
                                        </p>
                                    @endif
                                </div>
                                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                                    <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Total Penerimaan</small>
                                    <h3 class="fw-bold text-primary mb-0">Rp {{ number_format($totalAmount, 0, ',', '.') }}</h3>
                                    <span class="badge bg-light-primary text-primary px-2">{{ count($transactions) }} Transaksi</span>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-white sticky-top shadow-sm" style="z-index: 1;">
                                    <tr>
                                        <th class="ps-4 py-3 small text-uppercase text-muted border-0">Tanggal</th>
                                        <th class="py-3 small text-uppercase text-muted border-0">Issuer</th>
                                        <th class="py-3 small text-uppercase text-muted border-0">Settlement</th>
                                        <th class="pe-4 py-3 small text-uppercase text-muted text-end border-0">Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $t)
                                        <tr>
                                            <td class="ps-4">
                                                <span class="fw-medium d-block text-dark">{{ date('d M Y', strtotime($t->tgl_transaksi)) }}</span>
                                                <small class="text-muted" style="font-size: 0.7rem;">{{ date('H:i:s', strtotime($t->tgl_transaksi)) }}</small>
                                            </td>
                                            <td>{{ $t->issuer_name }}</td>
                                            <td><small class="text-muted">{{ $t->settlement ?? '-' }}</small></td>
                                            <td class="pe-4 text-end fw-bold text-dark">Rp {{ number_format($t->total_nilai, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">Tidak ada transaksi dalam periode ini</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
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
                    @if($merchant && count($transactions) > 0)
                        <button type="button" class="btn btn-primary shadow-sm" onclick="window.print()">
                            <i class="ti ti-printer me-1"></i> Cetak Rincian
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
