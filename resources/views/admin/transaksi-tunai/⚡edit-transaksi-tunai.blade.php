<?php

use Livewire\Component;
use App\Models\TransTunai;
use App\Models\Jukir;
use App\Models\Area;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;

new class extends Component {
    use WithFileUploads;

    public $id;
    public $tgl_transaksi = '';
    public $jumlah_transaksi = 0;
    public $no_kwitansi = '';
    public $jukir_id = '';
    public $area_id = '';
    public $selisih = 0;
    public $keterangan = '';
    public $type = '';
    public $dokumen;
    public $oldDokumen;

    public $jukirs = [];
    public $areas = [];

    public function mount()
    {
        $this->jukirs = Jukir::all();
        $this->areas = Area::all();
    }

    #[On('open-edit-transaction')]
    public function open($id)
    {
        $this->id = $id;
        $transaction = TransTunai::findOrFail($id);
        $this->tgl_transaksi = $transaction->tgl_transaksi;
        $this->jumlah_transaksi = $transaction->jumlah_transaksi;
        $this->no_kwitansi = $transaction->no_kwitansi;
        $this->jukir_id = $transaction->jukir_id;
        $this->area_id = $transaction->area_id;
        $this->selisih = $transaction->selisih;
        $this->keterangan = $transaction->keterangan;
        $this->type = $transaction->type;
        $this->oldDokumen = $transaction->dokumen;
        $this->dokumen = null;

        $this->dispatch('open-modal', name: 'modalEditTransaction');
    }

    public function update()
    {
        $this->validate([
            'tgl_transaksi'    => 'required|date',
            'jumlah_transaksi' => 'required|numeric',
            'no_kwitansi'      => 'required|string|max:250',
            'jukir_id'         => 'required',
            'area_id'          => 'required|exists:areas,id',
            'selisih'          => 'nullable|numeric',
            'keterangan'       => 'nullable|string|max:100',
            'type'             => 'required|string|max:50',
            'dokumen'          => 'nullable|file|max:2048',
        ]);

        $transaction = TransTunai::findOrFail($this->id);
        
        $dokumenPath = $this->oldDokumen;
        if ($this->dokumen) {
            $nama_dokumen = 'TUNAI_' . time() . '.' . $this->dokumen->extension();
            $dokumenPath = $this->dokumen->storeAs("transaksi/tunai", $nama_dokumen, 'public');
        }

        $transaction->update([
            'tgl_transaksi'    => $this->tgl_transaksi,
            'jumlah_transaksi' => $this->jumlah_transaksi,
            'no_kwitansi'      => $this->no_kwitansi,
            'jukir_id'         => $this->jukir_id,
            'area_id'          => $this->area_id,
            'selisih'          => $this->selisih ?: 0,
            'keterangan'       => $this->keterangan ?: '-',
            'type'             => $this->type,
            'dokumen'          => $dokumenPath,
        ]);

        $this->dispatch('refresh-transactions')->to('admin::transaksi-tunai.index-transaksi-tunai');
        $this->dispatch('close-modal', name: 'modalEditTransaction');
        session()->flash('success', 'Transaksi berhasil diupdate.');
    }

    public function render()
    {
        return view('admin.transaksi-tunai.⚡edit-transaksi-tunai');
    }
};
?>

<div>
    <div class="modal fade" id="modalEditTransaction" tabindex="-1" aria-labelledby="modalEditTransactionLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="modalEditTransactionLabel">Edit Transaksi Tunai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="update">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">Tanggal Transaksi</label>
                                <input type="date" class="form-control" wire:model="tgl_transaksi">
                                @error('tgl_transaksi') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">No. Kwitansi</label>
                                <input type="text" class="form-control" wire:model="no_kwitansi">
                                @error('no_kwitansi') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">Area</label>
                                <select class="form-select" wire:model.live="area_id">
                                    <option value="">-- Pilih Area --</option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id }}">{{ $area->Kecamatan }}</option>
                                    @endforeach
                                </select>
                                @error('area_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">Juru Parkir</label>
                                <select class="form-select" wire:model="jukir_id">
                                    <option value="">-- Pilih Jukir --</option>
                                    @foreach($jukirs as $jukir)
                                        <option value="{{ $jukir->id }}">{{ $jukir->nama_jukir }}</option>
                                    @endforeach
                                </select>
                                @error('jukir_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">Jumlah Transaksi (Rp)</label>
                                <input type="number" class="form-control" wire:model="jumlah_transaksi">
                                @error('jumlah_transaksi') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">Type Transaksi</label>
                                <select class="form-select" wire:model="type">
                                    <option value="Normal">Normal</option>
                                    <option value="Setoran">Setoran</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                                @error('type') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">Selisih (Rp)</label>
                                <input type="number" class="form-control" wire:model="selisih">
                                @error('selisih') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">Keterangan</label>
                                <input type="text" class="form-control" wire:model="keterangan">
                                @error('keterangan') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label text-dark fw-bold">Ubah Dokumen (Opsional)</label>
                                <input type="file" class="form-control" wire:model="dokumen">
                                @if($oldDokumen)
                                    <small class="text-success d-block mt-1">Dokumen saat ini: <a href="{{ asset('storage/'.$oldDokumen) }}" target="_blank">Lihat</a></small>
                                @endif
                                @error('dokumen') <small class="text-danger d-block">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning shadow-sm" wire:loading.attr="disabled">
                            <i class="ti ti-rotate me-1"></i> Update Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
