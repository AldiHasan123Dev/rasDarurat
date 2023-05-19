<?php

namespace App\Http\Livewire;

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
    public $lokasi, $pelayarans, $data, $active, $lokasiPelayaran;
    public $cont, $stuffing, $dari, $tujuan, $pelayaran;
    public $hpp, $margin, $r, $total, $pph, $total_pph, $ppn, $total_ppn;

    public function mount()
    {
        $this->hitung();
        $this->lokasi = TarifTrucking::where('customer_id',2)->where('tipe',20)->get();
        $this->pelayarans = Pelayaran::orderBy('nama')->get();
        $this->cont = 20;
        $this->stuffing = 'dalam';
        $this->dari = 41;
        $this->tujuan = 114;
        $this->pelayaran = 3;
        $this->active = false;
        $this->lokasiPelayaran = LSS::get();
    }

    public function changeCont()
    {
        $this->lokasi = TarifTrucking::where('customer_id',2)->where('tipe',$this->cont)->get();
    }

    public function render()
    {
        return view('livewire.estimasi-hpp');
    }

    public function hitung()
    {
        $truk = TarifTrucking::find($this->dari);
        $lss = LSS::where('lokasi_id',$this->tujuan)->first();
        $thc = THC::where('lokasi_id',$this->tujuan)->first();
        $agen = TarifAgen::where('dari',$this->dari)->where('tujuan',$this->tujuan)->whereHas('shipment', function($q){
                    $q->where('nama','LIKE','%'.$this->cont.'%');
                })->where('is_active',1)->first();
        $pelayarant = TarifPelayaran::where('tujuan',$this->tujuan)->whereHas('shipment', function($q){
                    $q->where('nama','LIKE','%'.$this->cont.'%');
                })->where('is_active',1)->first();

        $stuffing = $this->stuffing == 'dalam' ? 'luar' : 'dalam';
        $lain = Lain::where('nama','NOT LIKE','%STUFFING '.$stuffing.'%')->get();
        $data['TRUCKING'] = $truk->tarif ?? 0;
        $data['AGEN'] = $agen->tarif ?? 0;
        $data['PELAYARAN'] = $pelayarant->tarif ?? 0;
        $data['LSS'] = $this->cont == 20 ? ($lss->cont_20??0) : ($lss->cont_40??0);
        $data['THC'] = $this->cont == 20 ? ($thc->cont_20??0) : ($thc->cont_40??0);
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
