<?php

namespace App\Http\Livewire;

use App\Models\Jurnal;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ListJurnal extends Component
{
    public $months, $month, $year, $perPage, $search, $tipe, $debit, $credit, $balances, $date;

    public function mount($month = null, $tipe = null, $date = null)
    {
        $this->perPage = 50;
        $this->months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $this->year = date('Y');
        $this->month = request('month') ?? date('m');
        $this->tipe = request('tipe') ?? 'BB';
        $this->date = $date;
    }

    public function render()
    {
        $data =  Jurnal::join('coa','coa.id','=','jurnal.coa_id')
                ->leftJoin('order','order.id','=','jurnal.order_id')
                ->orWhere('order.job','LIKE','%'.$this->search.'%')
                ->whereMonth('jurnal.created_at',$this->month)
                ->where('jurnal.tipe','LIKE',$this->tipe.'%')
                ->whereYear('jurnal.created_at',$this->year)
                ->orWhere('coa.kode','LIKE','%'.$this->search.'%')
                ->whereMonth('jurnal.created_at',$this->month)
                ->where('jurnal.tipe','LIKE',$this->tipe.'%')
                ->whereYear('jurnal.created_at',$this->year)
                ->orWhere('coa.nama','LIKE','%'.$this->search.'%')
                ->whereMonth('jurnal.created_at',$this->month)
                ->where('jurnal.tipe','LIKE',$this->tipe.'%')
                ->whereYear('jurnal.created_at',$this->year)
                ->orWhere('jurnal.nama','LIKE','%'.$this->search.'%')
                ->whereMonth('jurnal.created_at',$this->month)
                ->where('jurnal.tipe','LIKE',$this->tipe.'%')
                ->whereYear('jurnal.created_at',$this->year)
                ->orWhere('jurnal.nomor','LIKE','%'.$this->search.'%')
                ->whereMonth('jurnal.created_at',$this->month)
                ->where('jurnal.tipe','LIKE',$this->tipe.'%')
                ->whereYear('jurnal.created_at',$this->year)
                ->orWhere('jurnal.created_at','LIKE','%'.$this->search.'%')
                ->whereMonth('jurnal.created_at',$this->month)
                ->where('jurnal.tipe','LIKE',$this->tipe.'%')
                ->whereYear('jurnal.created_at',$this->year)
                ->orWhere('jurnal.invoice','LIKE','%'.$this->search.'%')
                ->whereMonth('jurnal.created_at',$this->month)
                ->where('jurnal.tipe','LIKE',$this->tipe.'%')
                ->whereYear('jurnal.created_at',$this->year)
                ->orWhere('jurnal.container','LIKE','%'.$this->search.'%')
                ->whereMonth('jurnal.created_at',$this->month)
                ->where('jurnal.tipe','LIKE',$this->tipe.'%')
                ->whereYear('jurnal.created_at',$this->year)
                ->orWhere('jurnal.nopol','LIKE','%'.$this->search.'%')
                ->whereMonth('jurnal.created_at',$this->month)
                ->where('jurnal.tipe','LIKE',$this->tipe.'%')
                ->whereYear('jurnal.created_at',$this->year)
                ->select('jurnal.*')
                ->orderBy('jurnal.created_at')
                ->orderBy('jurnal.tipe')
                ->orderBy('jurnal.nomor')
                ->paginate($this->perPage);
        $debit =  Jurnal::join('coa','coa.id','=','jurnal.coa_id')
                ->leftJoin('order','order.id','=','jurnal.order_id')
                // ->whereMonth('jurnal.created_at',$this->month)
                // ->where('jurnal.tipe','LIKE',$this->tipe.'%')
                // ->whereYear('jurnal.created_at',$this->year)
                ->select('jurnal.*')
                ->sum('debit');
        $credit =  Jurnal::join('coa','coa.id','=','jurnal.coa_id')
                ->leftJoin('order','order.id','=','jurnal.order_id')
                // ->whereMonth('jurnal.created_at',$this->month)
                // ->where('jurnal.tipe','LIKE',$this->tipe.'%')
                // ->whereYear('jurnal.created_at',$this->year)
                ->select('jurnal.*')
                ->sum('credit');
        return view('livewire.list-jurnal',[
            'data' => $data,
            'total_debit' => $debit,
            'total_credit' => $credit,
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
