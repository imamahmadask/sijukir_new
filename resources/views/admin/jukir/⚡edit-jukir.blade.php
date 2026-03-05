<?php

use Livewire\Component;
use App\Models\Jukir;
use App\Models\Lokasi;
use Carbon\Carbon;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public $jukirId;
    
    // Fields from model
    public $kode_jukir = '';
    public $nik_jukir = '';
    public $nama_jukir = '';
    public $tempat_lahir = '';
    public $tgl_lahir = '';
    public $alamat = '';
    public $kel_alamat = '';
    public $kec_alamat = '';
    public $kab_kota_alamat = '';
    public $telepon = '';
    public $agama = '';
    public $jenis_jukir = 'Jukir Utama';
    public $status = 'Tunai';
    public $foto;
    public $oldFoto;
    public $lokasi_id = '';
    public $document;
    public $oldDocument;
    public $merchant_id = '';
    public $jenis_kelamin = '';
    public $no_perjanjian = '';
    public $tgl_perjanjian = '';
    public $potensi_harian = '';
    public $potensi_bulanan = '';
    public $ket_jukir = 'Pending';
    public $tgl_terbit_qr = '';
    public $hari_libur = [];
    public $jml_hari_kerja = '';
    public $waktu_kerja = '';
    public $area_id = '';
    public $hari_kerja_bulan = '';
    public $uji_petik = '';
    public $tgl_pkh_upl = '';
    public $potensi_bulanan_upl = '';

    public $lokasis = [];

    public function mount($id)
    {
        $jukir = Jukir::findOrFail($id);
        
        $this->jukirId          = $jukir->id;
        $this->kode_jukir       = $jukir->kode_jukir;
        $this->nik_jukir        = $jukir->nik_jukir;
        $this->nama_jukir       = $jukir->nama_jukir;
        $this->tempat_lahir     = $jukir->tempat_lahir;
        $this->tgl_lahir        = $jukir->tgl_lahir;
        $this->alamat           = $jukir->alamat;
        $this->kel_alamat       = $jukir->kel_alamat;
        $this->kec_alamat       = $jukir->kec_alamat;
        $this->kab_kota_alamat  = $jukir->kab_kota_alamat;
        $this->telepon          = $jukir->telepon;
        $this->agama            = $jukir->agama;
        $this->jenis_jukir      = $jukir->jenis_jukir ?? 'Jukir Utama';
        $this->status           = $jukir->status ?? 'Tunai';
        $this->oldFoto          = $jukir->foto;
        $this->lokasi_id        = $jukir->lokasi_id;
        $this->oldDocument      = $jukir->document;
        $this->merchant_id      = $jukir->merchant_id;
        $this->jenis_kelamin    = $jukir->jenis_kelamin;
        $this->no_perjanjian    = $jukir->no_perjanjian;
        $this->tgl_perjanjian   = $jukir->tgl_perjanjian;
        $this->potensi_harian   = $jukir->potensi_harian;
        $this->potensi_bulanan  = $jukir->potensi_bulanan;
        $this->ket_jukir        = $jukir->ket_jukir ?? 'Pending';
        $this->tgl_terbit_qr    = $jukir->tgl_terbit_qr;
        $this->hari_libur       = json_decode($jukir->hari_libur, true) ?? [];
        $this->jml_hari_kerja   = $jukir->jml_hari_kerja;
        $this->waktu_kerja      = $jukir->waktu_kerja;
        $this->area_id          = $jukir->area_id;
        $this->hari_kerja_bulan = $jukir->hari_kerja_bulan;
        $this->uji_petik        = $jukir->uji_petik;
        $this->tgl_pkh_upl      = $jukir->tgl_pkh_upl;
        $this->potensi_bulanan_upl = $jukir->potensi_bulanan_upl;

        $this->lokasis = Lokasi::all();
    }

    public function save()
    {
        $this->validate([
            'nama_jukir'    => 'required|string|max:255',
            'nik_jukir'     => 'required|string|max:16|unique:jukirs,nik_jukir,' . $this->jukirId,
            'kode_jukir'    => 'required|string|max:255',
            'lokasi_id'     => 'required|exists:lokasis,id',
            'foto'          => 'nullable|image|max:2048',
            'document'      => 'nullable|file|max:2048',            
            'jenis_kelamin' => 'required|string',
            'tempat_lahir'  => 'required|string',
            'tgl_lahir'     => 'required|date',
            'agama'         => 'required|string',
            'telepon'       => 'required|string',
            'alamat'        => 'required|string',
            'kel_alamat'    => 'required|string',
            'kec_alamat'    => 'required|string',
            'kab_kota_alamat' => 'required|string',
            'potensi_harian' => 'required|numeric',
            'tgl_terbit_qr' => 'required|date',
            'jml_hari_kerja' => 'required|numeric',
            'waktu_kerja'   => 'required|string',
            'hari_kerja_bulan' => 'required|numeric',
            'potensi_bulanan' => 'required|numeric',
        ]);

        $jukir = Jukir::findOrFail($this->jukirId);
        // $lokasi = Lokasi::findOrFail($this->lokasi_id); // This line is no longer needed as location data is directly assigned

        $data = [
            'kode_jukir'        => $this->kode_jukir,
            'nik_jukir'         => $this->nik_jukir,
            'nama_jukir'        => $this->nama_jukir,
            'tempat_lahir'      => $this->tempat_lahir,
            'tgl_lahir'         => $this->tgl_lahir,
            'alamat'            => $this->alamat,
            'kel_alamat'        => $this->kel_alamat,
            'kec_alamat'        => $this->kec_alamat,
            'kab_kota_alamat'   => $this->kab_kota_alamat,
            'telepon'           => $this->telepon,
            'agama'             => $this->agama,
            'jenis_jukir'       => $this->jenis_jukir,
            'status'            => $this->status,
            'jenis_kelamin'     => $this->jenis_kelamin,
            'no_perjanjian'     => $this->no_perjanjian,
            'tgl_perjanjian'    => $this->tgl_perjanjian ?: null,
            'tgl_akhir_perjanjian' => $this->tgl_perjanjian ? Carbon::parse($this->tgl_perjanjian)->addMonths(6)->toDateString() : null,
            'potensi_harian'    => $this->potensi_harian,
            'potensi_bulanan'   => $this->potensi_bulanan,
            'uji_petik'         => $this->uji_petik,
            'tgl_pkh_upl'       => $this->tgl_pkh_upl ?: null,
            'potensi_bulanan_upl' => $this->potensi_bulanan_upl,
            'tgl_terbit_qr'     => $this->tgl_terbit_qr ?: null,
            'lokasi_id'         => $this->lokasi_id,
            'ket_jukir'         => $this->ket_jukir,
            'jml_hari_kerja'    => $this->jml_hari_kerja,
            'hari_kerja_bulan'  => $this->hari_kerja_bulan,
            'waktu_kerja'       => $this->waktu_kerja,
            'hari_libur'        => json_encode($this->hari_libur),
            'area_id'           => $this->area_id
        ];

        if ($this->foto) {
            if ($jukir->foto) {
                Storage::disk('public')->delete($jukir->foto);
            }
            $nama_foto = $this->nama_jukir . '_' . $this->kode_jukir . '.' . $this->foto->extension();
            $data['foto'] = $this->foto->storeAs("foto_jukir", $nama_foto, 'public');
        }

        if ($this->document) {
            if ($jukir->document) {
                Storage::disk('public')->delete($jukir->document);
            }
            $nama_document = $this->nama_jukir . '_' . $this->kode_jukir . '.' . $this->document->extension();
            $data['document'] = $this->document->storeAs("document_jukir", $nama_document, 'public');
        }

        $jukir->update($data);

        session()->flash('success', 'Jukir berhasil diperbarui.');
        return redirect()->route('jukir.index');
    }

    public function render()
    {
        return $this->view()->title('Edit Jukir');
    }
};
?>

