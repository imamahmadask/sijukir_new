<?php

use App\Models\Jukir;
use App\Models\SummaryJukirMonth;
use App\Jobs\UpdateSummaryJukir;

$jukirs = Jukir::where('ket_jukir', 'Non Active')->whereNotNull('tgl_nonactive')->get();

$count = 0;
foreach ($jukirs as $jukir) {
    // Cari semua summary bulan yang dimiliki jukir ini
    $summaries = SummaryJukirMonth::where('jukir_id', $jukir->id)->get();
    foreach ($summaries as $summary) {
        echo "Updating Jukir ID {$jukir->id} for Month {$summary->bulan}-{$summary->tahun}...\n";
        UpdateSummaryJukir::dispatchSync($jukir->id, $summary->tahun, $summary->bulan);
        $count++;
    }
}
echo "\nTotal updated: $count summaries.\n";
