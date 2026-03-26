<?php

use Livewire\Component;
use App\Models\ParkirBerlangganan;
use Livewire\Attributes\On;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $monthFilter;

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->monthFilter = \Carbon\Carbon::now()->format('Y-m');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingMonthFilter()
    {
        $this->resetPage();
    }

    #[On('refresh-berlangganans')]
    public function render()
    {
        $query = ParkirBerlangganan::when($this->search, function($q) {
                $q->where(function($qq) {
                    $qq->where('nomor', 'like', '%' . $this->search . '%')
                       ->orWhere('nama', 'like', '%' . $this->search . '%')
                       ->orWhere('no_pol', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->monthFilter, function($q) {
                $parts = explode('-', $this->monthFilter);
                if (count($parts) == 2) {
                    $q->whereYear('tgl_dikeluarkan', $parts[0])
                      ->whereMonth('tgl_dikeluarkan', $parts[1]);
                }
            });

        $berlangganans = $query->latest()->paginate($this->perPage);

        return $this->view()->title('Daftar Parkir Berlangganan')->with([
            'berlangganans' => $berlangganans
        ]);
    }

    public function createBerlangganan()
    {
        $this->dispatch('open-create-berlangganan')->to('admin::berlangganan.create-berlangganan');
    }

    public function editBerlangganan($id)
    {
        $this->dispatch('open-edit-berlangganan', id: $id)->to('admin::berlangganan.edit-berlangganan');
    }

    public function showDetail($id)
    {
        $this->dispatch('show-berlangganan-detail', id: $id)->to('admin::berlangganan.detail-berlangganan');
    }

    public function deleteBerlangganan($id)
    {
        ParkirBerlangganan::findOrFail($id)->delete();
        session()->flash('success', 'Data Parkir Berlangganan berhasil dihapus.');
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
                        <h5 class="m-b-10">Parkir Berlangganan</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Parkir Berlangganan</a></li>
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
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="ti ti-check me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm tbl-card">
                <div class="card-header bg-transparent border-0 px-4 py-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h5 class="mb-0 fw-bold">Daftar Parkir Berlangganan</h5>
                    <button type="button" class="btn btn-primary shadow-sm" wire:click="createBerlangganan">
                        <i class="ti ti-plus me-1"></i> Tambah Berlangganan
                    </button>
                </div>
                
                <!-- Filters -->
                <div class="card-header bg-transparent border-0 px-4 pb-3 pt-0">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-2">
                             <select class="form-select" wire:model.live="perPage">
                                <option value="10">10 per halaman</option>
                                <option value="25">25 per halaman</option>
                                <option value="50">50 per halaman</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="month" class="form-control" wire:model.live="monthFilter" title="Filter Bulan">
                        </div>
                        <div class="col-md-4 ms-auto">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="ti ti-search text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari Nomor/Nama/Nopol..." wire:model.live.debounce.300ms="search">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless mb-0 align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">#</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted">Nomor</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted">Tgl Kwitansi</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted">Nama</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted">No Polisi</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted">Jenis</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted">Jumlah</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted text-center" width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($berlangganans as $index => $item)
                                    <tr wire:key="berlangganan-{{ $item->id }}">
                                        <td class="ps-4">{{ ($berlangganans->currentPage() - 1) * $berlangganans->perPage() + $index + 1 }}</td>
                                        <td><span class="badge bg-light-primary text-primary px-2 fw-bold">{{ $item->nomor ?? '-' }}</span></td>                                        
                                        <td>
                                            @if($item->tgl_dikeluarkan)
                                                {{ \Carbon\Carbon::parse($item->tgl_dikeluarkan)->format('d M Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="fw-bold">{{ $item->nama ?: $item->nama_pemilik }}</td>
                                        <td>{{ $item->no_pol ?? '-' }}</td>
                                        <td>{{ $item->jenis ?? '-' }}</td>
                                        <td>Rp. {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                                        <td class="pe-4 text-center">
                                            <div class="d-flex gap-1 justify-content-center">
                                                <button type="button" class="btn btn-sm btn-icon btn-light-info" title="Detail" 
                                                    wire:click="showDetail('{{ $item->id }}')">
                                                    <i class="ti ti-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-icon btn-light-warning" title="Edit"
                                                    wire:click="editBerlangganan('{{ $item->id }}')">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-icon btn-light-danger"
                                                    wire:confirm="Apakah Anda yakin ingin menghapus data ini?"
                                                    wire:click="deleteBerlangganan('{{ $item->id }}')">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted italic">
                                            <i class="ti ti-database-off fs-1 d-block mb-2"></i>
                                            Belum ada data parkir berlangganan
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 px-4 py-3">
                    {{ $berlangganans->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Components -->
    @livewire('admin::berlangganan.create-berlangganan')
    @livewire('admin::berlangganan.edit-berlangganan')
    @livewire('admin::berlangganan.detail-berlangganan')

    <script>
        document.addEventListener('livewire:initialized', () => {
           @this.on('open-modal', (event) => {
               const modal = new bootstrap.Modal(document.getElementById(event.name));
               modal.show();
           });

           @this.on('close-modal', (event) => {
               const modalElement = document.getElementById(event.name);
               const modal = bootstrap.Modal.getInstance(modalElement);
               if (modal) {
                   modal.hide();
               }
           });
        });
    </script>
</div>
