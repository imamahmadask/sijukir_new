<?php

use Livewire\Component;
use App\Models\Jukir;
use Livewire\Attributes\On;

new class extends Component {
    public $jukirs = [];

    public function mount()
    {
        $this->loadJukirs();
    }

    #[On('refresh-jukirs')]
    public function loadJukirs()
    {
        $this->jukirs = Jukir::with('lokasi')->get();
    }

    public function deleteJukir($id)
    {
        Jukir::findOrFail($id)->delete();
        $this->loadJukirs();
        session()->flash('success', 'Jukir berhasil dihapus.');
    }

    public function render()
    {
        return $this->view()->title('Daftar Jukir');
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
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Jukir</a></li>
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
                <h5 class="mb-0">Daftar Jukir</h5>
                <a href="{{ route('jukir.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i> Tambah Jukir
                </a>
            </div>

            <div class="card tbl-card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Jukir</th>
                                    <th>Lokasi Parkir</th>
                                    <th>Area</th>
                                    <th>Korlap</th>
                                    <th>Status</th>
                                    <th>Ket.</th>
                                    <th width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($jukirs as $index => $item)
                                    <tr wire:key="jukir-{{ $item->id }}">
                                        <td>{{ $index + 1 }}</td>                                        
                                        <td>
                                            <span class="fw-bold">{{ $item->nama_jukir }}</span>
                                            <br>
                                            <small class="text-muted fst-italic">{{ $item->merchant->merchant_name ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $item->lokasi->titik_parkir ?? '-' }}</span> <br>
                                            <small class="text-muted fst-italic">{{ $item->lokasi->lokasi_parkir ?? '-' }}</small>
                                        </td>
                                        <td>{{ $item->lokasi->area->Kecamatan }}</td>
                                        <td>{{ $item->lokasi->korlap->nama }}</td>
                                        <td>
                                            @if($item->status === 'Non-Tunai')
                                                <span class="badge bg-light-success fs-6">
                                                    {{ $item->status ?? '-' }}
                                                </span>
                                            @elseif($item->status === 'Tunai')
                                                <span class="badge bg-light-warning fs-6">
                                                    {{ $item->status ?? '-' }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->ket_jukir === 'Active')
                                                <span class="badge bg-light-success fs-6">
                                                    {{ $item->ket_jukir ?? '-' }}
                                                </span>
                                            @elseif($item->ket_jukir === 'Pending')
                                                <span class="badge bg-light-warning fs-6">
                                                    {{ $item->ket_jukir ?? '-' }}
                                                </span>
                                            @elseif($item->ket_jukir === 'Non Active')
                                                <span class="badge bg-light-danger fs-6">
                                                    {{ $item->ket_jukir ?? '-' }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('jukir.detail', $item->id) }}" class="btn btn-sm btn-info" title="Detail">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            <a href="{{ route('jukir.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                wire:confirm="Apakah Anda yakin ingin menghapus jukir ini?"
                                                wire:click="deleteJukir({{ $item->id }})">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Belum ada data</td>
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
