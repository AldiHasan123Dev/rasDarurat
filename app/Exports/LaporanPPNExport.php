<?php

namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class LaporanPPNExport implements FromView
{
    private $start;
    private $end;
    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }
    public function view(): View
{
    try {
        $transaksi = Transaksi::whereBetween('created_at', [$this->start, $this->end])
            ->orderBy('created_at')
            ->get();

        return view('exports.laporan_ppn', compact('transaksi'));

    } catch (\Throwable $e) {
        dd("Error view export:", $e->getMessage(), $e->getLine(), $e->getFile());
    }
}

}
