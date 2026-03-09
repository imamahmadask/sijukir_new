<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Imports\TransNonTunaiImport;
use Maatwebsite\Excel\Facades\Excel;
use Livewire\Attributes\On;

new class extends Component {
    use WithFileUploads;

    public $file;
    public $isImporting = false;

    #[On('open-import-modal')]
    public function open()
    {
        $this->file = null;
        $this->isImporting = false;
        $this->dispatch('open-modal', name: 'modalImportNonTunai');
    }

    public function import()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        $this->isImporting = true;

        try {
            $filename = $this->file->getClientOriginalName();
            Excel::import(new TransNonTunaiImport($filename), $this->file);
            
            session()->flash('success', 'Data berhasil diimport.');
            $this->dispatch('refresh-transactions')->to('admin::transaksi-non-tunai.index-transaksi-non-tunai');
            $this->dispatch('close-modal', name: 'modalImportNonTunai');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengimport data: ' . $e->getMessage());
        } finally {
            $this->isImporting = false;
        }
    }

    public function render()
    {
        return view('admin.transaksi-non-tunai.⚡import-transaksi-non-tunai');
    }
};
?>

<div>
    <div class="modal fade" id="modalImportNonTunai" tabindex="-1" aria-labelledby="modalImportNonTunaiLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="modalImportNonTunaiLabel">Import Transaksi Non-Tunai</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="import">
                    <div class="modal-body">
                        @if (session('error'))
                            <div class="alert alert-danger border-0 shadow-sm" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="mb-4">
                            <label class="form-label text-dark fw-bold">Pilih File Excel/CSV</label>
                            <input type="file" class="form-control" wire:model="file" id="fileImport">
                            <div wire:loading wire:target="file" class="mt-2 small text-primary">
                                <span class="spinner-border spinner-border-sm me-1" role="status"></span> Mengunggah file...
                            </div>
                            <small class="text-muted d-block mt-2">Format yang didukung: .xlsx, .xls, .csv (Maksimal 10MB)</small>
                        </div>

                        <div class="card bg-light border-0 p-3 mb-0">
                            <h6 class="fw-bold mb-2 small text-uppercase">Petunjuk Format:</h6>
                            <ul class="small text-muted mb-0 ps-3">
                                <li>Gunakan baris pertama sebagai header</li>
                                <li>Kolom wajib: <code>merchant_id</code>, <code>total_nilai</code></li>
                                <li>Kolom opsional: <code>tgl_transaksi</code>, <code>merchant_name</code>, <code>issuer_name</code>, <code>bulan</code>, <code>tahun</code>, <code>area_id</code>, <code>status</code>, <code>sender_name</code>, <code>kecamatan</code>, <code>settlement</code></li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal" wire:loading.attr="disabled">Batal</button>
                        <button type="submit" class="btn btn-primary shadow-sm" wire:loading.attr="disabled" wire:target="import">
                            <span wire:loading wire:target="import" class="spinner-border spinner-border-sm me-1" role="status"></span>
                            <i class="ti ti-upload me-1" wire:loading.remove wire:target="import"></i> Mulai Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
