<?php

namespace App\Http\Livewire;

use App\Models\COA;
use App\Models\Jurnal;
use Livewire\Component;
use Livewire\WithPagination;

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
            if($idx==0){
                if($this->tipe=='D'){
                    $saldo_awal = Jurnal::where('coa_id',$this->coa_id)->whereMonth('created_at',sprintf('%02d','12'))->whereYear('created_at',$this->year-1)->sum('debit') - Jurnal::where('coa_id',$this->coa_id)->whereMonth('created_at',sprintf('%02d','12'))->whereYear('created_at',$this->year-1)->sum('credit');
                }else{
                    $saldo_awal = Jurnal::where('coa_id',$this->coa_id)->whereMonth('created_at',sprintf('%02d','12'))->whereYear('created_at',$this->year-1)->sum('credit') - Jurnal::where('coa_id',$this->coa_id)->whereMonth('created_at',sprintf('%02d','12'))->whereYear('created_at',$this->year-1)->sum('debit');
                }
            }else{
                if ($this->tipe=='D') {
                    $saldo_awal = Jurnal::where('coa_id',$this->coa_id)->whereMonth('created_at',sprintf('%02d',$idx))->whereYear('created_at',$this->year)->sum('debit') - Jurnal::where('coa_id',$this->coa_id)->whereMonth('created_at',sprintf('%02d',$idx))->whereYear('created_at',$this->year)->sum('credit');
                } else {
                    $saldo_awal = Jurnal::where('coa_id',$this->coa_id)->whereMonth('created_at',sprintf('%02d',$idx))->whereYear('created_at',$this->year)->sum('credit') - Jurnal::where('coa_id',$this->coa_id)->whereMonth('created_at',sprintf('%02d',$idx))->whereYear('created_at',$this->year)->sum('debit');
                }
                if($saldo_awal>0){
                    $saldo_awal +=  $this->saldo['saldo_awal'][$idx-1];
                }
            }
            $debit = Jurnal::where('coa_id',$this->coa_id)->whereMonth('created_at',sprintf('%02d',$idx+1))->whereYear('created_at',$this->year)->sum('debit');
            $credit = Jurnal::where('coa_id',$this->coa_id)->whereMonth('created_at',sprintf('%02d',$idx+1))->whereYear('created_at',$this->year)->sum('credit');
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
        $this->saldo_awal = $this->saldo['saldo_awal'][$m];
    }

    public function render()
    {
        $data =  Jurnal::join('coa','coa.id','=','jurnal.coa_id')
            ->leftJoin('order','order.id','=','jurnal.order_id')
            ->orWhere('order.job','LIKE','%'.$this->search.'%')
            ->whereMonth('jurnal.created_at',$this->month)
            ->whereYear('jurnal.created_at',$this->year)
            ->where('jurnal.coa_id',$this->coa_id)
            ->orWhere('coa.kode','LIKE','%'.$this->search.'%')
            ->whereMonth('jurnal.created_at',$this->month)
            ->whereYear('jurnal.created_at',$this->year)
            ->where('jurnal.coa_id',$this->coa_id)
            ->orWhere('coa.nama','LIKE','%'.$this->search.'%')
            ->whereMonth('jurnal.created_at',$this->month)
            ->whereYear('jurnal.created_at',$this->year)
            ->where('jurnal.coa_id',$this->coa_id)
            ->orWhere('jurnal.nama','LIKE','%'.$this->search.'%')
            ->whereMonth('jurnal.created_at',$this->month)
            ->whereYear('jurnal.created_at',$this->year)
            ->where('jurnal.coa_id',$this->coa_id)
            ->orWhere('jurnal.nomor','LIKE','%'.$this->search.'%')
            ->whereMonth('jurnal.created_at',$this->month)
            ->whereYear('jurnal.created_at',$this->year)
            ->where('jurnal.coa_id',$this->coa_id)
            ->orWhere('jurnal.created_at','LIKE','%'.$this->search.'%')
            ->whereMonth('jurnal.created_at',$this->month)
            ->whereYear('jurnal.created_at',$this->year)
            ->where('jurnal.coa_id',$this->coa_id)
            ->select('jurnal.*')
            ->orderBy('jurnal.created_at')
            ->paginate($this->perPage);
        return view('livewire.buku-besar',[
            'data' => $data
        ]);
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
            if($idx==0){
                if($this->tipe=='D'){
                    $saldo_awal = Jurnal::where('coa_id',$this->coa_id)->whereMonth('created_at',sprintf('%02d','12'))->whereYear('created_at',$this->year-1)->sum('debit') - Jurnal::where('coa_id',$this->coa_id)->whereMonth('created_at',sprintf('%02d','12'))->whereYear('created_at',$this->year-1)->sum('credit');
                }else{
                    $saldo_awal = Jurnal::where('coa_id',$this->coa_id)->whereMonth('created_at',sprintf('%02d','12'))->whereYear('created_at',$this->year-1)->sum('credit') - Jurnal::where('coa_id',$this->coa_id)->whereMonth('created_at',sprintf('%02d','12'))->whereYear('created_at',$this->year-1)->sum('debit');
                }
            }else{
                if ($this->tipe=='D') {
                    $saldo_awal = Jurnal::where('coa_id',$this->coa_id)->whereMonth('created_at',sprintf('%02d',$idx))->whereYear('created_at',$this->year)->sum('debit') - Jurnal::where('coa_id',$this->coa_id)->whereMonth('created_at',sprintf('%02d',$idx))->whereYear('created_at',$this->year)->sum('credit');
                } else {
                    $saldo_awal = Jurnal::where('coa_id',$this->coa_id)->whereMonth('created_at',sprintf('%02d',$idx))->whereYear('created_at',$this->year)->sum('credit') - Jurnal::where('coa_id',$this->coa_id)->whereMonth('created_at',sprintf('%02d',$idx))->whereYear('created_at',$this->year)->sum('debit');
                }
                if($saldo_awal>0){
                    $saldo_awal +=  $this->saldo['saldo_awal'][$idx-1];
                }

            }
            $debit = Jurnal::where('coa_id',$this->coa_id)->whereMonth('created_at',sprintf('%02d',$idx+1))->whereYear('created_at',$this->year)->sum('debit');
            $credit = Jurnal::where('coa_id',$this->coa_id)->whereMonth('created_at',sprintf('%02d',$idx+1))->whereYear('created_at',$this->year)->sum('credit');
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
