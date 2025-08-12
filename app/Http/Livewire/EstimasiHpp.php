<?php

namespace App\Http\Livewire;

use App\Models\Agen;
use App\Models\Customer;
use App\Models\Lain;
use App\Models\Lokasi;
use App\Models\LSS;
use App\Models\Pelayaran;
use App\Models\TarifAgen;
use App\Models\TarifPelayaran;
use App\Models\TarifTrucking;
use App\Models\THC;
use Livewire\Component;

class EstimasiHpp extends Component
{
    public $lokasi, $pelayarans, $agens, $data, $active, $lokasiPelayaran, $customers;
    public $cont, $stuffing, $dari, $tujuan, $pelayaran, $agen, $pembayar_id;
    public $hpp, $margin, $r, $total, $pph, $total_pph, $ppn, $total_ppn;

    public function mount()
    {
        $this->lokasi = Lokasi::orderBy('nama')->get();
        $this->pelayarans = Pelayaran::orderBy('nama')->get();
        $this->cont = 20;
        $this->stuffing = 'dalam';
        $this->dari = 'PELABUHAN SURABAYA';
        $this->tujuan = 'JAYAPURA';
        $this->pelayaran = 3;
        $this->lokasiPelayaran = Lokasi::orderBy('nama')->get();
        $this->agens = Agen::where('kota',$this->tujuan)->get();
        $this->agen = 1;
        $this->customers = Customer::orderBy('nama')->get(['id','nama']);
        $this->hitung();
        $this->active = false;
    }

    public function changeCont()
    {
        $this->lokasi = TarifTrucking::where('customer_id',2)->where('tipe',$this->cont)->get();
    }

    public function changeTujuan()
    {
        $this->agens = Agen::where('kota',$this->tujuan)->get();
        $this->agen = '';
    }

    public function render()
    {
        return view('livewire.estimasi-hpp');
    }

    public function hitung()
    {
        $truk = TarifTrucking::find($this->dari);
        $lss = LSS::whereHas('lokasi',function($q){
            $q->where('nama','like','%'.$this->tujuan.'%');
        })->first();
        $thc = THC::whereHas('lokasi',function($q){
            $q->where('nama','like','%'.$this->tujuan.'%');
        })->first();
        $agen = TarifAgen::where('agen_id',$this->agen)
                    ->where('pembayar_id',$this->pembayar_id)
                    ->whereHas('dariInfo', function($q){
                        $q->where('nama',$this->dari);
                    })->whereHas('tujuanInfo',function($q){
                        $q->where('nama',$this->tujuan);
                    })->where('is_active',1)->first();

$pelayarant = TarifPelayaran::where('pelayaran_id',$this->pelayaran)
    ->whereHas('tujuanInfo',function($q){
        $q->where('nama',$this->tujuan);
    })
     ->whereHas('port',function($q){
        $q->where('name',$this->dari);
    })
    ->whereHas('shipment', function($q){
        $q->where('nama','LIKE','%'.$this->cont.'%');
    })
    ->whereNull('deleted_at')
    ->where('is_active',1)
    ->first(); // lihat SQL mentahnya

        $stuffing = $this->stuffing == 'dalam' ? 'luar' : 'dalam';
        $lain = Lain::where('nama','NOT LIKE','%'.$stuffing.'%')->get();
        $data['TRUCKING'] = $this->stuffing=='dalam'? 0 : ($truk->tarif??0);
        $data['AGEN'] = $agen->tarif ?? 0;
        $data['PELAYARAN'] = $pelayarant->tarif ?? 0;
        $data['LSS'] = $this->cont == 20 ? ($lss->cont_20??0) : ($lss->cont_40??0);
        $data['THC TUJUAN'] = $this->cont == 20 ? ($thc->cont_20??0) : ($thc->cont_40??0);
        foreach ($lain as $item ) {
            $data[$item->nama] = $this->cont == 20 ? $item->cont_20 : $item->cont_40;
        };
        $hpp = 0;
        foreach ($data as $item ) {
            $hpp+=(int)$item;
        }
        $this->data = $data;
        $this->active = true;
        $this->hpp = $hpp;
        $this->r = $this->cont==20?600000:1300000;
        $this->margin = $this->r/$hpp*100;
        $this->total = $this->r + $hpp;
        $this->pph = ($this->r+$hpp) * 0.02;
        $this->total_pph = $this->pph + $this->total;
        $this->ppn = $this->total_pph * 0.01;
        $this->total_ppn = $this->ppn + $this->total_pph;
    }

    public function hitungData()
    {
        $hpp = 0;
        foreach ($this->data as $item ) {
            $hpp+=(int)$item;
        }
        $this->hpp = $hpp;
        $this->margin = $this->r/$hpp*100;
        $this->total = $this->r + $hpp;
        $this->pph = ($this->r+$hpp) * 0.02;
        $this->total_pph = $this->pph + $this->total;
        $this->ppn = $this->total_pph * 0.01;
        $this->total_ppn = $this->ppn + $this->total_pph;
    }
}
