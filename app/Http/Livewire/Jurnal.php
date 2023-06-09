<?php

namespace App\Http\Livewire;

use App\Models\COA;
use App\Models\Jurnal as ModelsJurnal;
use App\Models\Order;
use App\Models\TemplateJurnal;
use App\Models\TemplateJurnalItem;
use Livewire\Component;

class Jurnal extends Component
{
    public $coa, $coa_id, $tipe, $orders, $jurnals, $jurnal_id, $template_id, $templates, $template;
    public $form, $order, $is_apply;
    public $debit_idx, $credit_idx;

    public function mount()
    {
        $this->order = null;
        $this->template_id = null;
        $this->template = null;
        $this->is_apply = false;
        $this->templates = TemplateJurnal::all();
        $this->coa = COA::doesnthave('coas')->orderBy('kode')->get();
        $this->orders = Order::select('id','no_job','job','seal')->orderBy('job')->orderBy('no_job')->get();
        $this->debit_idx = 2;
        $this->credit_idx = 2;
        $this->form = array();
        $this->jurnals = array();
        $this->jurnal_id = array();
        $this->coa_id = null;
        $this->tipe = null;
    }

    public function render()
    {
        return view('livewire.jurnal');
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
            $this->template = TemplateJurnalItem::where('template_jurnal_id',$this->template_id)->get();
        }else{
            $this->template = null;
        }
    }
}
