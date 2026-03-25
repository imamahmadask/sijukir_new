<?php

namespace App\Jobs;

use App\Models\SummaryKategori;
use App\Models\SummaryKategori2;
use App\Models\TargetKategori;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateSummaryKategori implements ShouldQueue
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
        $tanggal_akhir_bulan = Carbon::createFromDate($this->tahun, $this->bulan, 1)->endOfMonth()->format('Y-m-d');

        $summaries = DB::table('summary_jukir_month as sjm')
            ->join('jukirs as j', 'sjm.jukir_id', '=', 'j.id')
            ->join('lokasis as l', 'j.lokasi_id', '=', 'l.id')
            ->select(
                'l.jenis_lokasi as Kategori',
                'l.kategori as Sub_Kategori',
                DB::raw('
                    SUM(sjm.tunai) AS Tunai,
                    SUM(sjm.jml_trx) AS Trx,
                    SUM(sjm.non_tunai) AS Non_Tunai,
                    SUM(sjm.total) AS Total
                ')
            )
            ->where('sjm.bulan', $this->bulan)
            ->where('sjm.tahun', $this->tahun)
            ->groupBy('l.jenis_lokasi', 'l.kategori')
            ->orderBy('l.jenis_lokasi')
            ->orderBy('l.kategori')
            ->get();

        $summaries2 = DB::table('lokasis as l')
            ->join('jukirs as j', 'j.lokasi_id', '=', 'l.id')
            ->select(
                'l.jenis_lokasi as Kategori',
                'l.kategori as Sub_Kategori',
                DB::raw('
                    COUNT(DISTINCT l.id) AS jml_titik,
                    COUNT(DISTINCT j.id) AS jml_jukir
                ')
            )
            ->where('j.tgl_perjanjian', '<=', $tanggal_akhir_bulan)
            ->where('j.ket_jukir', '<>', 'Non Active')
            ->groupBy('l.jenis_lokasi', 'l.kategori')
            ->orderBy('l.jenis_lokasi')
            ->orderBy('l.kategori')
            ->get();

        foreach ($summaries as $summary) {
            SummaryKategori::updateOrCreate(
                [
                    'tahun' => $this->tahun,
                    'bulan' => $this->bulan,
                    'kategori' => $summary->Kategori,
                    'sub_kategori' => $summary->Sub_Kategori,
                ],
                [
                    'tunai' => $summary->Tunai,
                    'jml_trx' => $summary->Trx,
                    'non_tunai' => $summary->Non_Tunai,
                    'total' => $summary->Total,
                ]
            );
        }

        foreach ($summaries2 as $summary) {
            SummaryKategori2::updateOrCreate(
                [
                    'tahun' => $this->tahun,
                    'bulan' => $this->bulan,
                    'kategori' => $summary->Kategori,
                    'sub_kategori' => $summary->Sub_Kategori,
                ],
                [
                    'jml_titik' => $summary->jml_titik,
                    'jml_jukir' => $summary->jml_jukir
                ]
            );
        }

        $summaryKategori = SummaryKategori::selectRaw('tahun, kategori, sum(total) as total')
            ->where('tahun', $this->tahun)
            ->groupBy('tahun', 'kategori')
            ->get();
            
        $targets = TargetKategori::where('tahun', $this->tahun)
            ->get()
            ->keyBy('kategori');

        foreach ($summaryKategori as $summary) {
            $targetValue = $targets->has($summary->kategori) ? $targets->get($summary->kategori)->target : 0;

            $pencapaian = $summary->total;
            $selisih = $pencapaian - $targetValue;
            $persentase = ($targetValue > 0) ? (($pencapaian / $targetValue) * 100) : 0;

            TargetKategori::updateOrCreate(
                [
                    'tahun' => $this->tahun,
                    'kategori' => $summary->kategori
                ],
                [
                    'pencapaian' => $pencapaian,
                    'selisih' => $selisih,
                    'persentase' => $persentase
                ]
            );
        }

        info('Update Summary Kategori Success!');
    }
}
