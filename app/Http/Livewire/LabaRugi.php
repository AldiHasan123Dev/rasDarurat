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
        $this->penjualan_usaha = COA::whereIn('kode',['5.1.1','5.1.2','5.1.3','5.1.4','5.1.5'])->orderBy('kode')->get();
        $this->hpp = COA::whereIn('kode',['6.2.2','6.2.1','6.3.1','6.1.1','6.1','6.4','6.5','6.6','6.2.1.1','6.2.2.1'])->orderBy('kode')->get();
        $this->biaya_usaha = COA::whereIn('kode',['6.9','6.10','6.7.1','6.7.5','6.8.1','6.12.1','6.12.2','6.12.3','6.11.1','6.11.2'])->orderBy('kode')->get();
        $this->biaya_depresiasi = COA::whereIn('kode',['6.7.4','6.7.3'])->orderBy('kode')->get();
        $this->biaya_lain = COA::whereIn('kode',['5.2','5.3','7.1','6.13.1','6.1.2','6.8.2','6.1.3','6.7.2'])->orderBy('kode')->get();
        $this->biaya_keuangan1 = COA::whereIn('kode',['6.13.2'])->orderBy('kode')->get();
        $this->biaya_keuangan2 = COA::whereIn('kode',['6.14','6.3.2'])->orderBy('kode')->get();
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
