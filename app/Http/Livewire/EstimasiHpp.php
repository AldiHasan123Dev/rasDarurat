<?php

namespace App\Http\Livewire;

use App\Models\Lain;
use App\Models\Lokasi;
use App\Models\LSS;
use App\Models\Pelayaran;
use App\Models\TarifAgen;
use App\Models\TarifPelayaran;
use App\Models\THC;
use Livewire\Component;

class EstimasiHpp extends Component
{
    public $lokasi, $pelayarans, $data, $active;
    public $cont, $stuffing, $dari, $tujuan, $pelayaran;

    public function mount()
    {
        $this->hitung();
        $this->lokasi = LSS::get();
        $this->pelayarans = Pelayaran::orderBy('nama')->get();
        $this->cont = 20;
        $this->stuffing = 'dalam';
        $this->dari = 112;
        $this->tujuan = 114;
        $this->pelayaran = 3;
        $this->active = false;
    }

    public function render()
    {
        return view('livewire.estimasi-hpp');
    }

    public function hitung()
    {
        $lss = LSS::where('lokasi_id',$this->tujuan)->first();
        $thc = THC::where('lokasi_id',$this->tujuan)->first();
        $agen = TarifAgen::where('dari',$this->dari)->where('tujuan',$this->tujuan)->whereHas('shipment', function($q){
                    $q->where('nama','LIKE','%'.$this->cont.'%');
                })->where('is_active',1)->first();
        $pelayarant = TarifPelayaran::where('dari',$this->dari)->where('tujuan',$this->tujuan)->whereHas('shipment', function($q){
                    $q->where('nama','LIKE','%'.$this->cont.'%');
                })->where('is_active',1)->first();
        $lain = Lain::get();
        $data['AGEN'] = $agen->tarif ?? 0;
        $data['PELAYARAN'] = $pelayarant->tarif ?? 0;
        $data['LSS'] = $this->cont == 20 ? ($lss->cont_20??0) : ($lss->cont_40??0);
        $data['THC'] = $this->cont == 20 ? ($thc->cont_20??0) : ($thc->cont_40??0);
        foreach ($lain as $item ) {
            $data[$item->nama] = $this->cont == 20 ? $item->cont_20 : $item->cont_40;
        };
        $this->data = $data;
        $this->active = true;
    }
}
