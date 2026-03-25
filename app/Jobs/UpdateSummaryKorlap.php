<?php

namespace App\Jobs;

use App\Models\Jukir;
use App\Models\SummaryKorlap;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateSummaryKorlap implements ShouldQueue
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
        $datas = Jukir::select(
            'korlaps.id as korlap',
            'areas.Kecamatan as wilayah',
            DB::raw("COUNT(summary_jukir_month.jukir_id) as Jml_Jukir"),
            DB::raw("COUNT(CASE
                        WHEN (summary_jukir_month.non_tunai + COALESCE(summary_jukir_month.kompensasi, 0)) >= summary_jukir_month.potensi
                        THEN 1
                     END) as Hijau"),
            DB::raw("COUNT(CASE
                        WHEN (summary_jukir_month.non_tunai + COALESCE(summary_jukir_month.kompensasi, 0)) < summary_jukir_month.potensi
                             AND summary_jukir_month.non_tunai > 0
                        THEN 1
                     END) as Kuning"),
            DB::raw("COUNT(CASE
                        WHEN (summary_jukir_month.non_tunai + COALESCE(summary_jukir_month.kompensasi, 0)) = 0
                        THEN 1
                     END) as Merah"),
            DB::raw("(COUNT(CASE
                        WHEN (summary_jukir_month.non_tunai + COALESCE(summary_jukir_month.kompensasi, 0)) >= summary_jukir_month.potensi
                        THEN 1
                     END) / NULLIF(COUNT(jukirs.id), 0) * 100) as PersentaseHijau"),
            DB::raw("(COUNT(CASE
                        WHEN (summary_jukir_month.non_tunai + COALESCE(summary_jukir_month.kompensasi, 0)) < summary_jukir_month.potensi
                             AND summary_jukir_month.non_tunai > 0
                        THEN 1
                     END) / NULLIF(COUNT(jukirs.id), 0) * 100) as PersentaseKuning"),
            DB::raw("(COUNT(CASE
                        WHEN (summary_jukir_month.non_tunai + COALESCE(summary_jukir_month.kompensasi, 0)) = 0
                        THEN 1
                     END) / NULLIF(COUNT(jukirs.id), 0) * 100) as PersentaseMerah")
        )
        ->leftJoin('lokasis', 'jukirs.lokasi_id', 'lokasis.id')
        ->leftJoin('merchant', 'jukirs.merchant_id', 'merchant.id')
        ->leftJoin('korlaps', 'korlaps.id', 'lokasis.korlap_id')
        ->leftJoin('areas', 'areas.id', 'lokasis.area_id')
        ->leftJoin('summary_jukir_month', 'jukirs.id', 'summary_jukir_month.jukir_id')
        ->where('summary_jukir_month.tahun', $this->tahun)
        ->where('summary_jukir_month.bulan', $this->bulan)
        ->where('jukirs.ket_jukir', 'Active')
        ->whereNotNull('korlaps.id')
        ->groupBy('korlaps.id', 'areas.Kecamatan')
        ->orderByDesc('PersentaseHijau')
        ->orderByDesc('PersentaseKuning')
        ->get();


        foreach($datas as $data){
            SummaryKorlap::updateOrCreate(
                [
                    'korlap_id' => $data->korlap,
                    'bulan' => $this->bulan,
                    'tahun' => $this->tahun,
                ],
                [
                    'jml_jukir' => $data->Jml_Jukir,
                    'hijau' => $data->Hijau,
                    'kuning' => $data->Kuning,
                    'merah' => $data->Merah,
                    'ach_hijau' => $data->PersentaseHijau,
                    'ach_kuning' => $data->PersentaseKuning,
                    'ach_merah' => $data->PersentaseMerah,
                ]
            );
        }
        info('Update Summary Korlap Success');
    }
}
