<?php

use Livewire\Component;
use App\Models\Korlap;
use Livewire\Attributes\On;

new class extends Component {

    public $korlaps = [];

    public function mount()
    {
        $this->loadKorlaps();
    }

    #[On('refresh-korlaps')]
    public function loadKorlaps()
    {
        $this->korlaps = Korlap::all();
    }

    public function deleteKorlap($id)
    {
        Korlap::findOrFail($id)->delete();
        $this->loadKorlaps();
    }

    public function render()
    {
        return $this->view()->title('Korlap');
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
                        <h5 class="m-b-10">Korlap</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Korlap</a></li>
                        <li class="breadcrumb-item" aria-current="page">Index</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Daftar Korlap</h5>
                @can('manageAdmin')
                    <button type="button" class="btn btn-primary"
                        wire:click="$dispatch('open-create-korlap')"
                        data-bs-toggle="modal" data-bs-target="#createKorlapModal">
                        <i class="ti ti-plus me-1"></i> Tambah Korlap
                    </button>
                @endcan
            </div>

            <div class="card tbl-card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>NIK</th>
                                    <th>Telepon</th>
                                    <th>Status</th>
                                    <th width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($korlaps as $index => $item)
                                    <tr wire:key="korlap-{{ $item->id }}">
                                        <td>{{ $index + 1 }}</td>                                        
                                        <td>{{ $item->nama }}</td>
                                        <td>{{ $item->nik }}</td>
                                        <td>{{ $item->telepon }}</td>
                                        <td>
                                            <span class="badge bg-light-{{ $item->status === 'ASN' ? 'primary' : 'success' }}">
                                                {{ $item->status ?? 'Non ASN' }}
                                            </span>
                                        </td>
                                        <td>
                                            @can('manageAdmin')
                                                <button type="button" class="btn btn-sm btn-warning"
                                                    wire:click="$dispatch('open-edit-korlap', { id: {{ $item->id }} })"
                                                    data-bs-toggle="modal" data-bs-target="#editKorlapModal">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    wire:click="deleteKorlap({{ $item->id }})"
                                                    wire:confirm="Apakah Anda yakin ingin menghapus korlap ini?">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Belum ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Create Modal --}}
    @teleport('body')
        <div wire:ignore>
            <livewire:admin::korlap.create-korlap />
        </div>
    @endteleport

    {{-- Edit Modal --}}
    @teleport('body')
        <div wire:ignore>
            <livewire:admin::korlap.edit-korlap />
        </div>
    @endteleport
</div>
