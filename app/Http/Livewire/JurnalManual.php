<?php

namespace App\Http\Livewire;

use App\Models\COA;
use App\Models\Jurnal as ModelsJurnal;
use App\Models\Kendaraan;
use App\Models\Order;
use App\Models\OrderTrucking;
use App\Models\Setting;
use App\Models\TemplateJurnal;
use App\Models\TemplateJurnalItem;
use Illuminate\Support\Carbon;
use Livewire\Component;

class JurnalManual extends Component
{
    public $coa, $coa_id, $tipe, $jurnals, $jurnal_id, $orders, $bgs, $agens, $inv_vendor, $invx, $order_trucking, $kendaraan, $templates;
    public $no_1, $no_2, $no_3, $no_4, $no_5, $no_6, $no_7;
    public $form, $order, $is_apply;
    public $debit_idx, $credit_idx;
    public $c16, $c45, $c175, $relasi;

    public function mount()
    {
        $no_1 = ModelsJurnal::where('tipe','JNL')->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->max('no') + 1;
        $no_2 = ModelsJurnal::where('tipe','BBK')->whereYear('created_at',date('Y'))->max('no') + 1;
        $no_3 = ModelsJurnal::where('tipe','BBM')->whereYear('created_at',date('Y'))->max('no') + 1;
        $no_4 = ModelsJurnal::where('tipe','BKK')->whereYear('created_at',date('Y'))->max('no') + 1;
        $no_5 = ModelsJurnal::where('tipe','BKM')->whereYear('created_at',date('Y'))->max('no') + 1;
        $no_6 = ModelsJurnal::where('tipe','BBKT')->whereYear('created_at',date('Y'))->max('no') + 1;
        $no_7 = ModelsJurnal::where('tipe','BBMT')->whereYear('created_at',date('Y'))->max('no') + 1;
        $setting = Setting::find(1);
        $now = Carbon::now()->addMonths(1)->format('Y-m-d');
        $last = Carbon::now()->subMonths(14)->format('Y-m-d');
        $this->order_trucking = OrderTrucking::whereBetween('created_at', [$last, $now])
        ->whereNotNull('invoice')
        ->where('invoice', 'like', '%RAS-LT%') // Kondisi NOT LIKE untuk mengecualikan 'RAS-LT'
        ->select('invoice', 'id', 'container') // Pilih hanya kolom tertentu
        ->distinct() // Hapus duplikat berdasarkan kolom terpilih
        ->orderBy('invoice') // Urutkan berdasarkan kolom invoice
        ->get();    
        $this->orders = Order::whereBetween('created_at', [$last, $now])
        ->whereNotNull('invoice')
        ->select('invoice', 'id', 'container') // Pilih hanya kolom invoice
        ->distinct() // Tambahkan distinct untuk menghapus duplikat
        ->orderBy('invoice') // Urutkan berdasarkan invoice (opsional)
        ->get();
        $this->inv_vendor = OrderTrucking::whereBetween('created_at', [$last, $now])
        ->whereNotNull('invoice')
        ->where('invoice', 'not like', '%RAS-LT%') // Tambahkan kondisi LIKE
        ->select('invoice', 'id', 'container') // Pilih hanya kolom tertentu
        ->distinct() // Hapus duplikat berdasarkan kolom terpilih
        ->orderBy('invoice') // Urutkan berdasarkan kolom invoice
        ->get();
        $this->invx = ModelsJurnal::whereNotNull('invoice_external')
        ->orderBy('invoice_external')
        ->distinct()
        ->pluck('invoice_external');    
        $this->agens = Order::whereBetween('created_at', [$last, $now])
        ->whereNotNull('invoice_agen')
        ->select('invoice_agen', 'id', 'container') // Pilih hanya kolom invoice
        ->distinct() // Tambahkan distinct untuk menghapus duplikat
        ->orderBy('invoice_agen') // Urutkan berdasarkan invoice (opsional)
        ->get();
        $this->bgs= ModelsJurnal::whereNotNull('no_bg')->orderBy('no_bg')->pluck('no_bg');
        $this->invx= ModelsJurnal::whereNotNull('invoice_external')->orderBy('invoice_external')->pluck('invoice_external');
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
        $this->no_2 = sprintf('%03d',$no_2).'/BBK-'.$setting->short_name.'/'.date('y');
        $this->no_3 = sprintf('%03d',$no_3).'/BBM-'.$setting->short_name.'/'.date('y');
        $this->no_4 = sprintf('%03d',$no_4).'/BKK-'.$setting->short_name.'/'.date('y');
        $this->no_5 = sprintf('%03d',$no_5).'/BKM-'.$setting->short_name.'/'.date('y');
        $this->no_6 = sprintf('%03d',$no_6).'/BBKT-'.$setting->short_name.'/'.date('y');
        $this->no_7 = sprintf('%03d',$no_7).'/BBMT-'.$setting->short_name.'/'.date('y');
        $this->c16 = COA::where('coa_ras', 16)->first()->id ?? 16;
        $this->c45 = COA::where('coa_ras', 45)->first()->id ?? 45;
        $this->c175 = COA::where('coa_ras', 175)->first()->id ?? 175;
        $last_relasi = Carbon::now()->subMonths(13)->format('Y-m-d');
        $this->relasi = ModelsJurnal::where('created_at', '>=', $last_relasi)->distinct('nomor')->orderBy('nomor')->pluck('nomor')->toArray();
    }

    public function render()
    {
        return view('livewire.jurnal-manual');
    }

}
