<?php

namespace App\Exports;

use App\Models\COA;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class JurnalBatchExport implements WithMultipleSheets
{
    use Exportable;

    protected $year, $month;

    public function __construct(int $year, int $month)
    {
        $this->year = $year;
        $this->month = $month;
    }

    public function sheets(): array
    {
        $sheets = [];

        $coas = COA::where('is_active',1)->orderBy('kode')->get();
        foreach ($coas as $coa) {
            $sheets[] = new JurnalCoaExport($coa->id, $this->year, $this->month);
        }

        return $sheets;
    }
}
