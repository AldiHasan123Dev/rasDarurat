<?php

namespace App\Exports;

use App\Models\COA;
use App\Models\Jurnal;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
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
            ->orderBy('created_at')
            ->orderBy('tipe')
            ->orderBy('nomor', 'asc')
            ->get();
        $tipe = 'D';
        $c = COA::find($this->coa);
        if(substr($c->kode,0,1)=='2'||substr($c->kode,0,1)=='3'||substr($c->kode,0,1)=='5'){
            $tipe = 'C';
        }

        $ca = new Carbon($this->year.'-'.sprintf('%02d',$this->month).'-01');
        $now = $ca->startOfMonth()->format('Y-m-d');
        $last = $ca->subMonth()->endOfMonth()->format('Y-m-d');
        $kode_awal = substr($c->kode, 0, 1);
         if (in_array($kode_awal, ['5', '6', '7'])) {
            $saldo = 0;
         }
        else if($tipe=='D'){
            $saldo = Jurnal::where('coa_id',$this->coa)->whereBetween('created_at',['2022-12-01',$last])->sum('debit') - Jurnal::where('coa_id',$this->coa)->whereBetween('created_at',['2022-12-01',$last])->sum('credit');
        }else{
            $saldo = Jurnal::where('coa_id',$this->coa)->whereBetween('created_at',['2022-12-01',$last])->sum('credit') - Jurnal::where('coa_id',$this->coa)->whereBetween('created_at',['2022-12-01',$last])->sum('debit');
        }
        return view('exports.jurnal', compact('data','tipe','c','saldo','last'));
    }

    public function title(): string
    {
        $coa = COA::find($this->coa);
        return $coa->kode.' '.$coa->nama;
    }

}
