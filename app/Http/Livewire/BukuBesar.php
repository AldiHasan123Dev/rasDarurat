<?php

namespace App\Http\Livewire;

use App\Models\COA;
use App\Models\Jurnal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

class BukuBesar extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $coa, $coas, $coa_id, $months, $month, $year, $saldo, $saldo_awal, $perPage, $tipe, $search;

    public function mount($month = null)
    {
        $this->perPage = 100;
        $this->months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $this->coa_id = 45;
        $this->coa = COA::find(45);
        $this->year = date('Y');
        $this->month = $month ?? date('m');
        $this->coas = COA::orderBy('kode')->get(['id','nama','kode']);
        $c = COA::find($this->coa_id);
        $this->tipe = 'D';
        if(substr($c->kode,0,1)=='2'||substr($c->kode,0,1)=='3'||substr($c->kode,0,1)=='5'){
            $this->tipe = 'C';
        }
        foreach ($this->months as $idx => $item) {
            $bln = $idx + 1;
            $c = new Carbon($this->year.'-'.sprintf('%02d',$bln).'-01');
            $now = $c->startOfMonth()->format('Y-m-d');
            $last = $c->endOfMonth()->format('Y-m-d');
            $start = $c->subMonth()->startOfMonth()->format('Y-m-d');
            $des = $c->endOfMonth()->format('Y-m-d');
            dd($start,$last);
            if($idx==0){
                if($this->tipe=='D'){
                    $saldo_awal = Jurnal::where('coa_id',$this->coa_id)->whereBetween('created_at',[$start,$des])->sum('debit') - Jurnal::where('coa_id',$this->coa_id)->whereBetween('created_at',[$start,$des])->sum('credit');
                }else{
                    $saldo_awal = Jurnal::where('coa_id',$this->coa_id)->whereBetween('created_at',[$start,$last])->sum('credit') - Jurnal::where('coa_id',$this->coa_id)->whereBetween('created_at',[$start,$last])->sum('debit');
                }
            }else{
                // if ($this->tipe=='D') {
                //     $saldo_awal = Jurnal::where('coa_id',$this->coa_id)->whereBetween('created_at',[$start,$last])->sum('debit') - Jurnal::where('coa_id',$this->coa_id)->whereBetween('created_at',[$start,$last])->sum('credit');
                // } else {
                //     $saldo_awal = Jurnal::where('coa_id',$this->coa_id)->whereBetween('created_at',[$start,$last])->sum('credit') - Jurnal::where('coa_id',$this->coa_id)->whereBetween('created_at',[$start,$last])->sum('debit');
                // }
                // if($saldo_awal>0){
                // }
                $start = $now;
                $saldo_awal =  $this->saldo['saldo_akhir'][$idx-1];
                // dd($start,$last,$saldo_awal);
            }
            $debit = Jurnal::where('coa_id',$this->coa_id)->whereBetween('created_at',[$start,$last])->sum('debit');
            $credit = Jurnal::where('coa_id',$this->coa_id)->whereBetween('created_at',[$start,$last])->sum('credit');
            $this->saldo['saldo_awal'][$idx] = $saldo_awal;
            if ($this->tipe=='D') {
                $this->saldo['saldo_akhir'][$idx] = ($debit + $saldo_awal ) - $credit;
            } else {
                $this->saldo['saldo_akhir'][$idx] = ($credit + $saldo_awal) - $debit ;
            }
            $this->saldo['debit'][$idx] = $debit;
            $this->saldo['credit'][$idx] = $credit;
        }
        $m = (int)$this->month;
        $this->saldo_awal = $this->saldo['saldo_awal'][$m-1];
        $this->search = null;
    }

    #[Computed(persist: true, seconds: 7200, cache:true)]
    public function data()
    {
        return Cache::remember('data-jurnal',60*60*24, function(){
            return Jurnal::join('coa','coa.id','=','jurnal.coa_id')
                ->leftJoin('order','order.id','=','jurnal.order_id')
                ->whereMonth('jurnal.created_at',$this->month)
                ->whereYear('jurnal.created_at',$this->year)
                ->where('jurnal.coa_id',$this->coa_id)
                ->select('jurnal.*')
                ->orderBy('jurnal.created_at')
                ->get();
        });
    }

    public function render()
    {
        // if($this->search){
        //     $data =  Jurnal::join('coa','coa.id','=','jurnal.coa_id')
        //         ->leftJoin('order','order.id','=','jurnal.order_id')
        //         ->orWhere('order.job','LIKE','%'.$this->search.'%')
        //         ->whereMonth('jurnal.created_at',$this->month)
        //         ->whereYear('jurnal.created_at',$this->year)
        //         ->where('jurnal.coa_id',$this->coa_id)
        //         ->orWhere('coa.kode','LIKE','%'.$this->search.'%')
        //         ->whereMonth('jurnal.created_at',$this->month)
        //         ->whereYear('jurnal.created_at',$this->year)
        //         ->where('jurnal.coa_id',$this->coa_id)
        //         ->orWhere('coa.nama','LIKE','%'.$this->search.'%')
        //         ->whereMonth('jurnal.created_at',$this->month)
        //         ->whereYear('jurnal.created_at',$this->year)
        //         ->where('jurnal.coa_id',$this->coa_id)
        //         ->orWhere('jurnal.nama','LIKE','%'.$this->search.'%')
        //         ->whereMonth('jurnal.created_at',$this->month)
        //         ->whereYear('jurnal.created_at',$this->year)
        //         ->where('jurnal.coa_id',$this->coa_id)
        //         ->orWhere('jurnal.nomor','LIKE','%'.$this->search.'%')
        //         ->whereMonth('jurnal.created_at',$this->month)
        //         ->whereYear('jurnal.created_at',$this->year)
        //         ->where('jurnal.coa_id',$this->coa_id)
        //         ->orWhere('jurnal.created_at','LIKE','%'.$this->search.'%')
        //         ->whereMonth('jurnal.created_at',$this->month)
        //         ->whereYear('jurnal.created_at',$this->year)
        //         ->where('jurnal.coa_id',$this->coa_id)
        //         ->select('jurnal.*')
        //         ->orderBy('jurnal.created_at')
        //         ->get();
        // }else{
        // }
        return view('livewire.buku-besar');
    }

    public function changeMonth($month)
    {
        $this->month = sprintf('%02d',$month);
        $m = (int)$this->month;
        $this->saldo_awal = $this->saldo['saldo_awal'][$m-1];
        // $this->data = Jurnal::where('coa_id',$this->coa_id)->whereMonth('created_at',$this->month)->whereYear('created_at',$this->year)->orderBy('created_at')->paginate(100);
    }


    public function changeCoa()
    {
        // $this->data = Jurnal::where('coa_id',$this->coa_id)->whereMonth('created_at',$this->month)->whereYear('created_at',$this->year)->orderBy('created_at')->paginate(100);
        $c = COA::find($this->coa_id);
        $this->coa = COA::find($this->coa_id);
        $this->tipe = 'D';
        if(substr($c->kode,0,1)=='2'||substr($c->kode,0,1)=='3'||substr($c->kode,0,1)=='5'){
            $this->tipe = 'C';
        }
        foreach ($this->months as $idx => $item) {
            $bln = $idx + 1;
            $c = new Carbon($this->year.'-'.sprintf('%02d',$bln).'-01');
            $now = $c->startOfMonth()->format('Y-m-d');
            $last = $c->endOfMonth()->format('Y-m-d');
            $start = $c->subMonth()->startOfMonth()->format('Y-m-d');
            $des = $c->endOfMonth()->format('Y-m-d');
            // dd($start,$des);
            if($idx==0){
                if($this->tipe=='D'){
                    $saldo_awal = Jurnal::where('coa_id',$this->coa_id)->whereBetween('created_at',[$start,$des])->sum('debit') - Jurnal::where('coa_id',$this->coa_id)->whereBetween('created_at',[$start,$des])->sum('credit');
                }else{
                    $saldo_awal = Jurnal::where('coa_id',$this->coa_id)->whereBetween('created_at',[$start,$last])->sum('credit') - Jurnal::where('coa_id',$this->coa_id)->whereBetween('created_at',[$start,$last])->sum('debit');
                }
            }else{
                // if ($this->tipe=='D') {
                //     $saldo_awal = Jurnal::where('coa_id',$this->coa_id)->whereBetween('created_at',[$start,$last])->sum('debit') - Jurnal::where('coa_id',$this->coa_id)->whereBetween('created_at',[$start,$last])->sum('credit');
                // } else {
                //     $saldo_awal = Jurnal::where('coa_id',$this->coa_id)->whereBetween('created_at',[$start,$last])->sum('credit') - Jurnal::where('coa_id',$this->coa_id)->whereBetween('created_at',[$start,$last])->sum('debit');
                // }
                // if($saldo_awal>0){
                // }
                $start = $now;
                $saldo_awal =  $this->saldo['saldo_akhir'][$idx-1];
                // dd($start,$last,$saldo_awal);
            }
            $debit = Jurnal::where('coa_id',$this->coa_id)->whereBetween('created_at',[$start,$last])->sum('debit');
            $credit = Jurnal::where('coa_id',$this->coa_id)->whereBetween('created_at',[$start,$last])->sum('credit');
            $this->saldo['saldo_awal'][$idx] = $saldo_awal;
            if ($this->tipe=='D') {
                $this->saldo['saldo_akhir'][$idx] = ($debit + $saldo_awal ) - $credit;
            } else {
                $this->saldo['saldo_akhir'][$idx] = ($credit + $saldo_awal) - $debit ;
            }
            $this->saldo['debit'][$idx] = $debit;
            $this->saldo['credit'][$idx] = $credit;
        }
        $m = (int)$this->month;
        $this->saldo_awal = $this->saldo['saldo_awal'][$m-1];
    }

    public function loadMore()
    {
        $this->perPage += 100;
    }
}
