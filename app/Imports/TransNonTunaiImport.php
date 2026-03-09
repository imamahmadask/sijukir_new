<?php

namespace App\Imports;

use App\Models\Merchant;
use App\Models\TransNonTunai;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithUpsertColumns;
use Carbon\Carbon;

class TransNonTunaiImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, WithUpserts, WithUpsertColumns
{
    protected $filename;

    public function __construct($filename = null)
    {
        $this->filename = $filename;
    }

    public function model(array $row)
    {
        $merchant = Merchant::find($row['merchant_id']);

        if($merchant)
        {
            $area_id = $merchant->area->id;
            $area = $merchant->area->Kecamatan;

            $bulan = Carbon::parse($row['tanggal_transaksi'])->format('m');
            $tahun = Carbon::parse($row['tanggal_transaksi'])->format('Y');

            return new TransNonTunai([
                'tgl_transaksi' => $row['tanggal_transaksi'],
                'bulan'         => $bulan,
                'tahun'         => $tahun,
                'merchant_id'   => $row['merchant_id'] ?? null,
                'merchant_name' => $row['merchant'] ?? null,
                'issuer_name'   => $row['issuer_name'] ?? null,
                'total_nilai'   => $row['nilai_settled'] ?? 0,
                'status'        => $row['status'] ?? 'Success',
                'syslog'        => $row['tmoney_syslog'] ?? null,
                'area_id'       => $area_id,
                'sender_name'   => $row['sender_name'] ?? null,
                'kecamatan'     => $area,
                'filename'      => $this->filename,
                'info'          => $row['info'] ?? null,
                'settlement'    => $row['settlement'] ?? null,
            ]);
        }
        else
        {
            abort(404, 'Merchant not found');
        }        
    }

    public function rules(): array
    {
        return [
            'merchant_id' => 'required',
            'nilai_settled' => 'required',
        ];
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function uniqueBy()
    {
        return 'syslog';
    }

    public function upsertColumns()
    {
        return ['total_nilai', 'issuer_name'];
    }
}
