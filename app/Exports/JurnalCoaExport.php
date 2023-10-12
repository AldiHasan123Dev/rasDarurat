<?php

namespace App\Exports;

use App\Models\COA;
use App\Models\Jurnal;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class JurnalCoaExport implements WithTitle, FromView, ShouldAutoSize
{
    private $month;
    private $year;
    private $coa;

    public function __construct(int $coa, int $year, int $month)
    {
        $this->coa = $coa;
        $this->month = $month;
        $this->year  = $year;
    }

    public function view(): View
    {
        $data = Jurnal::where('coa_id', $this->coa)
            ->whereYear('created_at', $this->year)
            ->whereMonth('created_at', $this->month)
            ->get();
        $tipe = 'D';
        $c = COA::find($this->coa);
        if(substr($c->kode,0,1)=='2'||substr($c->kode,0,1)=='3'||substr($c->kode,0,1)=='5'){
            $tipe = 'C';
        }
        return view('exports.jurnal', compact('data','tipe','c'));
    }

    public function title(): string
    {
        $coa = COA::find($this->coa);
        return $coa->kode.' '.$coa->nama;
    }

}
