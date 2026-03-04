<?php

use Livewire\Component;
use App\Models\Korlap;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

new class extends Component
{
    use WithFileUploads;

    public $korlapId = null;
    public $nama = '';
    public $nik = '';
    public $alamat = '';
    public $telepon = '';
    public $status = '';
    public $foto;
    public $oldFoto;

    #[On('open-edit-korlap')]
    public function loadKorlap($id)
    {
        $this->resetValidation();
        $this->reset('foto');

        $korlap = Korlap::findOrFail($id);

        $this->korlapId = $korlap->id;
        $this->nama     = $korlap->nama;
        $this->nik      = $korlap->nik;
        $this->alamat   = $korlap->alamat;
        $this->telepon  = $korlap->telepon;
        $this->status   = $korlap->status;
        $this->oldFoto  = $korlap->foto;
    }

    public function save()
    {
        $this->validate([
            'nama'    => 'required|string|max:255',
            'nik'     => 'required|string|max:20|unique:korlaps,nik,' . $this->korlapId,
            'alamat'  => 'required|string',
            'telepon' => 'required|string|max:20',
            'status'  => 'required|in:ASN,Non ASN',
            'foto'    => 'nullable|image|max:1024',
        ]);

        $korlap = Korlap::findOrFail($this->korlapId);
        
        $data = [
            'nama'      => $this->nama,
            'nik'       => $this->nik,
            'alamat'    => $this->alamat,
            'telepon'   => $this->telepon,
            'status'    => $this->status,
            'edited_by' => auth()->user()->name,
        ];

        if ($this->foto) {
            // Delete old photo
            if ($korlap->foto) {
                Storage::disk('public')->delete($korlap->foto);
            }
            $data['foto'] = $this->foto->store('korlaps', 'public');
        }

        $korlap->update($data);

        $this->reset(['korlapId', 'nama', 'nik', 'alamat', 'telepon', 'status', 'foto', 'oldFoto']);
        $this->resetValidation();

        $this->dispatch('refresh-korlaps');
        $this->dispatch('hide-edit-korlap');
    }
};
?>

<div>
    <div wire:ignore.self class="modal fade" id="editKorlapModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form wire:submit.prevent="save">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Korlap</h5>
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
                            
                            <div class="mt-2">
                                @if ($foto)
                                    <img src="{{ $foto->temporaryUrl() }}" class="img-thumbnail" style="height: 150px;">
                                @elseif ($oldFoto)
                                    <img src="{{ asset('storage/' . $oldFoto) }}" class="img-thumbnail" style="height: 150px;">
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">
                            <span wire:loading.remove wire:target="save">Update</span>
                            <span wire:loading wire:target="save">Updating...</span>
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @script
    <script>
        $wire.on('hide-edit-korlap', () => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('editKorlapModal'));
            if (modal) modal.hide();
        });
    </script>
    @endscript
</div>
