<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Lokasi;
use App\Models\Area;
use App\Models\Korlap;
use Livewire\Attributes\On;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $areaFilter = '';
    public $korlapFilter = '';
    public $isActiveFilter = '';
    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingAreaFilter()
    {
        $this->resetPage();
    }

    public function updatingKorlapFilter()
    {
        $this->resetPage();
    }

    public function updatingIsActiveFilter()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    #[On('refresh-lokasis')]
    public function refresh()
    {
        // triggers re-render via refresh event
    }

    public function deleteLokasi($id)
    {
        Lokasi::findOrFail($id)->delete();
        session()->flash('success', 'Titik Parkir berhasil dihapus.');
    }

    public function with(): array
    {
        return [
            'lokasis' => Lokasi::with(['area', 'kelurahan', 'korlap'])
                ->when($this->search, function ($query) {
                    $query->where(function($qq) {
                        $qq->where('titik_parkir', 'like', '%' . $this->search . '%')
                           ->orWhere('lokasi_parkir', 'like', '%' . $this->search . '%')
                           ->orWhereHas('area', function ($q) {
                               $q->where('Kecamatan', 'like', '%' . $this->search . '%');
                           })
                           ->orWhereHas('kelurahan', function ($q) {
                               $q->where('kelurahan', 'like', '%' . $this->search . '%');
                           })
                           ->orWhereHas('korlap', function ($q) {
                               $q->where('nama', 'like', '%' . $this->search . '%');
                           });
                    });
                })
                ->when($this->areaFilter !== '', function ($query) {
                    $query->where('area_id', $this->areaFilter);
                })
                ->when($this->korlapFilter !== '', function ($query) {
                    $query->where('korlap_id', $this->korlapFilter);
                })
                ->when($this->isActiveFilter !== '', function ($query) {
                    $query->where('is_active', $this->isActiveFilter);
                })
                ->paginate($this->perPage),
            'areas' => Area::orderBy('Kecamatan', 'asc')->get(),
            'korlaps' => Korlap::orderBy('nama', 'asc')->get()
        ];
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
                <!-- Filters -->
                <div class="card-header bg-transparent border-0 px-4 pb-3 pt-3">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-2">
                             <select class="form-select" wire:model.live="perPage">
                                <option value="10">10 per halaman</option>
                                <option value="25">25 per halaman</option>
                                <option value="50">50 per halaman</option>
                                <option value="100">100 per halaman</option>
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
                            <select class="form-select" wire:model.live="korlapFilter">
                                <option value="">Semua Korlap</option>
                                @foreach($korlaps as $korlap)
                                    <option value="{{ $korlap->id }}">{{ $korlap->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" wire:model.live="isActiveFilter">
                                <option value="">Semua Status</option>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>
                        <div class="col-md-4 ms-auto">
                            <div class="input-group border-0 shadow-sm rounded-1">
                                <span class="input-group-text bg-white border-end-0"><i class="ti ti-search text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari Titik/Area/Korlap..." wire:model.live.debounce.300ms="search">
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
                                    <th>Titik Parkir</th>
                                    <th>Lokasi</th>
                                    <th>Area</th>
                                    <th>Kelurahan</th>
                                    <th>Korlap</th>
                                    <th>Status</th>
                                    <th width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($lokasis as $index => $item)
                                    <tr wire:key="lokasi-{{ $item->id }}">
                                        <td>{{ $lokasis->firstItem() + $index }}</td>                                        
                                        <td>{{ $item->titik_parkir }}</td>
                                        <td>{{ Str::limit($item->lokasi_parkir, 40) }}</td>
                                        <td>{{ $item->area->Kecamatan ?? '-' }}</td>
                                        <td>{{ $item->kelurahan->kelurahan ?? '-' }}</td>
                                        <td>{{ $item->korlap->nama ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-light-{{ $item->is_active === 1 ? 'success' : 'danger' }}">
                                                {{ $item->is_active === 1 ? 'Aktif' : 'Tidak Aktif' }}
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
                <div class="card-footer bg-transparent border-0 px-4 py-3">
                    {{ $lokasis->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.alert-dismissible').forEach(function (alert) {
                setTimeout(function () {
                    var bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                    bsAlert.close();
                }, 3000);
            });
        });
    </script>
</div>
