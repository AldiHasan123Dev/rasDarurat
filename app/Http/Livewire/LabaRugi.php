<?php

namespace App\Http\Livewire;

use App\Models\COA;
use Livewire\Component;

class LabaRugi extends Component
{
    public $penjualan_usaha, $hpp, $biaya_usaha, $biaya_depresiasi, $biaya_lain, $biaya_keuangan1, $biaya_keuangan2;
    public $month, $months, $year;

    public function mount()
    {
        $this->months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $this->month = date('m');
        $this->year = date('Y');
        $this->penjualan_usaha = COA::where('kategori','A')->orderBy('kode')->get();
        $this->hpp = COA::where('kategori','B')->orderBy('kode')->get();
        $this->biaya_usaha = COA::where('kategori','C')->orderBy('kode')->get();
        $this->biaya_depresiasi = COA::where('kategori','D')->orderBy('kode')->get();
        $this->biaya_lain = COA::where('kategori','E')->orderBy('kode')->get();
        $this->biaya_keuangan1 = COA::where('kategori','F')->orderBy('kode')->get();
        $this->biaya_keuangan2 = COA::where('kategori','G')->orderBy('kode')->get();
    }

    public function render()
    {
        return view('livewire.laba-rugi');
    }

    public function changeMonth($month)
    {
        $this->month = sprintf('%02d',$month);
    }
}
