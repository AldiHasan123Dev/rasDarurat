<?php

namespace App\Http\Livewire;

use App\Models\Jurnal;
use App\Models\JurnalSample;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ListJurnal extends Component
{
    public $months, $month, $year, $perPage, $search, $tipe, $debit, $credit, $balances, $date, $jnl, $is_sample;

    public function mount($month = null, $tipe = null, $date = null)
    {
        $this->perPage = 50;
        $this->months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $this->year = request('year') ?? date('Y');
        $this->month = request('month') ?? date('m');
        $this->tipe = request('tipe') ?? 'BB';
        $this->date = $date;
        $this->is_sample = request('is_sample') ?? false;
    }

    public function render()
    {
        $jurnal_model = new Jurnal();
        $prefix = '';
        if ($this->is_sample) {
            $jurnal_model = new JurnalSample();
            $prefix = '_sample';
        }
        $data =  $jurnal_model->join('coa','coa.id','=','jurnal'.$prefix.'.coa_id')
                ->leftJoin('order','order.id','=','jurnal'.$prefix.'.order_id')
                ->orWhere('order.job','LIKE','%'.$this->search.'%')
                ->whereMonth('jurnal'.$prefix.'.created_at',$this->month)
                ->where('jurnal'.$prefix.'.tipe','LIKE',$this->tipe.'%')
                ->whereYear('jurnal'.$prefix.'.created_at',$this->year)
                ->orWhere('coa.kode','LIKE','%'.$this->search.'%')
                ->whereMonth('jurnal'.$prefix.'.created_at',$this->month)
                ->where('jurnal'.$prefix.'.tipe','LIKE',$this->tipe.'%')
                ->whereYear('jurnal'.$prefix.'.created_at',$this->year)
                ->orWhere('coa.nama','LIKE','%'.$this->search.'%')
                ->whereMonth('jurnal'.$prefix.'.created_at',$this->month)
                ->where('jurnal'.$prefix.'.tipe','LIKE',$this->tipe.'%')
                ->whereYear('jurnal'.$prefix.'.created_at',$this->year)
                ->orWhere('jurnal'.$prefix.'.nama','LIKE','%'.$this->search.'%')
                ->whereMonth('jurnal'.$prefix.'.created_at',$this->month)
                ->where('jurnal'.$prefix.'.tipe','LIKE',$this->tipe.'%')
                ->whereYear('jurnal'.$prefix.'.created_at',$this->year)
                ->orWhere('jurnal'.$prefix.'.nomor','LIKE','%'.$this->search.'%')
                ->whereMonth('jurnal'.$prefix.'.created_at',$this->month)
                ->where('jurnal'.$prefix.'.tipe','LIKE',$this->tipe.'%')
                ->whereYear('jurnal'.$prefix.'.created_at',$this->year)
                ->orWhere('jurnal'.$prefix.'.created_at','LIKE','%'.$this->search.'%')
                ->whereMonth('jurnal'.$prefix.'.created_at',$this->month)
                ->where('jurnal'.$prefix.'.tipe','LIKE',$this->tipe.'%')
                ->whereYear('jurnal'.$prefix.'.created_at',$this->year)
                ->orWhere('jurnal'.$prefix.'.invoice','LIKE','%'.$this->search.'%')
                ->whereMonth('jurnal'.$prefix.'.created_at',$this->month)
                ->where('jurnal'.$prefix.'.tipe','LIKE',$this->tipe.'%')
                ->whereYear('jurnal'.$prefix.'.created_at',$this->year)
                ->orWhere('jurnal'.$prefix.'.container','LIKE','%'.$this->search.'%')
                ->whereMonth('jurnal'.$prefix.'.created_at',$this->month)
                ->where('jurnal'.$prefix.'.tipe','LIKE',$this->tipe.'%')
                ->whereYear('jurnal'.$prefix.'.created_at',$this->year)
                ->orWhere('jurnal'.$prefix.'.nopol','LIKE','%'.$this->search.'%')
                ->whereMonth('jurnal'.$prefix.'.created_at',$this->month)
                ->where('jurnal'.$prefix.'.tipe','LIKE',$this->tipe.'%')
                ->whereYear('jurnal'.$prefix.'.created_at',$this->year)
                ->select('jurnal'.$prefix.'.*')
                ->orderBy('jurnal'.$prefix.'.created_at')
                ->orderBy('jurnal'.$prefix.'.tipe')
                ->orderBy('jurnal'.$prefix.'.nomor')
                ->paginate($this->perPage);
        $debit =  $jurnal_model->join('coa','coa.id','=','jurnal'.$prefix.'.coa_id')
                ->leftJoin('order','order.id','=','jurnal'.$prefix.'.order_id')
                ->whereMonth('jurnal'.$prefix.'.created_at',$this->month)
                // ->where('jurnal'.$prefix.'.tipe','LIKE',$this->tipe.'%')
                ->whereYear('jurnal'.$prefix.'.created_at',$this->year)
                ->select('jurnal'.$prefix.'.*')
                ->sum('debit');
        $credit =  $jurnal_model->join('coa','coa.id','=','jurnal'.$prefix.'.coa_id')
                ->leftJoin('order','order.id','=','jurnal'.$prefix.'.order_id')
                ->whereMonth('jurnal'.$prefix.'.created_at',$this->month)
                // ->where('jurnal'.$prefix.'.tipe','LIKE',$this->tipe.'%')
                ->whereYear('jurnal'.$prefix.'.created_at',$this->year)
                ->select('jurnal'.$prefix.'.*')
                ->sum('credit');
        $jnl = $jurnal_model->whereMonth('created_at',$this->month)->whereYear('created_at',$this->year)->where('tipe','JNL')->max('no');
        return view('livewire.list-jurnal',[
            'data' => $data,
            'total_debit' => $debit,
            'total_credit' => $credit,
            'total_jnl' => $jnl,
        ]);
    }

    public function changeMonth($month)
    {
        $this->month = sprintf('%02d',$month);
    }

    public function loadMore()
    {
        $this->perPage += 50;
    }
}
