<?php

namespace App\Http\Livewire;

use App\Models\COA;
use App\Models\Jurnal as ModelsJurnal;
use App\Models\Order;
use App\Models\OrderTrucking;
use App\Models\TemplateJurnal;
use App\Models\TemplateJurnalItem;
use Livewire\Component;

class JurnalTrucking extends Component
{
    public $coa, $coa_id, $tipe, $orders, $jurnals, $jurnal_id, $template_id, $templates, $template, $template_count;
    public $no_1, $no_2, $no_3, $no_4, $no_5;
    public $form, $order, $is_apply;
    public $debit_idx, $credit_idx;

    public function mount()
    {
        $no_1 = ModelsJurnal::where('tipe','JNL')->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->max('no') + 1;
        $no_2 = ModelsJurnal::where('tipe','BBK')->whereYear('created_at',date('Y'))->max('no') + 1;
        $no_3 = ModelsJurnal::where('tipe','BBM')->whereYear('created_at',date('Y'))->max('no') + 1;
        $no_4 = ModelsJurnal::where('tipe','BKK')->whereYear('created_at',date('Y'))->max('no') + 1;
        $no_5 = ModelsJurnal::where('tipe','BKM')->whereYear('created_at',date('Y'))->max('no') + 1;
        $this->order = null;
        $this->template_id = null;
        $this->template = null;
        $this->is_apply = false;
        $this->templates = TemplateJurnal::all();
        $this->coa = COA::where('is_active',1)->orderBy('kode')->get();
        $this->orders = OrderTrucking::select('id','container','seal')->orderBy('container')->get();
        $this->debit_idx = 2;
        $this->credit_idx = 2;
        $this->form = array();
        $this->jurnals = array();
        $this->jurnal_id = array();
        $this->coa_id = null;
        $this->tipe = null;
        $this->template_count = 0;
        $this->no_1 = sprintf('%02d',date('m')).'-'.sprintf('%03d',$no_1).'/'.date('y');
        $this->no_2 = sprintf('%03d',$no_2).'/BBK-RAS/'.date('y');
        $this->no_3 = sprintf('%03d',$no_3).'/BBM-RAS/'.date('y');
        $this->no_4 = sprintf('%03d',$no_4).'/BKK-RAS/'.date('y');
        $this->no_5 = sprintf('%03d',$no_5).'/BKM-RAS/'.date('y');
    }

    public function render()
    {
        return view('livewire.jurnal-trucking');
    }

    public function addColumnDebit()
    {
        $this->debit_idx = $this->debit_idx + 1;
    }

    public function addColumnCredit()
    {
        $this->credit_idx = $this->credit_idx + 1;
    }

    public function apply()
    {
        if (!is_null($this->template_id)) {
            $this->template_count = 1;
            $this->template = TemplateJurnal::find($this->template_id);
        }else{
            $this->template = null;
        }
    }

    public function addBarisTemplate(){
        if($this->template){
            $this->template_count += 1;
        }
        // dd($this->template_count);
    }
}
