<?php

use Livewire\Component;
use App\Models\Merchant;
use Livewire\Attributes\On;

new class extends Component {
    public $merchants = [];

    public function mount()
    {
        $this->loadMerchants();
    }

    #[On('refresh-merchants')]
    public function loadMerchants()
    {
        $this->merchants = Merchant::with(['area'])->get();
    }

    public function createMerchant()
    {
        $this->dispatch('open-create-merchant')->to('admin::merchant.create-merchant');
    }

    public function showDetail($id)
    {
        $this->dispatch('show-merchant-detail', id: $id)->to('admin::merchant.detail-merchant');
    }

    public function editMerchant($id)
    {
        $this->dispatch('open-edit-merchant', id: $id)->to('admin::merchant.edit-merchant');
    }

    public function deleteMerchant($id)
    {
        Merchant::findOrFail($id)->delete();
        $this->loadMerchants();
        session()->flash('success', 'Merchant berhasil dihapus.');
    }

    public function render()
    {
        return $this->view()->title('Daftar Merchant');
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
                        <h5 class="m-b-10">Merchant</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Merchant</a></li>
                        <li class="breadcrumb-item" aria-current="page">Dashboard</li>
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
                    <h5 class="mb-0 fw-bold">Daftar Merchant</h5>
                    <button type="button" class="btn btn-primary shadow-sm" wire:click="createMerchant">
                        <i class="ti ti-plus me-1"></i> Tambah Merchant
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless mb-0 align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">#</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted">ID Merchant</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted">Nama Merchant</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted">Vendor</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted">Area</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted">Tgl Terdaftar</th>
                                    <th class="pe-4 py-3 text-uppercase small fw-bold text-muted" width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($merchants as $index => $item)
                                    <tr wire:key="merchant-{{ $item->id }}">
                                        <td class="ps-4">{{ $index + 1 }}</td>
                                        <td><span class="badge bg-light-primary text-primary px-2 fw-bold">{{ $item->id }}</span></td>
                                        <td class="fw-bold">{{ $item->merchant_name }}</td>
                                        <td>{{ $item->vendor }}</td>
                                        <td><i class="ti ti-map-pin text-muted me-1 small"></i>{{ $item->area->Kecamatan ?? '-' }}</td>
                                        <td>{{ $item->tgl_terdaftar ? date('d/m/Y', strtotime($item->tgl_terdaftar)) : '-' }}</td>
                                        <td class="pe-4">
                                            <div class="d-flex gap-1">
                                                <button type="button" class="btn btn-sm btn-icon btn-light-info" title="Detail" 
                                                    wire:click="showDetail('{{ $item->id }}')">
                                                    <i class="ti ti-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-icon btn-light-warning" title="Edit"
                                                    wire:click="editMerchant('{{ $item->id }}')">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-icon btn-light-danger"
                                                    wire:confirm="Apakah Anda yakin ingin menghapus merchant ini?"
                                                    wire:click="deleteMerchant('{{ $item->id }}')">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted italic">
                                            <i class="ti ti-database-off fs-1 d-block mb-2"></i>
                                            Belum ada data merchant
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
    @livewire('admin::merchant.create-merchant')
    @livewire('admin::merchant.edit-merchant')
    @livewire('admin::merchant.detail-merchant')

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
