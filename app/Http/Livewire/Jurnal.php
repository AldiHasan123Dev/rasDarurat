<?php

namespace App\Http\Livewire;

use App\Models\COA;
use Livewire\Component;

class Jurnal extends Component
{
    public $coa, $coa_id, $tipe;
    public $form;
    public $idx;

    public function mount()
    {
        $this->idx = 3;
        $this->form = array();
        $this->coa_id = null;
        $this->tipe = null;
        $this->coa = COA::all();
    }

    public function render()
    {
        return view('livewire.jurnal');
    }

    public function addColumn()
    {
        $this->idx+=1;
    }
}
