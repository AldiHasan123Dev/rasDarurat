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
    private $tipe;
    private $year;
    private $month;

    public function __construct($from, $to, $tipe, $year, $month)
    {
        $this->from = $from;
        $this->to  = $to;
        $this->tipe  = $tipe;
        $this->year  = $year;
        $this->month  = $month;
    }

    public function view(): View
    {
        $query = Jurnal::query();
        $query->where('tipe',$this->tipe);
        $query->where('no','>=',$this->from);
        $query->where('no','<=',$this->to);
        if ($this->tipe=='JNL') {
            $query->whereMonth('created_at',$this->month);
        }
        $query->whereYear('created_at',$this->year);
        $data = $query->orderBy('created_at')->get();

        return view('exports.jurnal_month', compact('data'));
    }
}
