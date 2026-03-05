<?php

use Livewire\Component;
use App\Models\Merchant;
use App\Models\Area;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;

new class extends Component {
    use WithFileUploads;

    public $merchantId;
    public $id_merchant;
    public $merchant_name = '';
    public $vendor = '';
    public $nmid = '';
    public $no_rekening = '';
    public $tgl_terdaftar = '';
    public $qris;
    public $existingQris;
    public $area_id = '';

    public $areas = [];

    public function mount()
    {
        $this->areas = Area::all();
    }

    #[On('open-edit-merchant')]
    public function open($id)
    {
        $this->merchantId = $id;
        $merchant = Merchant::findOrFail($id);
        
        $this->id_merchant = $merchant->id;
        $this->merchant_name = $merchant->merchant_name;
        $this->vendor = $merchant->vendor;
        $this->nmid = $merchant->nmid;
        $this->no_rekening = $merchant->no_rekening;
        $this->tgl_terdaftar = $merchant->tgl_terdaftar;
        $this->existingQris = $merchant->qris;
        $this->area_id = $merchant->area_id;

        $this->dispatch('open-modal', name: 'modalEdit');
    }

    public function save()
    {
        $this->validate([
            'merchant_name' => 'required|string|max:255',
            'vendor'        => 'required|string|max:255',
            'nmid'          => 'required|string|max:255',
            'no_rekening'   => 'required|numeric',
            'tgl_terdaftar' => 'required|date',
            'qris'          => 'nullable|mimes:pdf|max:2048',
            'area_id'       => 'required|exists:areas,id',
        ]);

        $merchant = Merchant::findOrFail($this->merchantId);
        
        $data = [
            'merchant_name' => $this->merchant_name,
            'vendor'        => $this->vendor,
            'nmid'          => $this->nmid,
            'no_rekening'   => $this->no_rekening,
            'tgl_terdaftar' => $this->tgl_terdaftar,
            'area_id'       => $this->area_id ?: null,
        ];

        if ($this->qris) {
            $nama_qris = $this->merchant_name . '_' . time() . '.' . $this->qris->extension();
            $data['qris'] = $this->qris->storeAs("qris", $nama_qris, 'public');
        }

        $merchant->update($data);

        $this->dispatch('refresh-merchants')->to('admin::merchant.index-merchant');
        $this->dispatch('close-modal', name: 'modalEdit');
        session()->flash('success', 'Merchant berhasil diperbarui.');
    }

    public function render()
    {
        return view('admin.merchant.⚡edit-merchant');
    }
};
?>

<div>
    <!-- Modal Edit -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="modalEditLabel">Edit Merchant: {{ $merchant_name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">ID Merchant</label>
                                <input type="text" class="form-control bg-light" value="{{ $id_merchant }}" disabled>
                                <small class="text-muted">ID tidak dapat diubah</small>
                            </div>                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">NMID</label>
                                <input type="text" class="form-control" wire:model="nmid">
                                @error('nmid') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">Nama Merchant</label>
                                <input type="text" class="form-control" wire:model="merchant_name">
                                @error('merchant_name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">Vendor / Bank</label>
                                <select class="form-select" wire:model="vendor">
                                    <option value="BNTBS">Bank NTBS</option>
                                </select>
                                @error('vendor') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">No. Rekening</label>
                                <input type="text" class="form-control" wire:model="no_rekening">
                                @error('no_rekening') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">Tanggal Terdaftar</label>
                                <input type="date" class="form-control" wire:model="tgl_terdaftar">
                                @error('tgl_terdaftar') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">Update QRIS (PDF Only)</label>
                                <input type="file" class="form-control" wire:model="qris" accept="application/pdf">
                                @if($existingQris)
                                    <div class="mt-1 d-flex align-items-center">
                                        <i class="ti ti-file-text text-danger me-1"></i>
                                        <small class="text-muted">Current: {{ basename($existingQris) }}</small>
                                    </div>
                                @endif
                                @error('qris') <small class="text-danger d-block">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">Area (Kecamatan)</label>
                                <select class="form-select" wire:model.live="area_id">
                                    <option value="">-- Pilih Area --</option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id }}">{{ $area->Kecamatan }}</option>
                                    @endforeach
                                </select>
                                @error('area_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary shadow-sm" wire:loading.attr="disabled">
                            <i class="ti ti-device-floppy me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
