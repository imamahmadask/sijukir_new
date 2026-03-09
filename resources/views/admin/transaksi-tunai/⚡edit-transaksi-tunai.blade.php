<?php

use Livewire\Component;
use App\Models\TransTunai;
use App\Models\Jukir;
use App\Models\Area;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Carbon\Carbon;

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
    public $tgl_terbit_qr = '';
    public $potensi_harian = '';
    public $tgl_perjanjian = '';    
    public $jml_hari_kerja = '';
    public $hari_libur = [];

    public $jukirs = [];

    public function mount()
    {
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

        // Load jukirs based on type
        $this->updatedType($this->type);
        // Load jukir details (will populate helper properties)
        $this->updatedJukirId($this->jukir_id);

        // Re-set tgl_transaksi from database because updatedJukirId might have overwritten it with Jukir's QR date
        $this->tgl_transaksi = $transaction->tgl_transaksi;

        $this->dispatch('open-modal', name: 'modalEditTransaction');
    }

    public function updatedType($value){
        if($value == "Non-Migrasi"){            
            $this->jukirs = Jukir::where('status', 'Tunai')
                ->orderBy('nama_jukir', 'asc')
                ->get();

            $this->potensi_harian = '';
            $this->tgl_terbit_qr = '';
            $this->tgl_perjanjian = '';
            $this->jml_hari_kerja = '';

        }elseif($value == "Migrasi"){
            $this->jukirs = Jukir::where('status', 'Non-Tunai')
            // ->where('ket_jukir', 'Pending') // Commented out for edit mode since it might already be processed
            ->orderBy('merchant_id', 'asc')
            ->get();
        }
    }

    public function updatedJukirId($value){
        if ($value) {
            $jukir = Jukir::find($value);
            if ($jukir) {
                $this->area_id = $jukir->area_id;
                $this->hari_libur = json_decode($jukir->hari_libur);

                if ($jukir->tgl_terbit_qr != NULL){
                    $this->tgl_perjanjian = date('d-m-Y', strtotime($jukir->tgl_perjanjian));
                    $this->tgl_terbit_qr = date('d-m-Y', strtotime($jukir->tgl_terbit_qr));
                    $this->potensi_harian = number_format($jukir->potensi_harian, 0, ',', '.');

                    $start = Carbon::parse($this->tgl_perjanjian);
                    $finish = Carbon::parse($this->tgl_terbit_qr);

                    // hitung jumlah hari kerja
                    $this->jml_hari_kerja = $start->diffInDays($finish);

                    // match create form behavior: auto set tgl_transaksi to tgl_terbit_qr for Migrasi
                    $this->tgl_transaksi = date('Y-m-d', strtotime($jukir->tgl_terbit_qr));
                }
                else {
                    $this->tgl_terbit_qr = '';
                    $this->potensi_harian = '';
                    $this->tgl_perjanjian = '';
                    $this->jml_hari_kerja = '';
                }
            }
        }
    }

    public function update()
    {
        $this->validate([
            'tgl_transaksi'    => 'required|date',
            'jumlah_transaksi' => 'required|numeric',
            'no_kwitansi'      => 'required|string|max:250',
            'jukir_id'         => 'required',
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
                    <h5 class="modal-title font-weight-bold" id="modalEditTransactionLabel">Edit Transaksi Tunai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="update">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">Type Transaksi</label>
                                <select class="form-select" wire:model.live="type">
                                    <option value="">-- Pilih Type --</option>
                                    <option value="Non-Migrasi">Non-Migrasi</option>
                                    <option value="Migrasi">Migrasi</option>
                                </select>
                                @error('type') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">Juru Parkir</label>
                                <select class="form-select" wire:model.live="jukir_id">
                                    <option value="">-- Pilih Jukir --</option>
                                    @foreach($jukirs as $jukir)
                                        <option value="{{ $jukir->id }}">{{ $jukir->nama_jukir }}</option>
                                    @endforeach
                                </select>
                                @error('jukir_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            @if($type == 'Migrasi' && $jukir_id)
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-muted small">Tgl Perjanjian</label>
                                    <input type="text" class="form-control bg-light" wire:model="tgl_perjanjian" readonly>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-muted small">Tgl Terbit QR</label>
                                    <input type="text" class="form-control bg-light" wire:model="tgl_terbit_qr" readonly>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-muted small">Potensi Harian</label>
                                    <input type="text" class="form-control bg-light" wire:model="potensi_harian" readonly>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="alert alert-info py-2 small border-0 shadow-none mb-0">
                                        <i class="ti ti-info-circle me-1"></i> Jumlah Hari Kerja: <strong>{{ $jml_hari_kerja }} hari</strong>
                                    </div>
                                </div>
                            @endif

                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">Tanggal Transaksi</label>
                                <input type="date" class="form-control" wire:model="tgl_transaksi">
                                @error('tgl_transaksi') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">No. Kwitansi</label>
                                <input type="text" class="form-control" wire:model="no_kwitansi" placeholder="Nomor Kwitansi">
                                @error('no_kwitansi') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                                                        
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">Jumlah Transaksi (Rp)</label>
                                <input type="number" class="form-control" wire:model="jumlah_transaksi" placeholder="0">
                                @error('jumlah_transaksi') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">Keterangan</label>
                                <input type="text" class="form-control" wire:model="keterangan" placeholder="-">
                                @error('keterangan') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label text-dark fw-bold">Ubah Dokumen Pendukung (Foto/PDF)</label>
                                <input type="file" class="form-control" wire:model="dokumen">
                                <small class="text-muted d-block">Max: 2MB. Kosongkan jika tidak ingin mengubah.</small>
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
