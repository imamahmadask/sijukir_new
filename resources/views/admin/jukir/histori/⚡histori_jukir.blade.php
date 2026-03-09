<?php

use Livewire\Component;
use App\Models\HistoriJukir;
use Livewire\Attributes\On;

new class extends Component {
    public $jukir_id;
    public $histori_id;
    public $isEdit = false;

    // Form fields
    public $tgl_histori = '';
    public $no_surat = '';
    public $jenis_histori = '';
    public $histori = '';
    public $jml_hari_libur = '';
    public $tahun_libur = '';
    public $tgl_awal_libur = '';
    public $tgl_akhir_libur = '';
    public $potensi_harian = '';
    public $kompensasi = '';
    public $bulan_libur = '';

    // Delete
    public $deleteId = null;

    #[On('open-create-histori')]
    public function openCreate($jukir_id)
    {
        $this->resetForm();
        $this->jukir_id = $jukir_id;
        $this->isEdit = false;
        $this->tgl_histori = date('Y-m-d');
        $this->dispatch('open-modal', name: 'modalHistoriJukir');
    }

    #[On('open-edit-histori')]
    public function openEdit($id)
    {
        $this->resetForm();
        $histori = HistoriJukir::findOrFail($id);
        $this->histori_id = $histori->id;
        $this->jukir_id = $histori->jukir_id;
        $this->isEdit = true;

        $this->tgl_histori = $histori->tgl_histori;
        $this->no_surat = $histori->no_surat;
        $this->jenis_histori = $histori->jenis_histori;
        $this->histori = $histori->histori;
        $this->jml_hari_libur = $histori->jml_hari_libur;
        $this->tahun_libur = $histori->tahun_libur;
        $this->tgl_awal_libur = $histori->tgl_awal_libur;
        $this->tgl_akhir_libur = $histori->tgl_akhir_libur;
        $this->potensi_harian = $histori->potensi_harian;
        $this->kompensasi = $histori->kompensasi;
        $this->bulan_libur = $histori->bulan_libur;

        $this->dispatch('open-modal', name: 'modalHistoriJukir');
    }

    #[On('confirm-delete-histori')]
    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->dispatch('open-modal', name: 'modalDeleteHistori');
    }

    public function delete()
    {
        HistoriJukir::findOrFail($this->deleteId)->delete();
        $this->deleteId = null;
        $this->dispatch('close-modal', name: 'modalDeleteHistori');
        $this->dispatch('refresh-detail-jukir');
    }

    public function save()
    {
        $this->validate([
            'tgl_histori'   => 'required|date',
            'jenis_histori' => 'required|string|max:255',
            'histori'       => 'nullable|string',
            'no_surat'      => 'nullable|string|max:255',
        ]);

        $data = [
            'jukir_id'       => $this->jukir_id,
            'tgl_histori'    => $this->tgl_histori,
            'no_surat'       => $this->no_surat,
            'jenis_histori'  => $this->jenis_histori,
            'histori'        => $this->histori,
            'jml_hari_libur' => $this->jml_hari_libur ?: null,
            'tahun_libur'    => $this->tahun_libur ?: null,
            'tgl_awal_libur' => $this->tgl_awal_libur ?: null,
            'tgl_akhir_libur'=> $this->tgl_akhir_libur ?: null,
            'potensi_harian' => $this->potensi_harian ?: null,
            'kompensasi'     => $this->kompensasi ?: null,
            'bulan_libur'    => $this->bulan_libur ?: null,
            'created_by'     => auth()->user()->name ?? null,
            'edited_by'      => $this->isEdit ? (auth()->user()->name ?? null) : null,
        ];

        if ($this->isEdit) {
            HistoriJukir::findOrFail($this->histori_id)->update($data);
        } else {
            HistoriJukir::create($data);
        }

        $this->dispatch('close-modal', name: 'modalHistoriJukir');
        $this->dispatch('refresh-detail-jukir');
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['histori_id', 'tgl_histori', 'no_surat', 'jenis_histori', 'histori',
            'jml_hari_libur', 'tahun_libur', 'tgl_awal_libur', 'tgl_akhir_libur',
            'potensi_harian', 'kompensasi', 'bulan_libur', 'isEdit']);
        $this->resetValidation();
    }

    public function render()
    {
        return <<<'HTML'
        <div>
            <!-- Modal Form Histori -->
            <div class="modal fade" id="modalHistoriJukir" tabindex="-1" aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog modal-lg">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title text-white">
                                <i class="ti ti-history me-1"></i>
                                {{ $isEdit ? 'Edit Histori Jukir' : 'Tambah Histori Jukir' }}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <form wire:submit.prevent="save">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Tanggal Histori <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" wire:model="tgl_histori">
                                        @error('tgl_histori') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">No. Surat</label>
                                        <input type="text" class="form-control" wire:model="no_surat" placeholder="Nomor Surat">
                                        @error('no_surat') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-bold">Jenis Histori <span class="text-danger">*</span></label>
                                        <select class="form-select" wire:model="jenis_histori">
                                            <option value="">-- Pilih Jenis --</option>
                                            <option value="Jukir Libur">Jukir Libur</option>
                                            <option value="Ganti PKH">Ganti PKH</option>
                                            <option value="Ganti Jukir">Ganti Jukir</option>
                                            <option value="Jukir Berhenti">Jukir Berhenti</option>
                                            <option value="Toko Pindah Lokasi">Toko Pindah Lokasi</option>
                                            <option value="Toko Tutup">Toko Tutup</option>
                                            <option value="QR Peralihan">QR Peralihan</option>
                                            <option value="Kompensasi PKH">Kompensasi PKH</option>
                                            <option value="Bayar Kurang Setor">Bayar Kurang Setor</option>
                                            <option value="Lainnya">Lainnya</option>
                                        </select>
                                        @error('jenis_histori') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>                                    
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-bold">Keterangan Histori</label>
                                        <textarea class="form-control" wire:model="histori" rows="3" placeholder="Deskripsi histori..."></textarea>
                                        @error('histori') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="col-12"><hr class="my-2"><h6 class="fw-bold text-muted small text-uppercase mb-3">Informasi Libur / Cuti</h6></div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Tgl Awal Libur</label>
                                        <input type="date" class="form-control" wire:model="tgl_awal_libur">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Tgl Akhir Libur</label>
                                        <input type="date" class="form-control" wire:model="tgl_akhir_libur">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Jml Hari Libur</label>
                                        <input type="number" class="form-control" wire:model="jml_hari_libur" min="1" placeholder="0">
                                        @error('jml_hari_libur') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Bulan Libur</label>
                                        <select class="form-select" wire:model="bulan_libur">
                                            <option value="">-- Pilih Bulan --</option>
                                            <option value="1">Januari</option>
                                            <option value="2">Februari</option>
                                            <option value="3">Maret</option>
                                            <option value="4">April</option>
                                            <option value="5">Mei</option>
                                            <option value="6">Juni</option>
                                            <option value="7">Juli</option>
                                            <option value="8">Agustus</option>
                                            <option value="9">September</option>
                                            <option value="10">Oktober</option>
                                            <option value="11">November</option>
                                            <option value="12">Desember</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Tahun Libur</label>
                                        <select class="form-select" wire:model="tahun_libur">
                                            <option value="">-- Pilih Tahun --</option>
                                            @foreach (range(date('Y'), date('Y') - 10) as $year)
                                                <option value="{{ $year }}">{{ $year }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Potensi Harian</label>
                                        <input type="number" class="form-control" wire:model="potensi_harian" placeholder="0">
                                    </div>                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Kompensasi</label>
                                        <input type="number" class="form-control" wire:model="kompensasi" placeholder="0">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                    <i class="ti ti-save me-1"></i> {{ $isEdit ? 'Perbarui' : 'Simpan' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Delete Confirmation -->
            <div class="modal fade" id="modalDeleteHistori" tabindex="-1" aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog modal-sm modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-body text-center p-4">
                            <i class="ti ti-trash text-danger" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 fw-bold">Hapus Histori?</h5>
                            <p class="text-muted small">Data yang dihapus tidak dapat dikembalikan.</p>
                            <div class="d-flex gap-2 justify-content-center mt-3">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                <button type="button" class="btn btn-danger btn-sm" wire:click="delete">
                                    <i class="ti ti-trash me-1"></i> Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        HTML;
    }
};
?>