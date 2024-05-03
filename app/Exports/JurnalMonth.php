<?php

namespace App\Exports;

use App\Models\Jurnal;
use App\Models\JurnalSample;
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
    private $is_sample;

    public function __construct($from, $to, $tipe, $year, $month, $is_sample)
    {
        $this->from = $from;
        $this->to  = $to;
        $this->tipe  = $tipe;
        $this->year  = $year;
        $this->month  = $month;
        $this->is_sample = $is_sample;
    }

    public function view(): View
    {
        $model = new Jurnal();
        if($this->is_sample == 'sample'){
            $model = new JurnalSample();
        }
        $query = $model->query();
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
