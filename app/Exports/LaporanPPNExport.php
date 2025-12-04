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
    dd(
        $this->start,
        $this->end,
        Transaksi::whereBetween('created_at',[$this->start,$this->end])->count()
    );
}

}
