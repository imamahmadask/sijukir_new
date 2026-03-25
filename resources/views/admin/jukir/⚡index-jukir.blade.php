<?php

use Livewire\Component;
use App\Models\Jukir;
use App\Models\Area;
use Livewire\Attributes\On;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $areaFilter = '';
    public $statusFilter = '';
    public $ketJukirFilter = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingAreaFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingKetJukirFilter()
    {
        $this->resetPage();
    }

    #[On('refresh-jukirs')]
    public function render()
    {
        $query = Jukir::with(['lokasi.area', 'lokasi.korlap', 'merchant'])
            ->when($this->search, function($q) {
                $q->where(function($qq) {
                    $qq->where('nama_jukir', 'like', '%' . $this->search . '%')
                       ->orWhereHas('lokasi', function($q3) {
                           $q3->where('titik_parkir', 'like', '%' . $this->search . '%')
                              ->orWhere('lokasi_parkir', 'like', '%' . $this->search . '%');
                       });
                });
            })
            ->when($this->areaFilter, function($q) {
                $q->whereHas('lokasi', function($q2) {
                    $q2->where('area_id', $this->areaFilter);
                });
            })
            ->when($this->statusFilter, function($q) {
                $q->where('status', $this->statusFilter);
            })
            ->when($this->ketJukirFilter, function($q) {
                $q->where('ket_jukir', $this->ketJukirFilter);
            });

        $jukirs = $query->paginate($this->perPage);
        $areas = Area::orderBy('Kecamatan', 'asc')->get();

        return $this->view()->title('Daftar Jukir')->with([
            'jukirs' => $jukirs,
            'areas' => $areas
        ]);
    }

    public function deleteJukir($id)
    {
        Jukir::findOrFail($id)->delete();
        session()->flash('success', 'Jukir berhasil dihapus.');
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
                <!-- Filters -->
                <div class="card-header bg-transparent border-0 px-4 pb-3 pt-3">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-2">
                             <select class="form-select" wire:model.live="perPage">
                                <option value="10">10 per halaman</option>
                                <option value="25">25 per halaman</option>
                                <option value="50">50 per halaman</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" wire:model.live="areaFilter">
                                <option value="">Semua Area</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}">{{ $area->Kecamatan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" wire:model.live="statusFilter">
                                <option value="">Semua Status</option>
                                <option value="Tunai">Tunai</option>
                                <option value="Non-Tunai">Non-Tunai</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" wire:model.live="ketJukirFilter">
                                <option value="">Semua Keterangan</option>
                                <option value="Active">Active</option>
                                <option value="Pending">Pending</option>
                                <option value="Non Active">Non Active</option>
                            </select>
                        </div>
                        <div class="col-md-4 ms-auto">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="ti ti-search text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari Jukir atau Lokasi..." wire:model.live="search">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
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
                                        <td>{{ ($jukirs->currentPage() - 1) * $jukirs->perPage() + $index + 1 }}</td>                                        
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
                                                <span class="badge bg-light-success">
                                                    {{ $item->status ?? '-' }}
                                                </span>
                                            @elseif($item->status === 'Tunai')
                                                <span class="badge bg-light-warning">
                                                    {{ $item->status ?? '-' }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->ket_jukir === 'Active')
                                                <span class="badge bg-light-success">
                                                    {{ $item->ket_jukir ?? '-' }}
                                                </span>
                                            @elseif($item->ket_jukir === 'Pending')
                                                <span class="badge bg-light-warning">
                                                    {{ $item->ket_jukir ?? '-' }}
                                                </span>
                                            @elseif($item->ket_jukir === 'Non Active')
                                                <span class="badge bg-light-danger">
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
                <div class="card-footer bg-transparent border-0 px-4 py-3">
                    {{ $jukirs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
