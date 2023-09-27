<?php

namespace App\Http\Livewire;

use App\Models\COA;
use App\Models\Jurnal;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Neraca extends Component
{
    public $aktiva_lancar, $aktiva_tak_lancar, $kewajiban, $modal, $lr;
    public $months, $month, $year, $start, $end;

    public function mount()
    {
        $this->months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $this->month = date('m');
        $this->year = date('Y');
        $m = sprintf('%02d',(int)$this->month -1);
        $this->start = $this->year.'-'.$m.'-01';
        if($this->month=='01'){
            $this->start = ((int)$this->year - 1).'-12-01';
        }
        $this->start = '2022-12-01';
        $this->end = $this->getLastDay();
        $this->aktiva_lancar = COA::where('kode','not like','1.2%')->where('kode','like','1%')->orderBy('kode')->get();
        $this->aktiva_tak_lancar = COA::where('kode','like','1.2%')->orderBy('kode')->get();
        $this->kewajiban = COA::whereIn('kode',['2','2.1','2.1.1','2.1.1.1','2.1.1.2','2.1.1.3','2.1.1.4','2.1.1.5','2.1.1.6','2.1.2','2.1.2.1','2.1.2.2','2.1.2.3','2.1.2.4','2.1.2.4.1','2.1.2.4.2','2.1.2.4.3','2.1.2.5','2.1.3','2.1.3.1','2.1.3.2','2.1.3.3','2.1.3.4','2.1.3.5','2.1.3.6','2.1.3.7','2.1.3.8','2.1.4','2.1.4.1','2.1.4.2','2.1.5','2.1.5.1','2.1.5.2','2.1.5.2.1','2.1.5.2.2'])->orderBy('kode')->get();
        $this->modal = COA::whereIn('kode',['3','3.1','3.2','3.3'])->orderBy('kode')->get();
        $kel5 = Jurnal::join('coa','coa.id','=','jurnal.coa_id')
                ->where('coa.kode','like','5.%')
                ->whereBetween('jurnal.created_at',[$this->start,$this->end])
                ->get();
        $kel6 = Jurnal::join('coa','coa.id','=','jurnal.coa_id')
                ->where('coa.kode','like','6.%')
                ->whereBetween('jurnal.created_at',[$this->start,$this->end])
                ->get();
        $kel7 = Jurnal::join('coa','coa.id','=','jurnal.coa_id')
                ->where('coa.kode','like','7.%')
                ->whereBetween('jurnal.created_at',[$this->start,$this->end])
                ->get();
        $this->lr = ($kel5->sum('credit') - $kel5->sum('debit')) - (($kel6->sum('debit') - $kel6->sum('credit')) + ($kel7->sum('debit') - $kel7->sum('credit')));
        // dd($this->start,$this->end);
    }

    public function render()
    {
        return view('livewire.neraca');
    }

    public function changeMonth($month)
    {
        $m = sprintf('%02d',$month);
        $this->month = $m;
        // $s = (int)$this->month -1;
        // $this->start = $this->year.'-'.$s.'-01';
        // if($this->month=='01'){
        //     $this->start = ((int)$this->year - 1).'-12-01';
        // }
        $this->end = $this->getLastDay();
        $this->modal = COA::whereIn('kode',['3','3.1','3.2','3.3'])->orderBy('kode')->get();
        $kel5 = Jurnal::join('coa','coa.id','=','jurnal.coa_id')
                ->where('coa.kode','like','5.%')
                ->whereBetween('jurnal.created_at',[$this->start,$this->end])
                ->get();
        $kel6 = Jurnal::join('coa','coa.id','=','jurnal.coa_id')
                ->where('coa.kode','like','6.%')
                ->whereBetween('jurnal.created_at',[$this->start,$this->end])
                ->get();
        $kel7 = Jurnal::join('coa','coa.id','=','jurnal.coa_id')
                ->where('coa.kode','like','7.%')
                ->whereBetween('jurnal.created_at',[$this->start,$this->end])
                ->get();
        $this->lr = ($kel5->sum('credit') - $kel5->sum('debit')) - (($kel6->sum('debit') - $kel6->sum('credit')) + ($kel7->sum('debit') - $kel7->sum('credit')));
        // dd($this->end);
    }

    public function changeYear()
    {
        // $s = (int)$this->month -1;
        // $this->start = $this->year.'-'.$s.'-01';
        // if($this->month=='01'){
        //     $this->start = ((int)$this->year - 1).'-12-01';
        // }
        $this->end = $this->getLastDay();
        $this->modal = COA::whereIn('kode',['3','3.1','3.2','3.3'])->orderBy('kode')->get();
        $kel5 = Jurnal::join('coa','coa.id','=','jurnal.coa_id')
                ->where('coa.kode','like','5.%')
                ->whereBetween('jurnal.created_at',[$this->start,$this->end])
                ->get();
        $kel6 = Jurnal::join('coa','coa.id','=','jurnal.coa_id')
                ->where('coa.kode','like','6.%')
                ->whereBetween('jurnal.created_at',[$this->start,$this->end])
                ->get();
        $kel7 = Jurnal::join('coa','coa.id','=','jurnal.coa_id')
                ->where('coa.kode','like','7.%')
                ->whereBetween('jurnal.created_at',[$this->start,$this->end])
                ->get();
        $this->lr = ($kel5->sum('credit') - $kel5->sum('debit')) - (($kel6->sum('debit') - $kel6->sum('credit')) + ($kel7->sum('debit') - $kel7->sum('credit')));
    }

    public function getLastDay()
    {
        $carbon = new Carbon($this->year.'-'.$this->month.'-01');
        $last = $carbon->endOfMonth()->toDateString();
        return $last;
    }
}
