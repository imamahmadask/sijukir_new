<?php

use Livewire\Component;
use App\Models\Insidentil;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public $insidentilId;
    // Data Surat
    public $tgl_pendaftaran, $no_surat;
    // Data Pemohon
    public $nik, $nama, $telepon, $alamat, $tempat_lahir, $tgl_lahir, $jk, $agama, $pekerjaan;
    // Data Perusahaan
    public $nama_perusahaan, $alamat_perusahaan, $akta_perusahaan, $npwp_perusahaan;
    // Data Acara
    public $nama_acara, $lokasi_acara, $waktu_acara, $jumlah_hari, $tgl_awal_acara, $tgl_akhir_acara;
    public $lokasi_parkir, $luas_lokasi, $kriteria_lokasi, $jenis_izin;
    public $r2, $r4, $potensi, $setoran, $dokumen, $keterangan;
    public $existing_dokumen;

    #[On('open-edit-insidentil')]
    public function openModal($id)
    {
        $this->resetValidation();
        $ins = Insidentil::findOrFail($id);
        $this->insidentilId = $ins->id;
        // Assign fields
        $this->tgl_pendaftaran = $ins->tgl_pendaftaran;
        $this->no_surat = $ins->no_surat;
        $this->nik = $ins->nik;
        $this->nama = $ins->nama;
        $this->alamat = $ins->alamat;
        $this->tempat_lahir = $ins->tempat_lahir;
        $this->tgl_lahir = $ins->tgl_lahir;
        $this->jk = $ins->jk;
        $this->agama = $ins->agama;
        $this->pekerjaan = $ins->pekerjaan;
        $this->telepon = $ins->telepon;

        $this->nama_perusahaan = $ins->nama_perusahaan;
        $this->alamat_perusahaan = $ins->alamat_perusahaan;
        $this->akta_perusahaan = $ins->akta_perusahaan;
        $this->npwp_perusahaan = $ins->npwp_perusahaan;

        $this->nama_acara = $ins->nama_acara;
        $this->lokasi_acara = $ins->lokasi_acara;
        $this->waktu_acara = $ins->waktu_acara;
        $this->jumlah_hari = $ins->jumlah_hari;
        $this->tgl_awal_acara = $ins->tgl_awal_acara;
        $this->tgl_akhir_acara = $ins->tgl_akhir_acara;

        $this->lokasi_parkir = $ins->lokasi_parkir;
        $this->luas_lokasi = $ins->luas_lokasi;
        $this->kriteria_lokasi = $ins->kriteria_lokasi;
        $this->jenis_izin = $ins->jenis_izin;
        $this->r2 = $ins->r2;
        $this->r4 = $ins->r4;
        $this->potensi = $ins->potensi;
        $this->setoran = $ins->setoran;
        $this->keterangan = $ins->keterangan;
        $this->existing_dokumen = $ins->dokumen;
        $this->dokumen = null;

        $this->dispatch('open-modal', name: 'modal-edit-insidentil');
    }

    public function update()
    {
        $this->validate([
            'nama' => 'required',
            'nama_acara' => 'required',
        ]);

        $ins = Insidentil::findOrFail($this->insidentilId);
        
        $dokumenPath = $ins->dokumen;
        if ($this->dokumen) {
            $dokumenPath = $this->dokumen->store('dokumen_insidentil', 'public');
        }

        $ins->update([
            'tgl_pendaftaran' => $this->tgl_pendaftaran,
            'no_surat' => $this->no_surat,
            'nik' => $this->nik,
            'nama' => $this->nama,
            'alamat' => $this->alamat,
            'tempat_lahir' => $this->tempat_lahir,
            'tgl_lahir' => $this->tgl_lahir,
            'jk' => $this->jk,
            'agama' => $this->agama,
            'pekerjaan' => $this->pekerjaan,
            'telepon' => $this->telepon,
            'nama_perusahaan' => $this->nama_perusahaan,
            'alamat_perusahaan' => $this->alamat_perusahaan,
            'akta_perusahaan' => $this->akta_perusahaan,
            'npwp_perusahaan' => $this->npwp_perusahaan,
            'nama_acara' => $this->nama_acara,
            'lokasi_acara' => $this->lokasi_acara,
            'waktu_acara' => $this->waktu_acara,
            'jumlah_hari' => $this->jumlah_hari,
            'tgl_awal_acara' => $this->tgl_awal_acara,
            'tgl_akhir_acara' => $this->tgl_akhir_acara,
            'lokasi_parkir' => $this->lokasi_parkir,
            'luas_lokasi' => $this->luas_lokasi,
            'kriteria_lokasi' => $this->kriteria_lokasi,
            'jenis_izin' => $this->jenis_izin,
            'r2' => $this->r2,
            'r4' => $this->r4,
            'potensi' => $this->potensi,
            'setoran' => $this->setoran,
            'keterangan' => $this->keterangan,
            'dokumen' => $dokumenPath,
        ]);

        $this->dispatch('close-modal', name: 'modal-edit-insidentil');
        $this->dispatch('refresh-insidentil');
        session()->flash('success', 'Data berhasil diperbarui.');
    }
};
?>

