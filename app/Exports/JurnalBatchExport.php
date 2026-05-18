<?php

namespace App\Exports;

use App\Models\COA;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class JurnalBatchExport implements WithMultipleSheets
{
    use Exportable;

    protected $year, $month,$coaGroup;

    public function __construct(int $year, int $month, string $coaGroup)
    {
        $this->year = $year;
        $this->month = $month;
        $this->coaGroup = $coaGroup;
    }

    public function sheets(): array
    {
        $sheets = [];

        $coas =COA::query()
            ->where('is_active',1)
            ->where('kode', 'LIKE', $this->coaGroup . '%')
            ->orderBy('kode')
            ->get();
        foreach ($coas as $coa) {
            $sheets[] = new JurnalCoaExport($coa->id,$this->year, $this->month);
        }
        return $sheets;
    }
}
