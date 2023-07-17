<?php

namespace App\Http\Livewire;

use App\Models\COA;
use App\Models\Jurnal as ModelsJurnal;
use App\Models\Kendaraan;
use App\Models\Order;
use App\Models\TemplateJurnal;
use App\Models\TemplateJurnalItem;
use Livewire\Component;

class JurnalManual extends Component
{
    public $coa, $coa_id, $tipe, $jurnals, $jurnal_id, $kendaraan;
    public $no_1, $no_2, $no_3, $no_4, $no_5;
    public $form, $order, $is_apply;
    public $debit_idx, $credit_idx;

    public function mount()
    {
        $no_1 = ModelsJurnal::where('tipe','JNL')->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->max('no') + 1;
        $no_2 = ModelsJurnal::where('tipe','BBK')->max('no') + 1;
        $no_3 = ModelsJurnal::where('tipe','BBM')->max('no') + 1;
        $no_4 = ModelsJurnal::where('tipe','BKK')->max('no') + 1;
        $no_5 = ModelsJurnal::where('tipe','BKM')->max('no') + 1;
        if($no_2==1){
            $no_2 = 2249;
        }
        if($no_3==1){
            $no_3 = 751;
        }
        if($no_4==1){
            $no_4 = 736;
        }
        if($no_5==1){
            $no_5 = 39;
        }
        $this->order = null;
        $this->is_apply = false;
        $this->kendaraan = Kendaraan::get(['id','nopol']);
        $this->coa = COA::where('is_active',1)->orderBy('kode')->get();
        $this->debit_idx = 2;
        $this->credit_idx = 2;
        $this->form = array();
        $this->jurnals = array();
        $this->jurnal_id = array();
        $this->coa_id = null;
        $this->tipe = null;
        $this->no_1 = sprintf('%02d',date('m')).'-'.sprintf('%03d',$no_1).'/'.date('y');
        $this->no_2 = sprintf('%03d',$no_2).'/BBK-RAS/'.date('y');
        $this->no_3 = sprintf('%03d',$no_3).'/BBM-RAS/'.date('y');
        $this->no_4 = sprintf('%03d',$no_4).'/BKK-RAS/'.date('y');
        $this->no_5 = sprintf('%03d',$no_5).'/BKM-RAS/'.date('y');
    }

    public function render()
    {
        return view('livewire.jurnal-manual');
    }

}
