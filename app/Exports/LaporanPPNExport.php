<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanPPNExport implements FromQuery, WithHeadings
{
    private $start;
    private $end;

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function query()
    {
        return Transaksi::query()
            ->whereBetween('created_at', [$this->start, $this->end])
            ->orderBy('created_at');
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'No Transaksi',
            'Customer',
            'PPN',
            'Total',
            // sesuaikan
        ];
    }
}

