<?php

namespace App\Http\Livewire;

use App\Models\Lokasi;
use App\Models\Pelayaran;
use Livewire\Component;

class EstimasiHpp extends Component
{
    public $lokasi, $pelayarans;
    public $cont, $stuffing, $dari, $tujuan, $pelayaran;

    public function mount()
    {
        $this->lokasi = Lokasi::orderBy('nama')->get();
        $this->pelayarans = Pelayaran::orderBy('nama')->get();
        $this->cont = 20;
        $this->stuffing = 'dalam';
        $this->dari = 112;
        $this->tujuan = 114;
        $this->pelayaran = 3;
    }

    public function render()
    {
        return view('livewire.estimasi-hpp');
    }

    public function hitung()
    {
        dd($this->cont, $this->stuffing, $this->dari, $this->tujuan, $this->pelayaran);
    }
}
