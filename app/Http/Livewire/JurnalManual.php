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
    public $coa, $coa_id, $tipe, $jurnals, $jurnal_id, $kendaraan, $templates;
    public $no_1, $no_2, $no_3, $no_4, $no_5, $no_6, $no_7;
    public $form, $order, $is_apply;
    public $debit_idx, $credit_idx;

    public function mount()
    {
        $no_1 = ModelsJurnal::where('tipe','JNL')->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->max('no') + 1;
        $no_2 = ModelsJurnal::where('tipe','BBK')->whereYear('created_at',date('Y'))->max('no') + 1;
        $no_3 = ModelsJurnal::where('tipe','BBM')->whereYear('created_at',date('Y'))->max('no') + 1;
        $no_4 = ModelsJurnal::where('tipe','BKK')->whereYear('created_at',date('Y'))->max('no') + 1;
        $no_5 = ModelsJurnal::where('tipe','BKM')->whereYear('created_at',date('Y'))->max('no') + 1;
        $no_6 = ModelsJurnal::where('tipe','BBKT')->whereYear('created_at',date('Y'))->max('no') + 1;
        $no_7 = ModelsJurnal::where('tipe','BBMT')->whereYear('created_at',date('Y'))->max('no') + 1;
        $this->order = null;
        $this->is_apply = false;
        $this->kendaraan = Kendaraan::get(['id','nopol']);
        $this->coa = COA::where('is_active',1)->orderBy('kode')->get();
        $this->templates = TemplateJurnal::all();
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
        $this->no_6 = sprintf('%03d',$no_6).'/BBKT-RAS/'.date('y');
        $this->no_7 = sprintf('%03d',$no_7).'/BBMT-RAS/'.date('y');
    }

    public function render()
    {
        return view('livewire.jurnal-manual');
    }

}
