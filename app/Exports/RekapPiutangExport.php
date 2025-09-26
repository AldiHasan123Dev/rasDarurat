<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RekapPiutangExport implements FromCollection, WithHeadings, WithMapping
{
    protected $rekapData;

    public function __construct($rekapData)
    {
        $this->rekapData = $rekapData;
    }

    /**
     * Data yang akan diekspor
     */
    public function collection()
    {
        return $this->rekapData;
    }

    /**
     * Mapping setiap baris
     */
    public function map($row): array
    {
        return [
            $row['no_job'],
            $row['cs'],
            $row['marketing'],     
            $row['customer'],
            $row['invoice'],
            $row['shipment'],
            $row['kapal'],
            $row['voyage'],
            $row['container'],
            $row['td'],
            $row['tarif'],
            $row['ppn'],
            $row['total'],
        ];
    }

    /**
     * Header Excel
     */
    public function headings(): array
    {
        return [
            'No Job',
            'CS',
            'Marketing',
            'Customer',
            'Invoice',
            'Shipment',
            'Kapal',
            'Voyage',
            'Container',
            'TD',
            'Tarif',
            'PPN',
            'Total'
        ];
    }
}
