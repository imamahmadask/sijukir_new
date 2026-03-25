<?php

namespace App\Jobs;

use App\Models\Korlap;
use App\Models\SummaryKorlap2;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateSummaryKorlap2 implements ShouldQueue
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
        $i = $this->bulan;
        $korlaps = Korlap::select(
                'korlaps.id as id',
                'korlaps.nama as Korlap',
                'areas.Kecamatan as Kecamatan',
                DB::raw("COUNT(DISTINCT CASE WHEN jukirs.ket_jukir = 'Active' THEN jukirs.id END) as jumlah_jukir"),
                DB::raw('SUM(CASE WHEN uji_petik > 0 THEN uji_petik ELSE potensi_harian END) as total_potensi_harian'),
                DB::raw('SUM(CASE WHEN potensi_bulanan_upl > 0 THEN potensi_bulanan_upl ELSE potensi_bulanan END) as total_potensi_bulanan'),
                DB::raw('SUM(COALESCE(summary_jukir_month.non_tunai, 0)) as total_pendapatan')
            )
            ->join('lokasis', 'korlaps.id', '=', 'lokasis.korlap_id')
            ->join('areas', 'lokasis.area_id', '=', 'areas.id')
            ->join('jukirs', 'lokasis.id', '=', 'jukirs.lokasi_id')
            ->leftJoin('summary_jukir_month', function($join) use ($i) {
                $join->on('summary_jukir_month.jukir_id', '=', 'jukirs.id')
                     ->where('summary_jukir_month.bulan', $i)
                     ->where('summary_jukir_month.tahun', $this->tahun);
            })
            ->groupBy('korlaps.id', 'korlaps.nama', 'areas.Kecamatan')
            ->orderByDesc('total_pendapatan')
            ->get();

        foreach ($korlaps as $korlap) {
            $potensiBulanan = $korlap->total_potensi_bulanan ?: 0;
            $pendapatan = $korlap->total_pendapatan ?: 0;
            $ach = ($potensiBulanan > 0) ? ($pendapatan / $potensiBulanan * 100) : 0;

            SummaryKorlap2::updateOrCreate(
                [
                    'korlap_id' => $korlap->id,
                    'tahun' => $this->tahun,
                    'bulan' => $i,
                ],
                [
                    'jml_jukir' => $korlap->jumlah_jukir ?: 0,
                    'potensi_harian' => $korlap->total_potensi_harian ?: 0,
                    'potensi_bulanan' => $potensiBulanan,
                    'pencapaian' => $pendapatan,
                    'ach' => $ach,
                ]
            );
        }
    }
}
