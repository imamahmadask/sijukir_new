<?php

use Livewire\Component;
use App\Models\ParkirBerlangganan;
use Livewire\Attributes\On;
use Carbon\Carbon;

new class extends Component {
    public $nomor;
    public $jenis;
    public $status;
    public $nama;
    public $no_pol;
    public $alamat;
    public $jumlah;
    public $masa_berlaku = '6 Bulan';
    public $awal_berlaku;
    public $akhir_berlaku;
    public $tgl_dikeluarkan;
    public $keterangan;

    public function rules()
    {
        return [
            'nomor' => 'required|string|max:255',
            'jenis' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'no_pol' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:1',
            'masa_berlaku' => 'required|string|max:255',
            'awal_berlaku' => 'required|date',
            'akhir_berlaku' => 'required|date|after_or_equal:awal_berlaku',
            'tgl_dikeluarkan' => 'required|date',
            'keterangan' => 'nullable|string',
        ];
    }

    public function updatedTglDikeluarkan($value)
    {
        if ($value && $this->jenis !== 'Lainnya') {
            $this->awal_berlaku = $value;
            $this->akhir_berlaku = Carbon::parse($value)->addMonths(6)->format('Y-m-d');
        }
    }

    public function updatedJenis($value)
    {
        if ($value === 'Lainnya') {
            $this->masa_berlaku = '0';
        } else {
            $this->masa_berlaku = '6 Bulan';
            if ($this->tgl_dikeluarkan) {
                $this->awal_berlaku = $this->tgl_dikeluarkan;
                $this->akhir_berlaku = Carbon::parse($this->tgl_dikeluarkan)->addMonths(6)->format('Y-m-d');
            }
        }
    }

    #[On('open-create-berlangganan')]
    public function openModal()
    {
        $this->reset([
            'nomor', 'jenis', 'status', 'nama', 'no_pol', 'alamat', 
            'jumlah', 'masa_berlaku', 'awal_berlaku', 
            'akhir_berlaku', 'tgl_dikeluarkan', 'keterangan'
        ]);
        $this->masa_berlaku = '6 Bulan';
        $this->resetValidation();
        $this->dispatch('open-modal', name: 'createBerlanggananModal');
    }

    public function save()
    {
        $this->validate();

        ParkirBerlangganan::create([
            'nomor' => $this->nomor,
            'jenis' => $this->jenis,
            'status' => $this->status,
            'nama' => $this->nama, 
            'no_pol' => $this->no_pol,
            'alamat' => $this->alamat,
            'jumlah' => $this->jumlah,
            'masa_berlaku' => $this->masa_berlaku,
            'awal_berlaku' => $this->awal_berlaku,
            'akhir_berlaku' => $this->akhir_berlaku,
            'tgl_dikeluarkan' => $this->tgl_dikeluarkan,
            'keterangan' => $this->keterangan,
        ]);

        $this->dispatch('close-modal', name: 'createBerlanggananModal');
        $this->dispatch('refresh-berlangganans')->to('admin::berlangganan.index-berlangganan');
        session()->flash('success', 'Data berhasil ditambahkan.');
    }
};
?>

<div>
    <div class="modal fade" id="createBerlanggananModal" tabindex="-1" aria-labelledby="createBerlanggananModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createBerlanggananModalLabel">Tambah Parkir Berlangganan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nomor <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nomor') is-invalid @enderror" wire:model="nomor" placeholder="Masukan Nomor">
                                @error('nomor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Jenis Kendaraan <span class="text-danger">*</span></label>
                                <select class="form-select @error('jenis') is-invalid @enderror" wire:model.live="jenis">
                                    <option value="">Pilih Jenis Kendaraan</option>
                                    <option value="Mobil">Mobil</option>
                                    <option value="Truck">Truck</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                                @error('jenis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" wire:model="status">
                                    <option value="">Pilih Status</option>
                                    <option value="Berkala">Berkala</option>
                                    <option value="Numpang Uji">Numpang Uji</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nama <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror" wire:model="nama" placeholder="Masukan Nama">
                                @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">No Polisi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('no_pol') is-invalid @enderror" wire:model="no_pol" placeholder="Misal: AB 1234 CD">
                                @error('no_pol') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Jumlah (Rp) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('jumlah') is-invalid @enderror" wire:model="jumlah" placeholder="Masukan Jumlah Biaya">
                                @error('jumlah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Alamat <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('alamat') is-invalid @enderror" wire:model="alamat" rows="2" placeholder="Masukan Alamat"></textarea>
                                @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Tanggal Kwitansi <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tgl_dikeluarkan') is-invalid @enderror" wire:model.live="tgl_dikeluarkan">
                                @error('tgl_dikeluarkan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Awal Berlaku <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('awal_berlaku') is-invalid @enderror" wire:model="awal_berlaku" {{ $jenis !== 'Lainnya' ? 'readonly' : '' }}>
                                @error('awal_berlaku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Akhir Berlaku <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('akhir_berlaku') is-invalid @enderror" wire:model="akhir_berlaku" {{ $jenis !== 'Lainnya' ? 'readonly' : '' }}>
                                @error('akhir_berlaku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Keterangan</label>
                                <textarea class="form-control @error('keterangan') is-invalid @enderror" wire:model="keterangan" rows="2" placeholder="Tambahkan Keterangan (Opsional)"></textarea>
                                @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
