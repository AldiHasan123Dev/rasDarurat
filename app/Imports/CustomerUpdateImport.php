<?php

namespace App\Imports;

use App\Models\Customer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class CustomerUpdateImport implements ToModel, WithStartRow
{

    public function model(array $row)
    {
        $customer = Customer::where('nama',$row[0])->first();
        if ($customer) {
            $customer->update([
                'nama_npwp' => $row[1],
                'alamat_npwp' => $row[2],
                'npwp' => $row[3],
                'nik' => $customer->nik??$row[4],
            ]);
        }
    }

    public function startRow(): int
    {
        return 2;
    }
}
