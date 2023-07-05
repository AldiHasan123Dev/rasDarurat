<?php

namespace App\Http\Livewire;

use App\Models\COA;
use App\Models\Order;
use App\Models\TemplateJurnal;
use Livewire\Component;

class JurnalKolektif extends Component
{
    public $coa, $coa_id, $tipe, $orders, $jurnals, $jurnal_id, $template_id, $templates, $template, $template_count;
    public $form, $order, $is_apply;
    public $debit_idx, $credit_idx;

    public function mount()
    {
        $job = Order::pluck('job')->toArray();
        $job = array_unique($job);
        $this->order = null;
        $this->template_id = null;
        $this->template = null;
        $this->is_apply = false;
        $this->templates = TemplateJurnal::all();
        $this->coa = COA::where('is_active',1)->orderBy('kode')->get();
        $this->orders = $job;
        $this->debit_idx = 2;
        $this->credit_idx = 2;
        $this->form = array();
        $this->jurnals = array();
        $this->jurnal_id = array();
        $this->coa_id = null;
        $this->tipe = null;
        $this->template_count = 0;
    }

    public function render()
    {
        return view('livewire.jurnal-kolektif');
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
