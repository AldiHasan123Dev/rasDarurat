<?php

namespace App\Exports;

use App\Models\Transaksi;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PajakExport implements FromView
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
        $transaksi = Transaksi::whereBetween('created_at',[$this->start,$this->end])->orderBy('created_at')->get();
        return view('exports.pajak', compact('transaksi'));
    }
}
