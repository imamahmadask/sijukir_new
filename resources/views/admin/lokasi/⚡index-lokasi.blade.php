<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Lokasi;
use Livewire\Attributes\On;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    public function updatingSearch()
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
                    $query->where('titik_parkir', 'like', '%' . $this->search . '%')
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
                })->paginate(10),
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
                <div class="card-body">                    
                    <div class="d-flex justify-content-md-end mb-3">
                        <div style="min-width: 250px;">
                            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari Titik/Area/Korlap...">
                        </div>
                    </div>
                    
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
                    <div class="mt-3">
                        {{ $lokasis->links() }}
                    </div>
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
