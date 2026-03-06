<?php

use Livewire\Component;
use App\Models\TransTunai;
use Livewire\Attributes\On;

new class extends Component {
    public $transaction;

    #[On('show-transaction-detail')]
    public function show($id)
    {
        $this->transaction = TransTunai::with(['jukir', 'area'])->findOrFail($id);
        $this->dispatch('open-modal', name: 'modalDetailTransaction');
    }

    public function render()
    {
        return view('admin.transaksi-tunai.⚡detail-transaksi-tunai');
    }
};
?>

<div>
    <div class="modal fade" id="modalDetailTransaction" tabindex="-1" aria-labelledby="modalDetailTransactionLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title text-white" id="modalDetailTransactionLabel">Detail Transaksi Tunai</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    @if($transaction)
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless mb-0">
                                <tbody class="text-dark">
                                    <tr class="border-bottom">
                                        <th class="ps-4 py-3" width="180">ID Transaksi</th>
                                        <td class="pe-4 py-3 text-end"><span class="badge bg-light-primary text-primary px-2 fw-bold">{{ $transaction->id }}</span></td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <th class="ps-4 py-3">Tanggal</th>
                                        <td class="pe-4 py-3 text-end fw-bold">{{ date('d F Y', strtotime($transaction->tgl_transaksi)) }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <th class="ps-4 py-3">Type</th>
                                        <td class="pe-4 py-3 text-end"><span class="badge bg-light-info text-info">{{ $transaction->type ?? 'Normal' }}</span></td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <th class="ps-4 py-3">Juru Parkir</th>
                                        <td class="pe-4 py-3 text-end fw-bold text-primary">{{ $transaction->jukir->nama_jukir ?? '-' }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <th class="ps-4 py-3">Titik Parkir</th>
                                        <td class="pe-4 py-3 text-end">{{ $transaction->jukir->lokasi->titik_parkir ?? '-' }} <br>({{ $transaction->jukir->lokasi->lokasi_parkir ?? '-' }})</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <th class="ps-4 py-3">Area (Kecamatan)</th>
                                        <td class="pe-4 py-3 text-end">{{ $transaction->area->Kecamatan ?? '-' }}</td>
                                    </tr>  
                                    @if($transaction->jukir)
                                        <tr class="border-bottom bg-light">
                                            <th class="ps-5 py-2 small text-muted">Tgl Perjanjian</th>
                                            <td class="pe-4 py-2 text-end small">{{ $transaction->jukir->tgl_perjanjian ?? '-' }}</td>
                                        </tr>
                                        <tr class="border-bottom bg-light">
                                            <th class="ps-5 py-2 small text-muted">Tgl Terbit QR</th>
                                            <td class="pe-4 py-2 text-end small">{{ $transaction->jukir->tgl_terbit_qr ?? '-' }}</td>
                                        </tr>
                                        <tr class="border-bottom bg-light">
                                            <th class="ps-5 py-2 small text-muted">Potensi Harian</th>
                                            <td class="pe-4 py-2 text-end small">Rp {{ number_format($transaction->jukir->potensi_harian ?? 0, 0, ',', '.') }}</td>
                                        </tr>
                                    @endif                                    
                                    <tr class="border-bottom">
                                        <th class="ps-4 py-3">No. Kwitansi</th>
                                        <td class="pe-4 py-3 text-end fw-bold">{{ $transaction->no_kwitansi }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <th class="ps-4 py-3">Jumlah Transaksi</th>
                                        <td class="pe-4 py-3 text-end fw-bold text-success fs-5">Rp {{ number_format($transaction->jumlah_transaksi, 0, ',', '.') }}</td>
                                    </tr>                                                                                                         
                                    <tr class="border-bottom">
                                        <th class="ps-4 py-3">Keterangan</th>
                                        <td class="pe-4 py-3 text-end italic">{{ $transaction->keterangan ?? '-' }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <th class="ps-4 py-3">Dokumen</th>
                                        <td class="pe-4 py-3 text-end">
                                            @if($transaction->dokumen)
                                                <a href="{{ asset('storage/'.$transaction->dokumen) }}" class="btn btn-sm btn-light-primary" target="_blank">
                                                    <i class="ti ti-download me-1"></i> Klik untuk Lihat Dokumen
                                                </a>
                                            @else
                                                <span class="text-muted small">Tidak ada dokumen</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary shadow-sm px-4" data-bs-dismiss="modal">Tutup</button>
                    @if($transaction)
                        <button type="button" class="btn btn-warning shadow-sm"
                            wire:click="$parent.editTransaction('{{ $transaction->id }}')" 
                            data-bs-dismiss="modal">
                            <i class="ti ti-edit me-1"></i> Edit Transaksi
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
