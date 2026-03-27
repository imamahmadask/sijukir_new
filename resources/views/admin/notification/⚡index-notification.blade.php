<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ReceiveNotifApi;
use Carbon\Carbon;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $jumlahFilter = '';
    public $startDate;
    public $endDate;
    public $perPage = 10;

    public function mount()
    {
        $this->endDate = Carbon::now()->format('Y-m-d');
        $this->startDate = Carbon::now()->subDays(14)->format('Y-m-d');
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingJumlahFilter() { $this->resetPage(); }
    public function updatingStartDate() { $this->resetPage(); }
    public function updatingEndDate() { $this->resetPage(); }
    public function updatingPerPage() { $this->resetPage(); }

    public function with(): array
    {
        return [
            'notifications' => ReceiveNotifApi::query()
                ->with(['jukir.lokasi'])
                ->when($this->search, function ($query) {
                    $query->whereHas('jukir', function($q) {
                        $q->where('nama_jukir', 'like', '%' . $this->search . '%')
                          ->orWhereHas('lokasi', function($qq) {
                              $qq->where('titik_parkir', 'like', '%' . $this->search . '%');
                          });
                    });
                })
                ->when($this->jumlahFilter, function ($query) {
                    $query->where('jumlah', $this->jumlahFilter);
                })
                ->when($this->startDate && $this->endDate, function ($query) {
                    $query->whereBetween('tgl_notif', [
                        $this->startDate . ' 00:00:00',
                        $this->endDate . ' 23:59:59'
                    ]);
                })
                ->orderBy('tgl_notif', 'desc')
                ->paginate($this->perPage)
        ];
    }

    public function render()
    {
        return $this->view()->title('Notifikasi API');
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
                        <h5 class="m-b-10">Notifikasi API</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item" aria-current="page">Notifikasi API</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="row">
        <div class="col-12">
            <div class="card tbl-card">
                <!-- Filters -->
                <div class="card-header bg-transparent border-0 px-4 pb-3 pt-3">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-2">
                             <select class="form-select" wire:model.live="perPage">
                                <option value="10">10 per halaman</option>
                                <option value="25">25 per halaman</option>
                                <option value="50">50 per halaman</option>
                                <option value="100">100 per halaman</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" wire:model.live="jumlahFilter">
                                <option value="">Filter Statis</option>
                                <option value="1000">1.000</option>
                                <option value="2000">2.000</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" wire:model.live="startDate" title="Mulai Tanggal">
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" wire:model.live="endDate" title="Sampai Tanggal">
                        </div>
                        <div class="col-md-4">
                            <div class="input-group border-0 shadow-sm rounded-1">
                                <span class="input-group-text bg-white border-end-0"><i class="ti ti-search text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari Jukir/Lokasi..." wire:model.live.debounce.300ms="search">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">                    
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless mb-0 align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>#</th>
                                    <th>Jukir</th>
                                    <th>Lokasi</th>
                                    <th>Tgl Transaksi</th>
                                    <th>Syslog</th>
                                    <th>Jumlah</th>
                                    <th>Issuer</th>
                                    <th>Sender Name</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($notifications as $index => $item)
                                    <tr wire:key="notif-{{ $item->id }}">
                                        <td>{{ $notifications->firstItem() + $index }}</td>   
                                        <td>
                                            <span class="fw-bold">{{ $item->jukir->nama_jukir }}</span>
                                            <br>
                                            <span class="badge bg-light-primary">{{ $item->merchant->merchant_name }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $item->jukir->lokasi->titik_parkir }}</span>
                                            <br>
                                            <span class="badge bg-light-primary">{{ $item->jukir->lokasi->lokasi_parkir }}</span>
                                        </td>                                     
                                        <td>
                                            <div class="fw-bold">{{ \Carbon\Carbon::parse($item->tgl_notif)->format('d M Y') }}</div>
                                            <div class="small text-muted">{{ \Carbon\Carbon::parse($item->tgl_notif)->format('H:i:s') }}</div>
                                        </td>
                                        <td><code>{{ $item->syslog }}</code></td>
                                        <td><span class="fw-bold text-primary">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</span></td>
                                        <td>{{ $item->issuer_name }}</td>
                                        <td>{{ $item->sender_name }}</td>
                                        <td>
                                            <span class="badge bg-light-{{ $item->status == 0 ? 'success' : ($item->status == 1 ? 'danger' : 'warning') }}">
                                                {{ $item->status == 0 ? 'SUCCESS' : ($item->status == 1 ? 'FAILED' : 'PENDING') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <img src="{{ asset('assets/images/no-data.svg') }}" alt="no data" style="width: 100px;" class="mb-3 d-block mx-auto">
                                            <p class="text-muted">Belum ada data notifikasi</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 px-4 py-3">
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
