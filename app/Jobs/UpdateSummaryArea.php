<?php

namespace App\Jobs;

use App\Models\Area;
use App\Models\Jukir;
use App\Models\SummaryAreaMonth;
use App\Models\SummaryByArea;
use App\Models\TransNonTunai;
use App\Models\TransTunai;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateSummaryArea implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tahun;
    protected $bulan;

    public function __construct($tahun, $bulan)
    {
        $this->tahun = $tahun;
        $this->bulan = $bulan;
    }

    public function handle(): void
    {
        $areas = Area::all();

        // 1. Parse date correctly utilizing $this->tahun and $this->bulan
        $awal = Carbon::create(2021, 1, 1)->startOfMonth();
        $akhir = Carbon::create($this->tahun, $this->bulan, 1)->endOfMonth();

        foreach ($areas as $data) {
            // A. Compute Monthly Metrics
            $potensi = Jukir::where('area_id', $data->id)
                ->whereBetween('tgl_perjanjian', [$awal, $akhir])
                ->where('ket_jukir', '!=', 'Non Active')
                ->sum(DB::raw('IF(potensi_bulanan_upl > 0, potensi_bulanan_upl, potensi_bulanan)'));

            // Fixed bug: TransTunai -> TransNonTunai and standardized tgl_transaksi filtering
            $nontunai = TransNonTunai::where('area_id', $data->id)
                ->whereYear('tgl_transaksi', $this->tahun)
                ->whereMonth('tgl_transaksi', $this->bulan)
                ->where('status', 'SUCCEED')
                ->sum('total_nilai');

            $jml_trx = TransNonTunai::where('area_id', $data->id)
                ->whereYear('tgl_transaksi', $this->tahun)
                ->whereMonth('tgl_transaksi', $this->bulan)
                ->where('status', 'SUCCEED')
                ->count();

            $tunai = TransTunai::where('area_id', $data->id)
                ->whereYear('tgl_transaksi', $this->tahun)
                ->whereMonth('tgl_transaksi', $this->bulan)
                ->sum('jumlah_transaksi');

            // Save Monthly Summary Data
            SummaryAreaMonth::updateOrCreate(
                ['area_id' => $data->id, 'bulan' => $this->bulan, 'tahun' => $this->tahun],
                [
                    'potensi' => $potensi,
                    'tunai' => $tunai,
                    'jml_trx' => $jml_trx,
                    'non_tunai' => $nontunai,
                    'total' => ($tunai + $nontunai),
                    'kurang_setor' => $nontunai - $potensi
                ]
            );

            // B. Compute Yearly Metrics (Optimized to run 1 query instead of 3)
            $yearlyStats = SummaryAreaMonth::where('area_id', $data->id)
                ->where('tahun', $this->tahun)
                ->selectRaw('SUM(jml_trx) as total_trx, SUM(non_tunai) as total_nontunai, SUM(tunai) as total_tunai')
                ->first();

            $totalTunai = $yearlyStats->total_tunai ?? 0;
            $totalNonTunai = $yearlyStats->total_nontunai ?? 0;
            $totalTrx = $yearlyStats->total_trx ?? 0;

            $potensiYearly = Jukir::where('area_id', $data->id)
                ->whereYear('tgl_perjanjian', '<=', $this->tahun)
                ->where('ket_jukir', '!=', 'Non Active')
                ->sum(DB::raw('IF(potensi_bulanan_upl > 0, potensi_bulanan_upl, potensi_bulanan)'));

            $jukirsCount = Jukir::join('lokasis', 'lokasis.id', '=', 'jukirs.lokasi_id')
                ->where('lokasis.area_id', $data->id)
                ->whereYear('jukirs.tgl_perjanjian', '<=', $this->tahun)
                ->where('jukirs.ket_jukir', '!=', 'Non Active')
                ->count('jukirs.id');

            // Save Yearly Summary Data
            SummaryByArea::updateOrCreate(
                ['area_id' => $data->id, 'tahun' => $this->tahun], 
                [
                    'area' => $data->Kecamatan,
                    'potensi' => $potensiYearly,
                    'tunai' => $totalTunai,
                    'non_tunai' => $totalNonTunai,
                    'jml_trx' => $totalTrx,
                    'total' => ($totalTunai + $totalNonTunai),
                    'jukirs' => $jukirsCount,
                ]
            );
        }

        info('Update Summary Area Success');
    }
}