<div class="modal fade" id="modal-edit-insidentil" tabindex="-1" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Insidentil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form wire:submit="update">
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Data Surat -->
                        <div class="col-12"><h6 class="mb-0 text-primary border-bottom pb-2">Informasi Pendaftaran</h6></div>
                        <div class="col-md-6">
                            <label class="form-label">No Surat</label>
                            <input type="text" class="form-control" wire:model="no_surat">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tgl Pendaftaran</label>
                            <input type="date" class="form-control" wire:model="tgl_pendaftaran">
                        </div>

                        <!-- Data Pemohon -->
                        <div class="col-12"><h6 class="mb-0 text-primary border-bottom pb-2 mt-3">Data Pemohon</h6></div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Pengelola <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="nama" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NIK</label>
                            <input type="text" class="form-control" wire:model="nik">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Alamat Pengelola</label>
                            <input type="text" class="form-control" wire:model="alamat">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" class="form-control" wire:model="tempat_lahir">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" class="form-control" wire:model="tgl_lahir">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jenis Kelamin</label>
                            <select class="form-select" wire:model="jk">
                                <option value="">Pilih</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Agama</label>
                            <input type="text" class="form-control" wire:model="agama">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pekerjaan</label>
                            <input type="text" class="form-control" wire:model="pekerjaan">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telepon</label>
                            <input type="text" class="form-control" wire:model="telepon">
                        </div>

                        <!-- Data Perusahaan -->
                        <div class="col-12"><h6 class="mb-0 text-primary border-bottom pb-2 mt-3">Data Perusahaan</h6></div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Perusahaan</label>
                            <input type="text" class="form-control" wire:model="nama_perusahaan">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Alamat Perusahaan</label>
                            <input type="text" class="form-control" wire:model="alamat_perusahaan">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Akta Perusahaan</label>
                            <input type="text" class="form-control" wire:model="akta_perusahaan">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NPWP Perusahaan</label>
                            <input type="text" class="form-control" wire:model="npwp_perusahaan">
                        </div>

                        <!-- Data Acara -->
                        <div class="col-12"><h6 class="mb-0 text-primary border-bottom pb-2 mt-3">Data Acara</h6></div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Acara <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="nama_acara" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Lokasi Acara</label>
                            <input type="text" class="form-control" wire:model="lokasi_acara">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Waktu Acara</label>
                            <select class="form-select" wire:model="waktu_acara">
                                <option value="">Pilih Waktu</option>
                                <option value="Pagi">Pagi</option>
                                <option value="Siang">Siang</option>
                                <option value="Sore">Sore</option>
                                <option value="Malam">Malam</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jumlah / Lama Acara (Hari)</label>
                            <input type="number" class="form-control" wire:model="jumlah_hari">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tgl Awal Acara</label>
                            <input type="date" class="form-control" wire:model="tgl_awal_acara">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tgl Akhir Acara</label>
                            <input type="date" class="form-control" wire:model="tgl_akhir_acara">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Lokasi Parkir</label>
                            <input type="text" class="form-control" wire:model="lokasi_parkir">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Luas Lokasi Parkir</label>
                            <input type="text" class="form-control" wire:model="luas_lokasi">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kriteria Lokasi Parkir</label>
                            <input type="text" class="form-control" wire:model="kriteria_lokasi">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis Izin</label>
                            <input type="text" class="form-control" wire:model="jenis_izin">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Potensi Roda 2</label>
                            <input type="number" class="form-control" wire:model="r2">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Potensi Roda 4</label>
                            <input type="number" class="form-control" wire:model="r4">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Potensi Perhitungan (Rp)</label>
                            <input type="number" class="form-control" wire:model="potensi">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Dokumen (Upload Baru)</label>
                            <input type="file" class="form-control" wire:model="dokumen">
                            @if($existing_dokumen)
                                <small class="text-success mt-1 d-block">File sudah ada. Upload baru untuk mengganti.</small>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Keterangan</label>
                            <input type="text" class="form-control" wire:model="keterangan">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Perbarui</button>
                </div>
            </form>
        </div>
    </div>
</div>
