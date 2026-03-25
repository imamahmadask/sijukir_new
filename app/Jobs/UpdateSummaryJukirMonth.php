<?php

namespace App\Jobs;

use App\Models\Jukir;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateSummaryJukirMonth implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 25;
    public $timeout = 300;

    protected $vendor;
    protected $tahun;
    protected $bulan;

    public function __construct($vendor, $tahun, $bulan)
    {
        $this->vendor = $vendor;
        $this->tahun = $tahun;
        $this->bulan = $bulan;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $jukirIds = Jukir::whereHas('merchant', function ($query) {
                $query->where('vendor', $this->vendor);
            })
            ->pluck('id');

        foreach ($jukirIds as $jukirId) {
            // BUG FIXED: Parameter order in UpdateSummaryJukir constructor is ($jukirId, $tahun, $bulan)
            UpdateSummaryJukir::dispatch($jukirId, $this->tahun, $this->bulan);
        }

        info('Update Summary Jukir Per month Success');
    }
}
