<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class InvoiceRekapExport implements FromView
{
    private $invoice;

    function __construct($invoice)
    {
        $this->invoice = $invoice;
    }

    public function view(): View
    {
        $data = Order::where('invoice',$this->invoice)->get();
        return view('exports.rekap_invoice', ['data'=>$data]);
    }
}
