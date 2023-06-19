<?php

namespace App\Http\Livewire;

use App\Models\COA;
use Livewire\Component;

class CreateTemplateJurnal extends Component
{
    public $kolom, $coa;

    public function mount()
    {
        $this->coa = COA::where('is_active',1)->orderBy('kode')->get();
        $this->kolom = 3;
    }

    public function render()
    {
        return view('livewire.create-template-jurnal');
    }

    public function addColumn()
    {
        $this->kolom += 1;
    }
}
