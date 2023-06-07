<?php

namespace App\Exports;

use App\Models\OrderTrucking;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class SlipSopirExport implements FromView
{
    private $invoice;

    public function __construct($invoice)
    {
        $this->invoice = $invoice;
    }

    public function view(): View
    {
        $order = OrderTrucking::where('invoice_sopir',$this->invoice)->first();
        $orders = OrderTrucking::where('invoice_sopir',$this->invoice)->get();
        return view('exports.slip_sopir', compact('orders','order'));
    }
}
