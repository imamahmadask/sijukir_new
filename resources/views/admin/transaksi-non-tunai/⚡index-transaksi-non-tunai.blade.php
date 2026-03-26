<?php

use Livewire\Component;
use App\Models\TransNonTunai;
use Livewire\Attributes\On;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $startDate = '';
    public $endDate = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->endDate = date('Y-m-d');
        $this->startDate = date('Y-m-d', strtotime('-14 days'));
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['search', 'startDate', 'endDate'])) {
            $this->resetPage();
        }
    }

    #[On('refresh-transactions')]
    public function render()
    {
        $transactions = TransNonTunai::select(
                'merchant_id',
                'merchant_name',
                \Illuminate\Support\Facades\DB::raw('MAX(id) as last_trans_id'),
                \Illuminate\Support\Facades\DB::raw('COUNT(*) as total_transaksi'),
                \Illuminate\Support\Facades\DB::raw('SUM(total_nilai) as total_nilai_sum'),
                \Illuminate\Support\Facades\DB::raw('MAX(tgl_transaksi) as last_date')
            )
            ->where(function($query) {
                $query->where('merchant_name', 'like', '%' . $this->search . '%')
                    ->orWhere('merchant_id', 'like', '%' . $this->search . '%')
                    ->orWhere('issuer_name', 'like', '%' . $this->search . '%');
            })
            ->when($this->startDate, function($query) {
                $query->whereDate('tgl_transaksi', '>=', $this->startDate);
            })
            ->when($this->endDate, function($query) {
                $query->whereDate('tgl_transaksi', '<=', $this->endDate);
            })
            ->groupBy('merchant_id', 'merchant_name')
            ->orderBy('total_nilai_sum', 'desc')
            ->paginate($this->perPage);

        return $this->view()->title('Rekap Transaksi Non-Tunai')->with([
            'transactions' => $transactions
        ]);
    }

    public function showDetail($merchantId)
    {
        $this->dispatch('show-non-tunai-detail', 
            merchantId: $merchantId, 
            startDate: $this->startDate, 
            endDate: $this->endDate
        )->to('admin::transaksi-non-tunai.detail-transaksi-non-tunai');
    }

    public function openImport()
    {
        $this->dispatch('open-import-modal')->to('admin::transaksi-non-tunai.import-transaksi-non-tunai');
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
                        <h5 class="m-b-10">Transaksi Non-Tunai</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Transaksi</a></li>
                        <li class="breadcrumb-item" aria-current="page">Non-Tunai</li>
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

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h5 class="mb-0 fw-bold">Daftar Transaksi Non-Tunai</h5>
                    @can('manageAdmin')
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-primary shadow-sm" wire:click="openImport">
                                <i class="ti ti-upload me-1"></i> Import Excel
                            </button>
                        </div>
                    @endcan
                </div>
                <div class="px-4 pb-3">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-5">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0"><i class="ti ti-search text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari Merchant atau ID..." wire:model.live.debounce.300ms="search">
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="d-flex align-items-center gap-2">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white text-muted small">Dari</span>
                                    <input type="date" class="form-control" wire:model.live="startDate">
                                </div>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white text-muted small">Sampai</span>
                                    <input type="date" class="form-control" wire:model.live="endDate">
                                </div>
                                @if($startDate || $endDate)
                                    <button class="btn btn-sm btn-light-danger shadow-none" wire:click="$set('startDate', ''); $set('endDate', '')" title="Reset Filter">
                                        <i class="ti ti-refresh"></i>
                                    </button>
                                @endif
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
                                    <th class="py-3 text-uppercase small fw-bold text-muted">Merchant</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted text-center">Jml Transaksi</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted text-end">Total Nilai</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted text-center">Update Terakhir</th>
                                    <th class="pe-4 py-3 text-uppercase small fw-bold text-muted text-center" width="80">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transactions as $index => $item)
                                    <tr wire:key="merchant-{{ $item->merchant_id }}">
                                        <td class="ps-4">{{ ($transactions->currentPage() - 1) * $transactions->perPage() + $index + 1 }}</td>
                                        <td>
                                            <span class="fw-bold d-block text-dark">{{ $item->merchant_name ?? '-' }}</span>
                                            <small class="text-muted">{{ $item->merchant_id }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-primary border border-primary border-opacity-10">{{ $item->total_transaksi }} Transaksi</span>
                                        </td>
                                        <td class="text-end fw-bold text-primary">Rp {{ number_format($item->total_nilai_sum, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <span class="small text-muted">{{ date('d/m/Y', strtotime($item->last_date)) }}</span>
                                        </td>
                                        <td class="pe-4 text-center">
                                            <button type="button" class="btn btn-sm btn-icon btn-light-info" title="Lihat Rincian" 
                                                wire:click="showDetail('{{ $item->merchant_id }}')">
                                                <i class="ti ti-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted fst-italic">
                                            <i class="ti ti-database-off fs-1 d-block mb-2"></i>
                                            Belum ada data transaksi non-tunai
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 px-4 py-3">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Components -->
    @livewire('admin::transaksi-non-tunai.detail-transaksi-non-tunai')
    @livewire('admin::transaksi-non-tunai.import-transaksi-non-tunai')

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
