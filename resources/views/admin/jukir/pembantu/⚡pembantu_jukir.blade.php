<?php

use Livewire\Component;
use App\Models\PembantuJukir;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public $jukir_id;
    public $pembantu_id;
    public $isEdit = false;

    // Form fields
    public $nama = '';
    public $nik = '';
    public $alamat = '';
    public $kel_alamat = '';
    public $kec_alamat = '';
    public $kab_kota_alamat = '';
    public $telepon = '';
    public $tempat_lahir = '';
    public $tgl_lahir = '';
    public $jenis_kelamin = '';
    public $agama = '';
    public $foto;
    public $status = '1';

    // Delete
    public $deleteId = null;

    #[On('open-create-pembantu')]
    public function openCreate($jukir_id)
    {
        $this->resetForm();
        $this->jukir_id = $jukir_id;
        $this->isEdit = false;
        $this->dispatch('open-modal', name: 'modalPembantuJukir');
    }

    #[On('open-edit-pembantu')]
    public function openEdit($id)
    {
        $this->resetForm();
        $pembantu = PembantuJukir::findOrFail($id);
        $this->pembantu_id = $pembantu->id;
        $this->jukir_id = $pembantu->jukir_id;
        $this->isEdit = true;

        $this->nama = $pembantu->nama;
        $this->nik = $pembantu->nik;
        $this->alamat = $pembantu->alamat;
        $this->kel_alamat = $pembantu->kel_alamat;
        $this->kec_alamat = $pembantu->kec_alamat;
        $this->kab_kota_alamat = $pembantu->kab_kota_alamat;
        $this->telepon = $pembantu->telepon;
        $this->tempat_lahir = $pembantu->tempat_lahir;
        $this->tgl_lahir = $pembantu->tgl_lahir;
        $this->jenis_kelamin = $pembantu->jenis_kelamin;
        $this->agama = $pembantu->agama;
        $this->status = $pembantu->status;

        $this->dispatch('open-modal', name: 'modalPembantuJukir');
    }

    #[On('confirm-delete-pembantu')]
    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->dispatch('open-modal', name: 'modalDeletePembantu');
    }

    public function delete()
    {
        PembantuJukir::findOrFail($this->deleteId)->delete();
        $this->deleteId = null;
        $this->dispatch('close-modal', name: 'modalDeletePembantu');
        $this->dispatch('refresh-detail-jukir');
    }

    public function save()
    {
        $this->validate([
            'nama'          => 'required|string|max:255',
            'nik'           => 'required|string|max:20',
            'alamat'        => 'nullable|string',
            'jenis_kelamin' => 'required|string',
            'foto'          => 'nullable|image|max:2048',
        ]);

        $fotoPath = null;
        if ($this->foto) {
            $nama_foto = $this->nama . '_' . time() . '.' . $this->foto->extension();
            $fotoPath = $this->foto->storeAs("pembantu_jukir", $nama_foto, 'public');
        }

        $data = [
            'jukir_id'       => $this->jukir_id,
            'nama'           => $this->nama,
            'nik'            => $this->nik,
            'alamat'         => $this->alamat,
            'kel_alamat'     => $this->kel_alamat,
            'kec_alamat'     => $this->kec_alamat,
            'kab_kota_alamat'=> $this->kab_kota_alamat,
            'telepon'        => $this->telepon,
            'tempat_lahir'   => $this->tempat_lahir,
            'tgl_lahir'      => $this->tgl_lahir ?: null,
            'jenis_kelamin'  => $this->jenis_kelamin,
            'agama'          => $this->agama,
            'status'         => $this->status,
        ];

        if ($fotoPath) {
            $data['foto'] = $fotoPath;
        }

        if ($this->isEdit) {
            PembantuJukir::findOrFail($this->pembantu_id)->update($data);
        } else {
            PembantuJukir::create($data);
        }

        $this->dispatch('close-modal', name: 'modalPembantuJukir');
        $this->dispatch('refresh-detail-jukir');
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['pembantu_id', 'nama', 'nik', 'alamat', 'kel_alamat', 'kec_alamat', 
            'kab_kota_alamat', 'telepon', 'tempat_lahir', 'tgl_lahir', 'jenis_kelamin', 
            'agama', 'foto', 'status', 'isEdit']);
        $this->status = '1';
        $this->resetValidation();
    }

    public function render()
    {
        return <<<'HTML'
        <div>
            <!-- Modal Form Pembantu -->
            <div class="modal fade" id="modalPembantuJukir" tabindex="-1" aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog modal-lg">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title text-white">
                                <i class="ti ti-users me-1"></i>
                                {{ $isEdit ? 'Edit Jukir Pembantu' : 'Tambah Jukir Pembantu' }}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <form wire:submit.prevent="save">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" wire:model="nama" placeholder="Nama Lengkap">
                                        @error('nama') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">NIK <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" wire:model="nik" placeholder="Nomor Induk Kependudukan">
                                        @error('nik') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Tempat Lahir</label>
                                        <input type="text" class="form-control" wire:model="tempat_lahir" placeholder="Kota/Kabupaten">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Tanggal Lahir</label>
                                        <input type="date" class="form-control" wire:model="tgl_lahir">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Jenis Kelamin <span class="text-danger">*</span></label>
                                        <select class="form-select" wire:model="jenis_kelamin">
                                            <option value="">-- Pilih --</option>
                                            <option value="Laki-laki">Laki-laki</option>
                                            <option value="Perempuan">Perempuan</option>
                                        </select>
                                        @error('jenis_kelamin') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Agama</label>
                                        <select class="form-select" wire:model="agama">
                                            <option value="">-- Pilih --</option>
                                            <option value="Islam">Islam</option>
                                            <option value="Kristen">Kristen</option>
                                            <option value="Katolik">Katolik</option>
                                            <option value="Hindu">Hindu</option>
                                            <option value="Buddha">Buddha</option>
                                            <option value="Konghucu">Konghucu</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">No. Telepon</label>
                                        <input type="text" class="form-control" wire:model="telepon" placeholder="08xxxxxxxxxx">
                                    </div>

                                    <div class="col-12"><hr class="my-2"><h6 class="fw-bold text-muted small text-uppercase mb-3">Alamat Domisili</h6></div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-bold">Alamat</label>
                                        <textarea class="form-control" wire:model="alamat" rows="2" placeholder="Alamat lengkap..."></textarea>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Kelurahan</label>
                                        <input type="text" class="form-control" wire:model="kel_alamat" placeholder="Kelurahan">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Kecamatan</label>
                                        <input type="text" class="form-control" wire:model="kec_alamat" placeholder="Kecamatan">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Kab/Kota</label>
                                        <input type="text" class="form-control" wire:model="kab_kota_alamat" placeholder="Kab/Kota">
                                    </div>

                                    <div class="col-12"><hr class="my-2"></div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Foto</label>
                                        <input type="file" class="form-control" wire:model="foto" accept="image/*">
                                        <small class="text-muted">Format: JPG, PNG, Max: 2MB</small>
                                        @error('foto') <small class="text-danger d-block">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Status</label>
                                        <select class="form-select" wire:model="status">
                                            <option value="1">Active</option>
                                            <option value="0">Non Active</option>
                                        </select>
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
            <div class="modal fade" id="modalDeletePembantu" tabindex="-1" aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog modal-sm modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-body text-center p-4">
                            <i class="ti ti-trash text-danger" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 fw-bold">Hapus Jukir Pembantu?</h5>
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