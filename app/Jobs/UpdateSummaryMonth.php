<?php

namespace App\Jobs;

use App\Models\Jukir;
use App\Models\ParkirBerlangganan;
use App\Models\SummaryJukirMonth;
use App\Models\SummaryMonth;
use App\Models\TransTunai;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateSummaryMonth implements ShouldQueue
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
        $nontunai = DB::table('trans_non_tunai')->selectRaw('bulan, tahun,
                    sum(total_nilai) as nontunai, count(total_nilai) as jml_trx, max(tgl_transaksi) as createdAt')
                    ->where('bulan', $this->bulan)
                    ->where('tahun', $this->tahun)
                    ->where('status', 'SUCCEED')
                    ->groupBy('bulan', 'tahun')
                    ->orderBy('createdAt', 'ASC')->first();

        $jml_trx = $nontunai ? $nontunai->jml_trx : 0;
        $totalNontunai = $nontunai ? $nontunai->nontunai : 0;
        $maxCreatedAt = ($nontunai && $nontunai->createdAt) ? $nontunai->createdAt : now();

        $tunai = TransTunai::whereYear('tgl_transaksi', $this->tahun)
                    ->whereMonth('tgl_transaksi', $this->bulan)
                    ->sum('jumlah_transaksi') ?: 0;

        $berlangganan = ParkirBerlangganan::whereMonth('tgl_dikeluarkan', $this->bulan)
                    ->whereYear('tgl_dikeluarkan', $this->tahun)
                    ->sum('jumlah') ?: 0;

        SummaryMonth::updateOrCreate(
            ['bulan' => $this->bulan, 'tahun' => $this->tahun],
            [
                'tunai' => $tunai,
                'jml_trx' => $jml_trx,
                'non_tunai' => $totalNontunai,
                'berlangganan' => $berlangganan,
                'total' => $tunai + $berlangganan + $totalNontunai,
                'max_createdAt' => $maxCreatedAt
            ]
        );

        info('Update Summary Month Success');
    }
}
