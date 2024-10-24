<?php

namespace App\Http\Livewire;

use App\Models\COA;
use App\Models\Jurnal;
use App\Models\Order;
use App\Models\Setting;
use App\Models\TemplateJurnal;
use Illuminate\Support\Carbon;
use Livewire\Component;

class JurnalKolektif extends Component
{
    public $coa, $coa_id, $tipe, $orders, $jurnals, $jurnal_id, $template_id, $templates, $template, $template_count;
    public $no_1, $no_2, $no_3, $no_4, $no_5, $no_6, $no_7;
    public $form, $order, $is_apply;
    public $debit_idx, $credit_idx;
    public $c16, $c45, $c175;

    public function mount()
    {
        $no_1 = Jurnal::where('tipe','JNL')->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->max('no') + 1;
        $no_2 = Jurnal::where('tipe','BBK')->whereYear('created_at',date('Y'))->max('no') + 1;
        $no_3 = Jurnal::where('tipe','BBM')->whereYear('created_at',date('Y'))->max('no') + 1;
        $no_4 = Jurnal::where('tipe','BKK')->whereYear('created_at',date('Y'))->max('no') + 1;
        $no_5 = Jurnal::where('tipe','BKM')->whereYear('created_at',date('Y'))->max('no') + 1;
        $no_6 = Jurnal::where('tipe','BBKT')->whereYear('created_at',date('Y'))->max('no') + 1;
        $no_7 = Jurnal::where('tipe','BBMT')->whereYear('created_at',date('Y'))->max('no') + 1;
        $now = Carbon::now()->addMonths(1)->format('Y-m-d');
        $last = Carbon::now()->subMonths(14)->format('Y-m-d');
        $job = Order::whereBetween('created_at',[$last,$now])->pluck('job')->toArray();
        $job = array_unique($job);
        $setting = Setting::find(1);
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
        $this->no_1 = sprintf('%02d',date('m')).'-'.sprintf('%03d',$no_1).'/'.date('y');
        $this->no_2 = sprintf('%03d',$no_2).'/BBK-'.$setting->short_name.'/'.date('y');
        $this->no_3 = sprintf('%03d',$no_3).'/BBM-'.$setting->short_name.'/'.date('y');
        $this->no_4 = sprintf('%03d',$no_4).'/BKK-'.$setting->short_name.'/'.date('y');
        $this->no_5 = sprintf('%03d',$no_5).'/BKM-'.$setting->short_name.'/'.date('y');
        $this->no_6 = sprintf('%03d',$no_6).'/BBKT-'.$setting->short_name.'/'.date('y');
        $this->no_7 = sprintf('%03d',$no_7).'/BBMT-'.$setting->short_name.'/'.date('y');
        $this->c16 = COA::where('coa_ras', 16)->first()->id ?? 16;
        $this->c45 = COA::where('coa_ras', 45)->first()->id ?? 45;
        $this->c175 = COA::where('coa_ras', 175)->first()->id ?? 175;
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
