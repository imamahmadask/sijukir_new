<?php

use Livewire\Component;
use App\Models\User;
use Livewire\Attributes\On;

new class extends Component {

    public $users = [];

    public function mount()
    {
        $this->loadUsers();
    }

    #[On('refresh-users')]
    public function loadUsers()
    {
        $this->users = User::all();
    }

    public function deleteUser($id)
    {
        User::findOrFail($id)->delete();
        $this->loadUsers();
    }

    public function render()
    {
        return $this->view()->title('Users');
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
                        <h5 class="m-b-10">Users</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Users</a></li>
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
                <h5 class="mb-0">List Users</h5>
                <button type="button" class="btn btn-primary"
                    wire:click="$dispatch('open-create-user')"
                    data-bs-toggle="modal" data-bs-target="#createUserModal">
                    <i class="ti ti-plus me-1"></i> Add User
                </button>
            </div>

            <div class="card tbl-card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th width="150">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $index => $item)
                                    <tr wire:key="user-{{ $item->id }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->email }}</td>
                                        <td>
                                            <span class="badge bg-light-{{ $item->role === 'superadmin' ? 'primary' : ($item->role === 'admin' ? 'success' : ($item->role === 'korlap' ? 'warning' : 'secondary')) }}">
                                                {{ ucfirst($item->role) }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-warning"
                                                wire:click="$dispatch('open-edit-user', { id: {{ $item->id }} })"
                                                data-bs-toggle="modal" data-bs-target="#editUserModal">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                wire:click="deleteUser({{ $item->id }})"
                                                wire:confirm="Are you sure you want to delete this user?">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada data</td>
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
            <livewire:admin::users.create-user />
        </div>
    @endteleport

    {{-- Edit Modal --}}
    @teleport('body')
        <div wire:ignore>
            <livewire:admin::users.edit-user />
        </div>
    @endteleport
</div>