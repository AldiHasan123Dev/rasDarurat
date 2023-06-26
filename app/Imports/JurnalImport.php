<?php

namespace App\Imports;

use App\Models\COA;
use App\Models\Jurnal;
use App\Models\Order;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;

class JurnalImport implements ToModel
{
    public function model(array $row)
    {
        if($row[2]){
            $coa = COA::where('kode',str_replace(' ','',$row[2]))->first();
            if ($coa) {
                Jurnal::create([
                    'coa_id' => $coa->id,
                    'order_id' => null,
                    'nomor' => $row[1],
                    'nama' => str_replace(["'"],'',$row[4]),
                    'debit' => $row[5] ?? 0,
                    'credit' => $row[6] ?? 0,
                    'is_balik' => 1,
                    'created_at' => $row[0]
                ]);
            }
        }
    }
}
