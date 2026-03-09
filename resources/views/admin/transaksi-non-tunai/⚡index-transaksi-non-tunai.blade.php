<?php

use Livewire\Component;
use App\Models\TransNonTunai;
use Livewire\Attributes\On;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    #[On('refresh-transactions')]
    public function render()
    {
        $transactions = TransNonTunai::with(['merchant', 'area'])
            ->where(function($query) {
                $query->where('merchant_name', 'like', '%' . $this->search . '%')
                    ->orWhere('merchant_id', 'like', '%' . $this->search . '%')
                    ->orWhere('issuer_name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('tgl_transaksi', 'desc')
            ->paginate($this->perPage);

        return $this->view()->title('Daftar Transaksi Non-Tunai')->with([
            'transactions' => $transactions
        ]);
    }

    public function showDetail($id)
    {
        $this->dispatch('show-non-tunai-detail', id: $id)->to('admin::transaksi-non-tunai.detail-transaksi-non-tunai');
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
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary shadow-sm" wire:click="openImport">
                            <i class="ti ti-upload me-1"></i> Import Excel
                        </button>
                    </div>
                </div>
                <div class="px-4 pb-3">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="ti ti-search text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari Merchant atau ID..." wire:model.live.debounce.300ms="search">
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
                                    <th class="py-3 text-uppercase small fw-bold text-muted">Tanggal</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted">Merchant</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted">Issuer</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted text-end">Total Nilai</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted text-center">Status</th>
                                    <th class="pe-4 py-3 text-uppercase small fw-bold text-muted text-center" width="120">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transactions as $index => $item)
                                    <tr wire:key="transaction-{{ $item->id }}">
                                        <td class="ps-4">{{ ($transactions->currentPage() - 1) * $transactions->perPage() + $index + 1 }}</td>
                                        <td>
                                            <span class="fw-bold">{{ date('d/m/Y', strtotime($item->tgl_transaksi)) }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold d-block">{{ $item->merchant_name ?? '-' }}</span>
                                            <small class="text-muted">{{ $item->merchant_id }}</small>
                                        </td>
                                        <td>{{ $item->issuer_name ?? '-' }}</td>
                                        <td class="text-end fw-bold text-primary">Rp {{ number_format($item->total_nilai, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            @if($item->status == 'Settlement' || $item->status == 'Success')
                                                <span class="badge bg-light-success text-success px-2">{{ $item->status }}</span>
                                            @else
                                                <span class="badge bg-light-warning text-warning px-2">{{ $item->status ?? 'Pending' }}</span>
                                            @endif
                                        </td>
                                        <td class="pe-4 text-center">
                                            <div class="d-flex gap-1 justify-content-center">
                                                <button type="button" class="btn btn-sm btn-icon btn-light-info" title="Detail" 
                                                    wire:click="showDetail('{{ $item->id }}')">
                                                    <i class="ti ti-eye"></i>
                                                </button>
                                            </div>
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
