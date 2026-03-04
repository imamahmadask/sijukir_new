<?php

use Livewire\Component;
use App\Models\Korlap;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public $nama = '';
    public $nik = '';
    public $alamat = '';
    public $telepon = '';
    public $status = '';
    public $foto;

    #[On('open-create-korlap')]
    public function resetFields()
    {
        $this->reset(['nama', 'nik', 'alamat', 'telepon', 'status', 'foto']);
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate([
            'nama'    => 'required|string|max:255',
            'nik'     => 'required|string|max:20|unique:korlaps,nik',
            'alamat'  => 'required|string',
            'telepon' => 'required|string|max:20',
            'status'  => 'required|in:ASN,Non ASN',
            'foto'    => 'nullable|image|max:1024', // Max 1MB
        ]);

        $fotoPath = null;
        if ($this->foto) {
            $fotoPath = $this->foto->store('korlaps', 'public');
        }

        Korlap::create([
            'nama'       => $this->nama,
            'nik'        => $this->nik,
            'alamat'     => $this->alamat,
            'telepon'    => $this->telepon,
            'status'     => $this->status,
            'foto'       => $fotoPath,
            'created_by' => auth()->user()->name,
            'edited_by'  => auth()->user()->name,
        ]);

        $this->reset(['nama', 'nik', 'alamat', 'telepon', 'status', 'foto']);
        $this->resetValidation();

        $this->dispatch('refresh-korlaps');
        $this->dispatch('hide-create-korlap');
    }
};
?>

<div>
    <div wire:ignore.self class="modal fade" id="createKorlapModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form wire:submit.prevent="save">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Korlap</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" class="form-control" wire:model="nama">
                            @error('nama') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">NIK</label>
                            <input type="text" class="form-control" wire:model="nik">
                            @error('nik') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea class="form-control" wire:model="alamat"></textarea>
                            @error('alamat') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Telepon</label>
                            <input type="text" class="form-control" wire:model="telepon">
                            @error('telepon') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" wire:model="status">
                                <option value="">-- Pilih Status --</option>
                                <option value="ASN">ASN</option>
                                <option value="Non ASN">Non ASN</option>
                            </select>
                            @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Foto</label>
                            <input type="file" class="form-control" wire:model="foto">
                            @error('foto') <small class="text-danger">{{ $message }}</small> @enderror
                            
                            @if ($foto)
                                <div class="mt-2">
                                    <img src="{{ $foto->temporaryUrl() }}" class="img-thumbnail" style="height: 150px;">
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">
                            <span wire:loading.remove wire:target="save">Simpan</span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @script
    <script>
        $wire.on('hide-create-korlap', () => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('createKorlapModal'));
            if (modal) modal.hide();
        });
    </script>
    @endscript
</div>