<div>
    <!-- Header -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Jukir</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('jukir.index') }}">Jukir</a></li>
                        <li class="breadcrumb-item" aria-current="page">Edit</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <form wire:submit.prevent="save">
                <div class="card">
                    <div class="card-header">
                        <h5>Form Edit Jukir</h5>
                    </div>
                    <div class="card-body">
                        <!-- Informasi Pribadi -->
                        <h6 class="mb-3 text-primary">Informasi Pribadi</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Nama Jukir <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" wire:model="nama_jukir">
                                @error('nama_jukir') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">NIK <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" wire:model="nik_jukir">
                                @error('nik_jukir') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Kode Jukir</label>
                                <input type="text" class="form-control" wire:model="kode_jukir">
                                @error('kode_jukir') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <select class="form-select" wire:model="jenis_kelamin">
                                    <option value="">-- Pilih --</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tempat Lahir</label>
                                <input type="text" class="form-control" wire:model="tempat_lahir">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control" wire:model="tgl_lahir">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Agama</label>
                                <select class="form-select" wire:model="agama">
                                    <option value="">-- Pilih --</option>
                                    <option value="Islam">Islam</option>
                                    <option value="Kristen">Kristen</option>
                                    <option value="Katolik">Katolik</option>
                                    <option value="Hindu">Hindu</option>
                                    <option value="Budha">Budha</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Telepon</label>
                                <input type="text" class="form-control" wire:model="telepon">
                            </div>
                        </div>

                        <!-- Alamat -->
                        <h6 class="mb-3 mt-4 text-primary">Alamat</h6>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Alamat Lengkap</label>
                                <textarea class="form-control" wire:model="alamat" rows="2"></textarea>
                                @error('alamat') <small class="text-danger">{{ $message }}</small> @enderror                                                                
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Kelurahan</label>
                                <input type="text" class="form-control" wire:model="kel_alamat">
                                @error('kel_alamat') <small class="text-danger">{{ $message }}</small> @enderror                                                                
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Kecamatan</label>
                                <input type="text" class="form-control" wire:model="kec_alamat">
                                @error('kec_alamat') <small class="text-danger">{{ $message }}</small> @enderror                                                                
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Kab/Kota</label>
                                <input type="text" class="form-control" wire:model="kab_kota_alamat">
                                @error('kab_kota_alamat') <small class="text-danger">{{ $message }}</small> @enderror                                                                
                            </div>
                        </div>

                        <!-- Penugasan -->
                        <h6 class="mb-3 mt-4 text-primary">Lokasi & Hari Kerja</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Titik Parkir</label>
                                <select class="form-select" wire:model="lokasi_id">
                                    <option value="">-- Pilih Lokasi --</option>
                                    @foreach($lokasis as $lok)
                                        <option value="{{ $lok->id }}">{{ $lok->titik_parkir }}</option>
                                    @endforeach
                                </select>
                                @error('lokasi_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">No. Perjanjian</label>
                                <input type="text" class="form-control" wire:model="no_perjanjian">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tgl Perjanjian</label>
                                <input type="date" class="form-control" wire:model="tgl_perjanjian">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tgl Terbit QR</label>
                                <input type="date" class="form-control" wire:model="tgl_terbit_qr">
                                @error('tgl_terbit_qr') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Jumlah Hari Kerja (Seminggu)</label>
                                <input type="number" class="form-control" wire:model="jml_hari_kerja">
                                @error('jml_hari_kerja') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Jumlah Hari Kerja (Sebulan)</label>
                                <input type="number" class="form-control" wire:model="hari_kerja_bulan">
                                @error('hari_kerja_bulan') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Hari Libur (Bila Ada)</label>
                                <select class="form-select" wire:model="hari_libur" multiple="multiple">
                                    <option value="">-- Pilih Hari --</option>
                                    <option value="Senin">Senin</option>
                                    <option value="Selasa">Selasa</option>
                                    <option value="Rabu">Rabu</option>
                                    <option value="Kamis">Kamis</option>
                                    <option value="Jumat">Jumat</option>
                                    <option value="Sabtu">Sabtu</option>
                                    <option value="Minggu">Minggu</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Waktu Kerja</label>
                                <select class="form-select" wire:model="waktu_kerja">
                                    <option value="" selected>--Pilih Waktu Kerja--</option>
                                    <option value="Pagi-Siang">Pagi-Siang</option>
                                    <option value="Pagi-Sore">Pagi-Sore</option>
                                    <option value="Pagi-Malam">Pagi-Malam</option>
                                    <option value="Siang-Sore">Siang-Sore</option>
                                    <option value="Siang-Malam">Siang-Malam</option>
                                    <option value="Sore-Malam">Sore-Malam</option>
                                </select>
                                @error('waktu_kerja') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>                            
                        </div>

                        <h6 class="mb-3 mt-4 text-primary">Potensi Harian & Bulanan</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Potensi Harian</label>
                                <input type="number" class="form-control" wire:model="potensi_harian">
                                @error('potensi_harian') <small class="text-danger">{{ $message }}</small> @enderror                                
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Potensi Bulanan</label>
                                <input type="number" class="form-control" wire:model="potensi_bulanan">
                                @error('potensi_bulanan') <small class="text-danger">{{ $message }}</small> @enderror                                
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tgl PKH UPL</label>
                                <input type="date" class="form-control" wire:model.live="tgl_pkh_upl">
                            </div>
                            @if($tgl_pkh_upl)
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Potensi Harian (Uji Petik)</label>
                                <input type="number" class="form-control" wire:model="uji_petik">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Potensi Bulanan (Uji Petik)</label>
                                <input type="number" class="form-control" wire:model="potensi_bulanan_upl">
                            </div>
                            @endif
                        </div>                       

                        <!-- Berkas -->
                        <h6 class="mb-3 mt-4 text-primary">Berkas & Foto</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Foto Jukir</label>
                                <input type="file" class="form-control" wire:model="foto">
                                @error('foto') <small class="text-danger">{{ $message }}</small> @enderror
                                <div class="mt-2 text-center">
                                    @if ($foto)
                                        <img src="{{ $foto->temporaryUrl() }}" class="img-thumbnail" style="height: 150px;">
                                    @elseif ($oldFoto)
                                        <img src="{{ asset('storage/' . $oldFoto) }}" class="img-thumbnail" style="height: 150px;">
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Dokumen (PDF/Scan)</label>
                                <input type="file" class="form-control" wire:model="document">
                                @error('document') <small class="text-danger">{{ $message }}</small> @enderror
                                @if ($oldDocument)
                                    <div class="mt-2">
                                        <a href="{{ asset('storage/' . $oldDocument) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                            <i class="ti ti-file-description"></i> Lihat Dokumen Saat Ini
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Keterangan</label>
                                <textarea class="form-control" wire:model="ket_jukir" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <a href="{{ route('jukir.index') }}" class="btn btn-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="save">Update Jukir</span>
                            <span wire:loading wire:target="save">Updating...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
