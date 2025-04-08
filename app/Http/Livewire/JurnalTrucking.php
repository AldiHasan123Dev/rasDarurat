<?php

namespace App\Http\Livewire;

use App\Models\COA;
use App\Models\Jurnal as ModelsJurnal;
use App\Models\Order;
use App\Models\OrderTrucking;
use App\Models\Setting;
use App\Models\TemplateJurnal;
use App\Models\TemplateJurnalItem;
use Illuminate\Support\Carbon;
use Livewire\Component;

class JurnalTrucking extends Component
{
    public $coa, $coa_id, $tipe, $orders, $jurnals, $jurnal_id, $template_id, $templates, $inv_trucking, $inv_vendor, $template, $template_count;
    public $no_1, $no_2, $no_3, $no_4, $no_5, $no_6, $no_7;
    public $form, $order, $is_apply;
    public $debit_idx, $credit_idx;
    public $c16, $c45, $c175, $c31, $relasi;

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
        $last = Carbon::now()->subMonths(13)->format('Y-m-d');
        $setting = Setting::find(1);
        $this->invx = ModelsJurnal::whereNotNull('invoice_external')
        ->orderBy('invoice_external')
        ->distinct()
        ->pluck('invoice_external');   
        $this->inv_trucking = OrderTrucking::whereBetween('created_at', [$last, $now])
        ->whereNotNull('invoice')
        ->where('invoice', 'like', '%RAS-LT%') // Kondisi NOT LIKE untuk mengecualikan 'RAS-LT'
        ->select('invoice', 'id', 'container') // Pilih hanya kolom tertentu
        ->distinct() // Hapus duplikat berdasarkan kolom terpilih
        ->orderBy('invoice') // Urutkan berdasarkan kolom invoice
        ->get();    
        $this->inv_vendor = OrderTrucking::whereBetween('created_at', [$last, $now])
        ->whereNotNull('invoice')
        ->where('invoice', 'not like', '%RAS-LT%') // Tambahkan kondisi LIKE
        ->select('invoice', 'id', 'container') // Pilih hanya kolom tertentu
        ->distinct() // Hapus duplikat berdasarkan kolom terpilih
        ->orderBy('invoice') // Urutkan berdasarkan kolom invoice
        ->get();
    
        $this->order = null;
        $this->template_id = null;
        $this->template = null;
        $this->is_apply = false;
        $this->templates = TemplateJurnal::all();
        $this->coa = COA::where('is_active',1)->orderBy('kode')->get();
        $this->orders = OrderTrucking::whereBetween('created_at',[$last,$now])->select('id','container','seal','invoice')->orderBy('container')->get();
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
        $this->c31 = COA::where('coa_ras', 31)->first()->id ?? 31;
        $last_relasi = Carbon::now()->subMonths(13)->format('Y-m-d');
        $this->relasi = ModelsJurnal::where('created_at', '>=', $last_relasi)->distinct('nomor')->orderBy('nomor')->pluck('nomor')->toArray();
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
