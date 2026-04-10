<?php

namespace App\Jobs;

use App\Models\HistoriJukir;
use App\Models\Jukir;
use App\Models\KurangSetor;
use App\Models\SummaryJukir;
use App\Models\SummaryJukirMonth;
use App\Models\TransNonTunai;
use App\Models\TransTunai;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;

class UpdateSummaryJukir implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 25;
    public $timeout = 300;

    protected $jukirId;
    protected $tahun;
    protected $bulan;

    /**
     * Create a new job instance.
     */
    public function __construct($jukirId, $tahun, $bulan)
    {
        $this->jukirId = $jukirId;
        $this->tahun = $tahun;
        $this->bulan = $bulan;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $jukir = Jukir::find($this->jukirId);
        
        if (!$jukir) {
            info("Update Summary Jukir Failed: Jukir ID {$this->jukirId} not found.");
            return;
        }

        $month_now = Carbon::createFromDate($this->tahun, $this->bulan, 1)->format('Y-m');
        $month_awal_tap = $jukir->tgl_terbit_qr ? Carbon::parse($jukir->tgl_terbit_qr)->format('Y-m') : null;

        // Proses jika jukir murni tunai (tanpa qr) ATAU bulan awal tap sudah masuk
        if (!$month_awal_tap || $month_awal_tap <= $month_now) {
            
            // 1. Data Summary Bulanan
            $non_tunai_data = $this->getNonTunaiData($jukir);
            $tunai = $this->getTunaiData();
            $is_past = Carbon::createFromDate($this->tahun, $this->bulan, 1)->endOfMonth()->isBefore(now()->startOfMonth());
            $calculated_status = null;
            $potensi = $this->calculatePotensi($jukir, $is_past, $calculated_status);

            $kompensasiBulan = HistoriJukir::where('jukir_id', $this->jukirId)
                ->where('bulan_libur', $this->bulan)
                ->where('tahun_libur', $this->tahun)
                ->sum('kompensasi');

            $summaryData = $this->prepareSummaryData($jukir, $non_tunai_data, $tunai, $potensi, $kompensasiBulan, $is_past, $calculated_status);

            // Perbaiki N+1 / whereHas yang lambat menggunakan updateOrCreate
            SummaryJukirMonth::updateOrCreate(
                [
                    'jukir_id' => $this->jukirId,
                    'bulan'    => $this->bulan,
                    'tahun'    => $this->tahun
                ],
                $summaryData
            );

            // 2. Data Summary Tahunan (Berdasarkan Agregasi Bulanan)
            $summaryAll = SummaryJukirMonth::selectRaw('SUM(tunai) as tunai, SUM(non_tunai) as non_tunai, SUM(jml_trx) as jml_trx, SUM(potensi) as potensi')
                ->where('jukir_id', $this->jukirId)
                ->where('tahun', $this->tahun)
                ->first();

            $potensiTahun    = $summaryAll->potensi ?? 0;
            $non_tunaiTahun  = $summaryAll->non_tunai ?? 0;
            $tunaiTahun      = $summaryAll->tunai ?? 0;
            $jml_trxTahun    = $summaryAll->jml_trx ?? 0;

            $bayarKurangSetor = $this->getBayarKurangSetor() ?? 0;
            $setoranHarian    = $non_tunaiTahun - $bayarKurangSetor;
            $kompensasiTahun  = $this->getKompensasi($this->jukirId) ?? 0;
            
            $kurang_setorTahun = $non_tunaiTahun - $potensiTahun + $kompensasiTahun;
            if ($kurang_setorTahun > 0) {
                $kurang_setorTahun = 0;
            }

            $persentaseSetoran = ($potensiTahun > 0) ? ($non_tunaiTahun / $potensiTahun) * 100 : 0;

            SummaryJukir::updateOrCreate(
                [
                    'jukir_id' => $this->jukirId,
                    'tahun'    => $this->tahun,
                ],
                [
                    'potensi'            => $potensiTahun,
                    'tunai'              => $tunaiTahun,
                    'non_tunai'          => $non_tunaiTahun,
                    'setoran_harian'     => $setoranHarian,
                    'bayar_kurang_setor' => $bayarKurangSetor,
                    'kompensasi'         => $kompensasiTahun,
                    'jml_transaksi'      => $jml_trxTahun,
                    'total'              => $tunaiTahun + $non_tunaiTahun,
                    'kurang_setor'       => $kurang_setorTahun,
                    'persentase'         => round($persentaseSetoran, 2),
                ]
            );

            info("Update Summary Jukir Success for Jukir ID: {$this->jukirId}");
        } else {
            info("Update Summary Jukir Pending for Jukir ID: {$this->jukirId} (Awal Tap: {$month_awal_tap} > {$month_now})");
        }
    }

    private function getKompensasi($id)
    {
        // BUG FIXED: Hanya hitung kompensasi di tahun yang sama
        return HistoriJukir::where('jukir_id', $id)
            ->where('tahun_libur', $this->tahun)
            ->sum('kompensasi');
    }

    private function getNonTunaiData($jukir)
    {
        // BUG FIXED: Jika Jukir ini murni Tunai, kita wajib return 0.
        // Sebelumnya, jika merchant_id null, query akan sum SELURUH Jukir di database!
        if (empty($jukir->merchant_id) && empty($jukir->old_merchant_id)) {
            return (object) ['total' => 0, 'jumlah' => 0];
        }

        return TransNonTunai::selectRaw('SUM(total_nilai) as total, COUNT(id) as jumlah')
            ->where(function ($query) use ($jukir) {
                if ($jukir->merchant_id) {
                    $query->where('merchant_id', $jukir->merchant_id);
                }
                if ($jukir->old_merchant_id) {
                    $query->orWhere('merchant_id', $jukir->old_merchant_id);
                }
            })
            // Direkomendasikan pakai whereMonth dan whereYear daripada pencarian string murni
            ->whereYear('tgl_transaksi', $this->tahun)
            ->whereMonth('tgl_transaksi', $this->bulan)
            ->where('status', 'SUCCEED')
            ->first();
    }

    private function getTunaiData()
    {
        return TransTunai::where('jukir_id', $this->jukirId)
            ->whereYear('tgl_transaksi', $this->tahun)
            ->whereMonth('tgl_transaksi', $this->bulan)
            ->sum('jumlah_transaksi');
    }

    private function getStatusJukirAtMonth($jukir, $existing)
    {
        // Jika historisnya memang 'Non Active', pertahankan
        if ($existing && $existing->status_jukir == 'Non Active') {
            return 'Non Active';
        }

        $status_jukir = $jukir->ket_jukir;
        if ($jukir->ket_jukir == 'Non Active' && $jukir->tgl_nonactive) {
            $month_now = Carbon::createFromDate($this->tahun, $this->bulan, 1)->format('Y-m');
            $month_nonactive = Carbon::parse($jukir->tgl_nonactive)->format('Y-m');
            if ($month_now < $month_nonactive) {
                $status_jukir = 'Active';
            } else {
                $status_jukir = 'Non Active';
            }
        }
        return $status_jukir;
    }

    private function calculatePotensi($jukir, $is_past = false, &$calculated_status = null)
    {
        $potensi_bulanan = ($jukir->potensi_bulanan_upl > 0) ? $jukir->potensi_bulanan_upl : $jukir->potensi_bulanan;

        $existing = null;
        if ($is_past) {
            $existing = SummaryJukirMonth::where('jukir_id', $this->jukirId)
                ->where('bulan', $this->bulan)
                ->where('tahun', $this->tahun)
                ->first();
        }

        $calculated_status = $this->getStatusJukirAtMonth($jukir, $existing);

        return ($calculated_status == 'Non Active') ? 0 : $potensi_bulanan;
    }

    private function prepareSummaryData($jukir, $non_tunai_data, $tunai, $potensi, $kompensasiBulan, $is_past = false, $calculated_status = null)
    {
        $total_non_tunai = $non_tunai_data->total ?? 0;
        $jumlah_non_tunai = $non_tunai_data->jumlah ?? 0;

        $kurangSetor = $total_non_tunai + $kompensasiBulan - $potensi;
        if ($kurangSetor > 0) {
            $kurangSetor = 0; // Konsisten dengan cara perhitungan tahunan
        }

        $data = [
            'potensi'      => $potensi,
            'jml_trx'      => $jumlah_non_tunai,
            'tunai'        => $tunai,
            'non_tunai'    => $total_non_tunai,
            'total'        => $tunai + $total_non_tunai,
            'kompensasi'   => $kompensasiBulan,
            'kurang_setor' => $kurangSetor
        ];

        // Jika ini bulan lalu, kita hanya update snapshot tipe/lokasi jika belum ada datanya.
        // Jika sudah ada, kita pertahankan snapshot tipe lama agar tidak berubah saat jukir dimutasi/dinonaktifkan
        $existing = SummaryJukirMonth::where('jukir_id', $this->jukirId)
            ->where('bulan', $this->bulan)
            ->where('tahun', $this->tahun)
            ->first();

        if ($calculated_status) {
            $data['status_jukir'] = $calculated_status;
        } else {
            $data['status_jukir'] = $jukir->ket_jukir;
        }

        if (!$is_past || !$existing) {
            $data['tipe_jukir']   = $jukir->status;
            $data['korlap_id']    = $jukir->lokasi?->korlap_id;
        }

        return $data;
    }

    private function getBayarKurangSetor()
    {
        return KurangSetor::where('jukir_id', $this->jukirId)
            ->where('tahun', $this->tahun)
            ->sum('jumlah');
    }
}
