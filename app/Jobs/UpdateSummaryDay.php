<?php

namespace App\Jobs;

use App\Models\SummaryDay;
use App\Models\TransNonTunai;
use App\Models\TransTunai;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;

class UpdateSummaryDay implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tahun;
    protected $bulan;

    /**
     * Create a new job instance.
     */
    public function __construct($tahun, $bulan)
    {
        $this->tahun = $tahun;
        $this->bulan = $bulan;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 1. Data Transaksi Non-Tunai per hari
        $nonTunai = TransNonTunai::whereYear('tgl_transaksi', $this->tahun)
            ->whereMonth('tgl_transaksi', $this->bulan)
            ->where('status', 'SUCCEED')
            ->selectRaw('DATE(tgl_transaksi) as tanggal, COUNT(*) as jml_trx, SUM(total_nilai) as total, COUNT(DISTINCT merchant_name) as jml_jukir')
            ->groupBy('tanggal')
            ->get()
            ->keyBy('tanggal');

        // 2. Data Transaksi Tunai per hari
        $tunai = TransTunai::whereYear('tgl_transaksi', $this->tahun)
            ->whereMonth('tgl_transaksi', $this->bulan)
            ->selectRaw('DATE(tgl_transaksi) as tanggal, COUNT(*) as jml_trx, SUM(jumlah_transaksi) as total, COUNT(DISTINCT jukir_id) as jml_jukir')
            ->groupBy('tanggal')
            ->get()
            ->keyBy('tanggal');

        // 3. Gabungkan semua tanggal (hanya tanggal yang ada transaksinya)
        $dates = collect($nonTunai->keys())->merge($tunai->keys())->unique();

        foreach ($dates as $date) {
            $nt = $nonTunai->get($date);
            $t = $tunai->get($date);

            $totalTrx = ($nt->jml_trx ?? 0) + ($t->jml_trx ?? 0);
            $totalNilai = ($nt->total ?? 0) + ($t->total ?? 0);
            
            // Estimasi total jukir per hari dari kedua jenis transaksi
            $totalJukir = ($nt->jml_jukir ?? 0) + ($t->jml_jukir ?? 0);

            SummaryDay::updateOrCreate(
                ['tanggal' => $date],
                [
                    'jml_transaksi' => $totalTrx,
                    'jml_jukir'     => $totalJukir,
                    'total'         => $totalNilai,
                    'average_trx'   => $totalTrx > 0 ? round($totalNilai / $totalTrx) : 0,
                ]
            );
        }

        info('Update Summary Day Success');
    }
}
