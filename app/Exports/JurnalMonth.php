<?php

namespace App\Exports;

use App\Models\Jurnal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class JurnalMonth implements FromView
{
    private $from;
    private $to;

    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to  = $to;
    }

    public function view(): View
    {
        $data = Jurnal::whereBetween('created_at',[$this->from,$this->to])
                ->get();

        return view('exports.jurnal_month', compact('data'));
    }
}
