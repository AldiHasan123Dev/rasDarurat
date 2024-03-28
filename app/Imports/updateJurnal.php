<?php

namespace App\Imports;

use App\Models\Jurnal;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class updateJurnal implements ToCollection, WithStartRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $key => $row) {
            Jurnal::find($row[0])->update([
                'order_id' => $row[1]
            ]);
        }
    }

    public function startRow(): int
    {
        return 2;
    }
}
