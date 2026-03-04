<?php

use Livewire\Component;
use App\Models\User;
use Livewire\Attributes\On;

new class extends Component
{
    public $userId = null;
    public $name = '';
    public $email = '';
    public $role = '';

    #[On('open-edit-user')]
    public function loadUser($id)
    {
        $this->resetValidation();

        $user = User::findOrFail($id);

        $this->userId = $user->id;
        $this->name   = $user->name;
        $this->email  = $user->email;
        $this->role   = $user->role;
    }

    public function save()
    {
        $this->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'role'  => 'required|in:superadmin,admin,korlap,guest',
        ]);

        User::findOrFail($this->userId)->update([
            'name'  => $this->name,
            'email' => $this->email,
            'role'  => $this->role,
        ]);

        $this->reset(['userId', 'name', 'email', 'role']);
        $this->resetValidation();

        $this->dispatch('refresh-users');
        $this->dispatch('hide-edit-user');
    }
};
?>

<div>
    <div wire:ignore.self class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form wire:submit.prevent="save">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" wire:model="name">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" wire:model="email">
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select" wire:model="role">
                                <option value="">-- Select Role --</option>
                                <option value="superadmin">Superadmin</option>
                                <option value="admin">Admin</option>
                                <option value="korlap">Korlap</option>
                                <option value="guest">Guest</option>
                            </select>
                            @error('role') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">
                            <span wire:loading.remove wire:target="save">Update</span>
                            <span wire:loading wire:target="save">Saving...</span>
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @script
    <script>
        $wire.on('hide-edit-user', () => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('editUserModal'));
            if (modal) modal.hide();
        });
    </script>
    @endscript
</div>
