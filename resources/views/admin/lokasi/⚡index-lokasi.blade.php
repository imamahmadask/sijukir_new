<?php

use Livewire\Component;
use App\Models\Lokasi;
use Livewire\Attributes\On;

new class extends Component {

    public $lokasis = [];

    public function mount()
    {
        $this->loadLokasis();
    }

    #[On('refresh-lokasis')]
    public function loadLokasis()
    {
        $this->lokasis = Lokasi::with(['area', 'kelurahan', 'korlap'])->get();
    }

    public function deleteLokasi($id)
    {
        Lokasi::findOrFail($id)->delete();
        $this->loadLokasis();
        session()->flash('success', 'Titik Parkir berhasil dihapus.');
    }

    public function render()
    {
        return $this->view()->title('Titik Parkir');
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
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Titik Parkir</a></li>
                        <li class="breadcrumb-item" aria-current="page">Index</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Daftar Titik Parkir</h5>
                <a href="{{ route('lokasi.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i> Tambah Titik Parkir
                </a>
            </div>

            <div class="card tbl-card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Gambar</th>
                                    <th>Titik Parkir</th>
                                    <th>Lokasi</th>
                                    <th>Area (Kecamatan)</th>
                                    <th>Kelurahan</th>
                                    <th>Korlap</th>
                                    <th>Status</th>
                                    <th width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($lokasis as $index => $item)
                                    <tr wire:key="lokasi-{{ $item->id }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            @if($item->gambar)
                                                <img src="{{ asset('storage/' . $item->gambar) }}" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="rounded bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="ti ti-map-pin text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $item->titik_parkir }}</td>
                                        <td>{{ Str::limit($item->lokasi_parkir, 40) }}</td>
                                        <td>{{ $item->area->Kecamatan ?? '-' }}</td>
                                        <td>{{ $item->kelurahan->kelurahan ?? '-' }}</td>
                                        <td>{{ $item->korlap->nama ?? '-' }}</td>
                                        <td>
                                            <span class="badge fs-6 bg-light-{{ $item->status === 'Aktif' ? 'success' : 'danger' }}">
                                                {{ $item->status ?? 'Aktif' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('lokasi.detail', $item->id) }}" class="btn btn-sm btn-info" title="Detail">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            <a href="{{ route('lokasi.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                wire:click="deleteLokasi({{ $item->id }})"
                                                wire:confirm="Apakah Anda yakin ingin menghapus titik parkir ini?">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">Belum ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
