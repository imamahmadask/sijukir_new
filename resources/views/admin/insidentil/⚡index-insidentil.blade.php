<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Insidentil;
use Livewire\Attributes\On;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingPerPage() { $this->resetPage(); }

    #[On('refresh-insidentil')]
    public function render()
    {
        $query = Insidentil::when($this->search, function($q) {
            $q->where(function($qq) {
                $qq->where('nama', 'like', '%' . $this->search . '%')
                   ->orWhere('nama_acara', 'like', '%' . $this->search . '%')
                   ->orWhere('nik', 'like', '%' . $this->search . '%')
                   ->orWhere('no_surat', 'like', '%' . $this->search . '%');
            });
        });

        $insidentils = $query->latest('id')->paginate($this->perPage);

        return $this->view()->title('Daftar Insidentil')->with('insidentils', $insidentils);
    }

    public function create() { $this->dispatch('open-create-insidentil'); }
    public function detail($id) { $this->dispatch('show-insidentil-detail', id: $id); }
    public function edit($id) { $this->dispatch('open-edit-insidentil', id: $id); }

    public function delete($id)
    {
        Insidentil::findOrFail($id)->delete();
        session()->flash('success', 'Data Insidentil berhasil dihapus.');
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
                        <h5 class="m-b-10">Insidentil</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Insidentil</a></li>
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
                    <h5 class="mb-0 fw-bold">Daftar Insidentil</h5>
                    @can('manageAdmin')
                        <button type="button" class="btn btn-primary shadow-sm" wire:click="create">
                            <i class="ti ti-plus me-1"></i> Tambah Insidentil
                        </button>
                    @endcan
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
                        <div class="col-md-4 ms-auto">
                            <div class="input-group border-0 shadow-sm rounded-1">
                                <span class="input-group-text bg-white border-end-0"><i class="ti ti-search text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari Nama / Acara ..." wire:model.live.debounce.300ms="search">
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
                                    <th class="py-3 text-uppercase small fw-bold text-muted">No Surat</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted">Nama</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted">Acara</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted">Lokasi</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted">Tanggal Acara</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted">Jenis</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted text-center" width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($insidentils as $index => $item)
                                    <tr wire:key="insidentil-{{ $item->id }}">
                                        <td class="ps-4">{{ ($insidentils->currentPage() - 1) * $insidentils->perPage() + $index + 1 }}</td>
                                        <td><span class="badge bg-light-primary text-primary px-2">{{ $item->no_surat ?? '-' }}</span></td>
                                        <td class="fw-bold">
                                            {{ $item->nama }}
                                            <br>
                                            <span class="small text-muted fst-italic">{{ $item->nama_perusahaan ?? '-' }}</span>
                                        </td>
                                        <td>{{ $item->nama_acara ?? '-' }}</td>
                                        <td>{{ $item->lokasi_acara ?? '-' }}</td>
                                        <td>
                                            @if($item->tgl_awal_acara)
                                                {{ \Carbon\Carbon::parse($item->tgl_awal_acara)->format('d M y') }}
                                                s/d
                                                {{ $item->tgl_akhir_acara ? \Carbon\Carbon::parse($item->tgl_akhir_acara)->format('d M y') : '-' }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            {{ $item->jenis_izin ?? '-' }}
                                        </td>
                                        <td class="pe-4 text-center">
                                            <div class="d-flex gap-1 justify-content-center">
                                                <button type="button" class="btn btn-sm btn-icon btn-light-info" title="Detail" 
                                                    wire:click="detail('{{ $item->id }}')">
                                                    <i class="ti ti-eye"></i>
                                                </button>
                                                @can('manageAdmin')
                                                    <button type="button" class="btn btn-sm btn-icon btn-light-warning" title="Edit"
                                                        wire:click="edit('{{ $item->id }}')">
                                                        <i class="ti ti-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-icon btn-light-danger"
                                                        wire:confirm="Apakah Anda yakin ingin menghapus data ini?"
                                                        wire:click="delete('{{ $item->id }}')">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted fst-italic">
                                            <i class="ti ti-database-off fs-1 d-block mb-2"></i>
                                            Belum ada data insidentil
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 px-4 py-3">
                    {{ $insidentils->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Components -->
    @livewire('admin::insidentil.create-insidentil')
    @livewire('admin::insidentil.edit-insidentil')
    @livewire('admin::insidentil.detail-insidentil')

    <script>
        document.addEventListener('livewire:initialized', () => {
           @this.on('open-modal', (event) => {
               const modalName = event.name || event[0]?.name;
               const modal = new bootstrap.Modal(document.getElementById(modalName));
               modal.show();
           });

           @this.on('close-modal', (event) => {
               const modalName = event.name || event[0]?.name;
               const modalElement = document.getElementById(modalName);
               if(modalElement) {
                   const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
                   modal.hide();
               }
               // Clean up backdrop if stuck
               document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
               document.body.classList.remove('modal-open');
               document.body.style.overflow = '';
               document.body.style.paddingRight = '';
           });
        });
    </script>
</div>
