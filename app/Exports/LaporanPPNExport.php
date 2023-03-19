<?php

namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class LaporanPPNExport implements FromView
{

    public function view(): View
    {
        $transaksi = Transaksi::all()->sortBy('created_at');
        return view('exports.laporan_ppn', compact('transaksi'));
    }
}
