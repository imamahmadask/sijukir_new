<?php

use Livewire\Component;
use App\Models\TransTunai;
use Livewire\Attributes\On;

new class extends Component {
    public $transactions = [];

    public function mount()
    {
        $this->loadTransactions();
    }

    #[On('refresh-transactions')]
    public function loadTransactions()
    {
        $this->transactions = TransTunai::with(['jukir', 'area'])->orderBy('tgl_transaksi', 'desc')->get();
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
        $this->loadTransactions();
        session()->flash('success', 'Transaksi berhasil dihapus.');
    }

    public function render()
    {
        return $this->view()->title('Daftar Transaksi Tunai');
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
                <div class="card-header bg-transparent border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Daftar Transaksi Tunai</h5>
                    <button type="button" class="btn btn-primary shadow-sm" wire:click="createTransaction">
                        <i class="ti ti-plus me-1"></i> Tambah Transaksi
                    </button>
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
                                        <td class="ps-4">{{ $index + 1 }}</td>
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
                                                <button type="button" class="btn btn-sm btn-icon btn-light-warning" title="Edit"
                                                    wire:click="editTransaction('{{ $item->id }}')">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-icon btn-light-danger"
                                                    wire:confirm="Apakah Anda yakin ingin menghapus transaksi ini?"
                                                    wire:click="deleteTransaction('{{ $item->id }}')">
                                                    <i class="ti ti-trash"></i>
                                                </button>
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
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Components -->
    @livewire('admin::transaksi-tunai.create-transaksi-tunai')
    @livewire('admin::transaksi-tunai.edit-transaksi-tunai')
    @livewire('admin::transaksi-tunai.detail-transaksi-tunai')

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
