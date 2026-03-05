<?php

use Livewire\Component;
use App\Models\Merchant;
use App\Models\Area;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;

new class extends Component {
    use WithFileUploads;

    public $id = '';
    public $merchant_name = '';
    public $vendor = 'BNTBS';
    public $nmid = '';
    public $no_rekening = '';
    public $tgl_terdaftar = '';
    public $qris;
    public $area_id = '';

    public $areas = [];

    public function mount()
    {
        $this->areas = Area::all();
        $this->tgl_terdaftar = date('Y-m-d');
    }

    #[On('open-create-merchant')]
    public function open()
    {
        $this->reset(['id', 'merchant_name', 'nmid', 'no_rekening', 'qris', 'area_id']);
        $this->tgl_terdaftar = date('Y-m-d');
        $this->vendor = 'BNTBS';
        $this->dispatch('open-modal', name: 'modalCreate');
    }

    public function save()
    {
        $this->validate([
            'id'            => 'required|unique:merchant,id',
            'merchant_name' => 'required|string|max:255',
            'vendor'        => 'required|string|max:255',
            'nmid'          => 'required|string|max:255',
            'no_rekening'   => 'required|numeric',
            'tgl_terdaftar' => 'required|date',
            'qris'          => 'nullable|mimes:pdf|max:2048',
            'area_id'       => 'required|exists:areas,id',
        ]);

        $qrisPath = null;
        if ($this->qris) {
            $nama_qris = $this->merchant_name . '_' . time() . '.' . $this->qris->extension();
            $qrisPath = $this->qris->storeAs("qris", $nama_qris, 'public');
        }
        
        Merchant::create([
            'id'            => $this->id,
            'merchant_name' => $this->merchant_name,
            'vendor'        => $this->vendor,
            'nmid'          => $this->nmid,
            'no_rekening'   => $this->no_rekening,
            'tgl_terdaftar' => $this->tgl_terdaftar,
            'qris'          => $qrisPath,
            'area_id'       => $this->area_id ?: null,
        ]);

        $this->dispatch('refresh-merchants')->to('admin::merchant.index-merchant');
        $this->dispatch('close-modal', name: 'modalCreate');
        session()->flash('success', 'Merchant berhasil ditambahkan.');
    }

    public function render()
    {
        return view('admin.merchant.⚡create-merchant');
    }
};
?>

<div>
    <!-- Modal Create -->
    <div class="modal fade" id="modalCreate" tabindex="-1" aria-labelledby="modalCreateLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="modalCreateLabel">Tambah Merchant Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">ID Merchant</label>
                                <input type="text" class="form-control" wire:model="id" placeholder="Contoh: MERCH-001">
                                @error('id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">NMID</label>
                                <input type="text" class="form-control" wire:model="nmid" placeholder="NMID QRIS">
                                @error('nmid') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">Nama Merchant</label>
                                <input type="text" class="form-control" wire:model="merchant_name" placeholder="Nama Lengkap Merchant">
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
                                <input type="text" class="form-control" wire:model="no_rekening" placeholder="Hanya Angka">
                                @error('no_rekening') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">Tanggal Terdaftar</label>
                                <input type="date" class="form-control" wire:model="tgl_terdaftar">
                                @error('tgl_terdaftar') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">QRIS Merchant (PDF)</label>
                                <input type="file" class="form-control" wire:model="qris" accept="application/pdf">
                                <small class="text-muted">Format: PDF, Max: 2MB</small>
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
                            <i class="ti ti-save me-1"></i> Simpan Merchant
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
