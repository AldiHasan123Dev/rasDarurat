<?php

namespace App\Http\Livewire;

use App\Models\Jurnal;
use Livewire\Component;

class ListJurnal extends Component
{
    public $months, $month, $year, $perPage, $search;

    public function mount($month = null)
    {
        $this->perPage = 100;
        $this->months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $this->year = date('Y');
        $this->month = $month ?? date('m');
    }

    public function render()
    {
        $data =  Jurnal::join('coa','coa.id','=','jurnal.coa_id')
                ->leftJoin('order','order.id','=','jurnal.order_id')
                ->orWhere('order.job','LIKE','%'.$this->search.'%')
                ->whereMonth('jurnal.created_at',$this->month)
                ->whereYear('jurnal.created_at',$this->year)
                ->orWhere('coa.kode','LIKE','%'.$this->search.'%')
                ->whereMonth('jurnal.created_at',$this->month)
                ->whereYear('jurnal.created_at',$this->year)
                ->orWhere('coa.nama','LIKE','%'.$this->search.'%')
                ->whereMonth('jurnal.created_at',$this->month)
                ->whereYear('jurnal.created_at',$this->year)
                ->orWhere('jurnal.nama','LIKE','%'.$this->search.'%')
                ->whereMonth('jurnal.created_at',$this->month)
                ->whereYear('jurnal.created_at',$this->year)
                ->orWhere('jurnal.nomor','LIKE','%'.$this->search.'%')
                ->whereMonth('jurnal.created_at',$this->month)
                ->whereYear('jurnal.created_at',$this->year)
                ->orWhere('jurnal.created_at','LIKE','%'.$this->search.'%')
                ->whereMonth('jurnal.created_at',$this->month)
                ->whereYear('jurnal.created_at',$this->year)
                ->select('jurnal.*')
                ->orderBy('jurnal.created_at')
                ->orderBy('jurnal.tipe')
                ->paginate($this->perPage);
        return view('livewire.list-jurnal',[
            'data' => $data
        ]);
    }

    public function changeMonth($month)
    {
        $this->month = sprintf('%02d',$month);
    }

    public function loadMore()
    {
        $this->perPage += 100;
    }
}
