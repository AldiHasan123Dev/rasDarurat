<?php

namespace App\Http\Livewire;

use App\Models\COA;
use App\Models\Jurnal as ModelsJurnal;
use App\Models\Order;
use App\Models\TemplateJurnal;
use App\Models\TemplateJurnalItem;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Jurnal extends Component
{
    public $coa, $coa_id, $tipe, $orders, $jurnals, $jurnal_id, $template_id, $templates, $template, $template_count, $bgs;
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
        $now = Carbon::now()->addMonths(1)->format('Y-m-d');
        $last = Carbon::now()->subMonths(14)->format('Y-m-d');
        $this->order = null;
        $this->template_id = null;
        $this->template = null;
        $this->is_apply = false;
        $this->templates = TemplateJurnal::all();
        $this->coa = COA::where('is_active',1)->orderBy('kode')->get();
        $this->orders = Order::whereBetween('created_at',[$last,$now])->select('id','no_job','job','seal','invoice')->orderBy('job')->orderBy('no_job')->get();
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
        $this->no_6 = sprintf('%03d',$no_6).'/BBKT-RAS/'.date('y');
        $this->no_7 = sprintf('%03d',$no_7).'/BBMT-RAS/'.date('y');
        $bgs = ModelsJurnal::whereNotNull('no_bg')->orderBy('no_bg')->pluck('no_bg')->toArray();
        $this->bgs = array_unique($bgs);
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
