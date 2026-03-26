<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TransTunai;
use Livewire\Attributes\On;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;
    public $startDate;
    public $endDate;

    public function mount()
    {
        $this->endDate = \Carbon\Carbon::now()->format('Y-m-d');
        $this->startDate = \Carbon\Carbon::now()->subDays(14)->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function updatingStartDate()
    {
        $this->resetPage();
    }

    public function updatingEndDate()
    {
        $this->resetPage();
    }

    #[On('refresh-transactions')]
    public function refreshTransactions()
    {
        $this->resetPage();
    }

    public function createTransaction()
    {
        $this->dispatch('open-create-transaction')->to('admin::transaksi-tunai.create-transaksi-tunai');
    }

    public function showDetail($id)
    {
        $this->dispatch('show-transaction-detail', id: $id)->to('admin::transaksi-tunai.detail-transaksi-tunai');
    }

    public function editTransaction($id)
    {
        $this->dispatch('open-edit-transaction', id: $id)->to('admin::transaksi-tunai.edit-transaksi-tunai');
    }

    public function deleteTransaction($id)
    {
        TransTunai::findOrFail($id)->delete();
        $this->resetPage();
        session()->flash('success', 'Transaksi berhasil dihapus.');
    }

    #[On('show-alert')]
    public function showAlert($type, $message)
    {
        session()->flash($type, $message);
    }

    public function render()
    {
        $query = TransTunai::with(['jukir', 'area']);

        if ($this->search) {
            $query->where(function($qq) {
                $qq->where('no_kwitansi', 'like', '%' . $this->search . '%')
                   ->orWhereHas('jukir', function($q) {
                       $q->where('nama_jukir', 'like', '%' . $this->search . '%');
                   })
                   ->orWhereHas('area', function($q) {
                       $q->where('Kecamatan', 'like', '%' . $this->search . '%');
                   });
            });
        }

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('tgl_transaksi', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']);
        } elseif ($this->startDate) {
            $query->where('tgl_transaksi', '>=', $this->startDate . ' 00:00:00');
        } elseif ($this->endDate) {
            $query->where('tgl_transaksi', '<=', $this->endDate . ' 23:59:59');
        }

        $transactions = $query->orderBy('tgl_transaksi', 'desc')->paginate($this->perPage);

        return $this->view()->with('transactions', $transactions)->title('Daftar Transaksi Tunai');
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
                        <h5 class="m-b-10">Transaksi Tunai</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Transaksi</a></li>
                        <li class="breadcrumb-item" aria-current="page">Tunai</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 px-4 py-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h5 class="mb-0 fw-bold">Daftar Transaksi Tunai</h5>
                    @can('manageAdmin')
                        <button type="button" class="btn btn-primary shadow-sm text-nowrap" wire:click="createTransaction">
                            <i class="ti ti-plus me-1"></i> Tambah Transaksi
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
                                <option value="100">100 per halaman</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group border-0 shadow-sm rounded-1 d-flex">
                                <input type="date" class="form-control" wire:model.live="startDate" title="Tanggal Awal">
                                <span class="input-group-text bg-white border-start-0 border-end-0 text-muted">s/d</span>
                                <input type="date" class="form-control" wire:model.live="endDate" title="Tanggal Akhir">
                            </div>
                        </div>
                        <div class="col-md-4 ms-auto">
                            <div class="input-group border-0 shadow-sm rounded-1">
                                <span class="input-group-text bg-white border-end-0"><i class="ti ti-search text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari transaksi..." wire:model.live.debounce.300ms="search">
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
                                    <th class="py-3 text-uppercase small fw-bold text-muted">No Kwitansi</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted">Jukir</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted">Area</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted">Jumlah</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted">Jenis</th>
                                    <th class="pe-4 py-3 text-uppercase small fw-bold text-muted" width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transactions as $index => $item)
                                    <tr wire:key="transaction-{{ $item->id }}">
                                        <td class="ps-4">{{ $transactions->firstItem() + $index }}</td>
                                        <td>
                                            <span class="fw-bold">{{ $item->no_kwitansi }}</span>
                                            <br>
                                            <span class="text-muted small fst-italic">
                                                {{ date('d/m/Y', strtotime($item->tgl_transaksi)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $item->jukir->nama_jukir ?? '-' }}</span>
                                            <br>
                                            <span class="text-muted small fst-italic">
                                                {{ $item->jukir->lokasi->titik_parkir ?? '-' }}
                                            </span>
                                        </td>
                                        <td><i class="ti ti-map-pin text-muted me-1 small"></i>{{ $item->area->Kecamatan ?? '-' }}</td>
                                        <td>Rp {{ number_format($item->jumlah_transaksi, 0, ',', '.') }}</td>
                                        <td><span class="badge bg-light-info text-info px-2">{{ $item->type ?? 'Normal' }}</span></td>
                                        <td class="pe-4">
                                            <div class="d-flex gap-1">
                                                <button type="button" class="btn btn-sm btn-icon btn-light-info" title="Detail" 
                                                    wire:click="showDetail('{{ $item->id }}')">
                                                    <i class="ti ti-eye"></i>
                                                </button>
                                                @can('manageAdmin')
                                                    <button type="button" class="btn btn-sm btn-icon btn-light-warning" title="Edit"
                                                        wire:click="editTransaction('{{ $item->id }}')">
                                                        <i class="ti ti-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-icon btn-light-danger"
                                                        wire:confirm="Apakah Anda yakin ingin menghapus transaksi ini?"
                                                        wire:click="deleteTransaction('{{ $item->id }}')">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted italic">
                                            <i class="ti ti-database-off fs-1 d-block mb-2"></i>
                                            Belum ada data transaksi
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-4 py-3 border-top">
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Components -->
    @livewire('admin::transaksi-tunai.create-transaksi-tunai')
    @livewire('admin::transaksi-tunai.edit-transaksi-tunai')
    @livewire('admin::transaksi-tunai.detail-transaksi-tunai')

    <script>
        function initializeAlerts() {
            document.querySelectorAll('.alert-dismissible').forEach(function (alert) {
                if (!alert.dataset.initialized) {
                    alert.dataset.initialized = 'true';
                    setTimeout(function () {
                        var bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                        bsAlert.close();
                    }, 3000);
                }
            });
        }

        document.addEventListener('DOMContentLoaded', initializeAlerts);
        
        document.addEventListener('livewire:initialized', () => {
            Livewire.hook('morph.updated', ({ el, component }) => {
                initializeAlerts();
            });
            
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
