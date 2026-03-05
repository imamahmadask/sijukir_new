<?php

use Livewire\Component;
use App\Models\Lokasi;
use App\Models\Area;
use App\Models\Kelurahan;
use App\Models\Korlap;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

new class extends Component {
    use WithFileUploads;

    // Fields from model
    public $titik_parkir = '';
    public $lokasi_parkir = '';
    public $slug = '';
    public $jenis_lokasi = '';
    public $waktu_pelayanan = '';
    public $dasar_ketetapan = '';
    public $no_ketetapan = '';
    public $koordinat = '';
    public $kord_lat = '';
    public $kord_long = '';
    public $status = 'Tunai';
    public $tgl_registrasi = '';
    public $area_id = '';
    public $kelurahan_id = '';
    public $korlap_id = '';
    public $pendaftaran = '';
    public $sisi = '';
    public $panjang_luas = '';
    public $google_maps = '';
    public $tgl_ketetapan = '';
    public $is_jukir = 0;
    public $hari_buka = '';
    public $kategori = '';
    public $keterangan = '';
    public $is_active = 1;
    public $gambar;

    public $areas = [];
    public $kelurahans = [];
    public $korlaps = [];

    public function mount()
    {
        $this->areas = Area::all();
        $this->korlaps = Korlap::all();
        $this->tgl_registrasi = now()->format('Y-m-d');
        $this->tgl_ketetapan = now()->format('Y-m-d');
    }

    public function updatedTitikParkir($value)
    {
        $this->slug = Str::slug($value);
    }

    public function updatedAreaId($value)
    {
        $this->kelurahans = Kelurahan::where('area_id', $value)->get();
        $this->kelurahan_id = '';
    }

    public function updatedKoordinat($value)
    {
        // Split by comma or space
        $parts = preg_split('/[,\s]+/', trim($value));
        
        if (count($parts) >= 2) {
            $this->kord_lat = $parts[0];
            $this->kord_long = $parts[1];
        }
    }

    public function save()
    {
        $this->validate([
            'titik_parkir'    => 'required|string|max:255',
            'lokasi_parkir'   => 'required|string|max:255',
            'slug'            => 'required|string|max:255|unique:lokasis,slug',
            'jenis_lokasi'    => 'required|string|max:255',
            'waktu_pelayanan' => 'required|string|max:255',
            'dasar_ketetapan' => 'nullable|string|max:255',
            'no_ketetapan'    => 'nullable|string|max:255',
            'tgl_registrasi'  => 'required|date',
            'area_id'         => 'required|exists:areas,id',
            'kelurahan_id'    => 'required|exists:kelurahan,id',
            'korlap_id'       => 'required|exists:korlaps,id',
            'gambar'          => 'nullable|image|max:2048',
            'kategori'        => 'required|string|max:255',
        ]);

        $gambarPath = null;
        if ($this->gambar) {
            $gambarPath = $this->gambar->store('lokasis', 'public');
        }

        Lokasi::create([
            'titik_parkir'    => $this->titik_parkir,
            'lokasi_parkir'   => $this->lokasi_parkir,
            'slug'            => $this->slug,
            'jenis_lokasi'    => $this->jenis_lokasi,
            'waktu_pelayanan' => $this->waktu_pelayanan,
            'dasar_ketetapan' => $this->dasar_ketetapan,
            'no_ketetapan'    => $this->no_ketetapan,
            'kord_lat'        => $this->kord_lat,
            'kord_long'       => $this->kord_long,
            'status'          => $this->status,
            'tgl_registrasi'  => $this->tgl_registrasi,
            'area_id'         => $this->area_id,
            'kelurahan_id'    => $this->kelurahan_id,
            'korlap_id'       => $this->korlap_id,
            'pendaftaran'     => $this->pendaftaran,
            'sisi'            => $this->sisi,
            'panjang_luas'    => $this->panjang_luas,
            'google_maps'     => $this->google_maps,
            'tgl_ketetapan'   => $this->tgl_ketetapan,
            'is_jukir'        => $this->is_jukir,
            'hari_buka'       => $this->hari_buka,
            'kategori'        => $this->kategori,
            'keterangan'      => $this->keterangan,
            'is_active'       => $this->is_active,
            'gambar'          => $gambarPath,
        ]);

        session()->flash('success', 'Titik Parkir berhasil ditambahkan.');
        return redirect()->route('lokasi.index');
    }

    public function render()
    {
        return $this->view()->title('Tambah Titik Parkir');
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
                        <h5 class="m-b-10">Titik Parkir</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('lokasi.index') }}">Titik Parkir</a></li>
                        <li class="breadcrumb-item" aria-current="page">Tambah</li>
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
                        <h5>Form Tambah Titik Parkir</h5>
                    </div>
                    <div class="card-body">
                        <!-- Informasi Utama -->
                        <h6 class="mb-3 text-primary">Informasi Utama</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Titik Parkir <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" wire:model="titik_parkir" placeholder="Contoh: Alfamart Ahmad Yani">
                                @error('titik_parkir') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Lokasi Parkir (Alamat Lengkap) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" wire:model="lokasi_parkir" placeholder="Contoh: Jl. Ahmad Yani">
                                @error('lokasi_parkir') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <!-- Wilayah & Korlap -->
                        <h6 class="mb-3 mt-4 text-primary">Wilayah & Pengelola</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Area (Kecamatan) <span class="text-danger">*</span></label>
                                <select class="form-select" wire:model.live="area_id">
                                    <option value="">-- Pilih Area --</option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id }}">{{ $area->Kecamatan }}</option>
                                    @endforeach
                                </select>
                                @error('area_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Kelurahan <span class="text-danger">*</span></label>
                                <select class="form-select" wire:model="kelurahan_id">
                                    <option value="">-- Pilih Kelurahan --</option>
                                    @foreach($kelurahans as $kel)
                                        <option value="{{ $kel->id }}">{{ $kel->kelurahan }}</option>
                                    @endforeach
                                </select>
                                @error('kelurahan_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Korlap <span class="text-danger">*</span></label>
                                <select class="form-select" wire:model="korlap_id">
                                    <option value="">-- Pilih Korlap --</option>
                                    @foreach($korlaps as $korlap)
                                        <option value="{{ $korlap->id }}">{{ $korlap->nama }}</option>
                                    @endforeach
                                </select>
                                @error('korlap_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <!-- Teknis -->
                        <h6 class="mb-3 mt-4 text-primary">Detail Teknis</h6>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Jenis Lokasi <span class="text-danger">*</span></label>
                                <select class="form-select" wire:model="jenis_lokasi">
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="TJU">TJU (Tepi Jalan Umum)</option>
                                    <option value="TKP">TKP (Tempat Khusus Parkir)</option>
                                </select>
                                @error('jenis_lokasi') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Waktu Pelayanan <span class="text-danger">*</span></label>
                                <select class="form-select" wire:model="waktu_pelayanan">
                                    <option>--Pilih Waktu Pelayanan--</option>
                                    <option value="Pagi-Siang">Pagi - Siang</option>
                                    <option value="Pagi-Sore">Pagi - Sore</option>
                                    <option value="Pagi-Malam">Pagi - Malam</option>
                                    <option value="Siang-Sore">Siang - Sore</option>
                                    <option value="Siang-Malam">Siang - Malam</option>
                                    <option value="Sore-Malam">Sore - Malam</option>
                                </select>
                                @error('waktu_pelayanan') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select class="form-select" wire:model="kategori">
                                    <option>--Pilih Kategori--</option>
                                    <option value="Apotek">Apotek</option>
                                    <option value="Bank">Bank</option>
                                    <option value="Cafe">Cafe</option>
                                    <option value="Minimarket">Minimarket</option>
                                    <option value="Pasar-TJU">Pasar-TJU</option>
                                    <option value="Pasar-TKP">Pasar-TKP</option>
                                    <option value="Pertokoan">Pertokoan Umum</option>
                                    <option value="Rumah Makan">Rumah Makan/Warung</option>
                                    <option value="Taman-TJU">Taman-TJU</option>
                                    <option value="Taman-TKP">Taman-TKP</option>
                                    <option value="Pelataran PKL">Pelataran PKL</option>
                                </select>
                                @error('kategori') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Sisi</label>
                                <input type="text" class="form-control" wire:model="sisi" placeholder="e.g. Kanan/Kiri Jalan">
                                @error('sisi') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <!-- Legalitas & Koordinat -->
                        <h6 class="mb-3 mt-4 text-primary">Legalitas & Koordinat</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">No. Ketetapan</label>
                                <input type="text" class="form-control" wire:model="no_ketetapan">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tgl Ketetapan</label>
                                <input type="date" class="form-control" wire:model="tgl_ketetapan">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Dasar Ketetapan</label>
                                <select class="form-select" wire:model="dasar_ketetapan">
                                    <option value="">-- Pilih Dasar Ketetapan --</option>
                                    <option value="PERWAL">PERWAL</option>
                                    <option value="SK WALIKOTA">SK WALIKOTA</option>
                                    <option value="SK KADIS">SK KADIS</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Koordinat</label>
                                <input type="text" class="form-control" wire:model="koordinat" placeholder="-7.xxxxxx">
                                @error('koordinat') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Google Maps Link</label>
                                <input type="text" class="form-control" wire:model="google_maps" placeholder="https://goo.gl/maps/...">
                                @error('google_maps') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <!-- Lainnya -->
                        <h6 class="mb-3 mt-4 text-primary">Informasi Tambahan</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tgl Registrasi <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" wire:model="tgl_registrasi">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Pendaftaran</label>
                                <select class="form-select" wire:model="pendaftaran">
                                    <option value="">-- Pilih Pendaftaran --</option>
                                    <option value="Pendaftaran Baru">Pendaftaran Baru</option>
                                    <option value="Registrasi Ulang">Registrasi Ulang</option>
                                </select>
                                @error('pendaftaran') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Panjang/Luas</label>
                                <input type="text" class="form-control" wire:model="panjang_luas" placeholder="e.g. 10m x 2m">
                                @error('panjang_luas') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Hari Buka (Seminggu)</label>
                                <input type="number" class="form-control" wire:model="hari_buka" placeholder="7 hari">
                                @error('hari_buka') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>                                                     
                            
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Keterangan</label>
                                <textarea class="form-control" wire:model="keterangan" rows="2"></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Gambar/Foto Lokasi</label>
                                <input type="file" class="form-control" wire:model="gambar">
                                @error('gambar') <small class="text-danger">{{ $message }}</small> @enderror
                                @if ($gambar)
                                    <div class="mt-2">
                                        <img src="{{ $gambar->temporaryUrl() }}" class="img-thumbnail" style="height: 200px;">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <a href="{{ route('lokasi.index') }}" class="btn btn-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <span wire:loading.remove wire:target="save">Simpan Titik Parkir</span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
