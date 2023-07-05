<?php

namespace App\Http\Livewire;

use App\Models\COA;
use App\Models\TemplateJurnal;
use Livewire\Component;

class CreateTemplateJurnal extends Component
{
    public $kolom, $coa, $template_id, $template;

    public function mount($template_id = null)
    {
        $this->coa = COA::where('is_active',1)->orderBy('kode')->get();
        $this->kolom = 3;
        $this->template_id = $template_id;
        $this->template = TemplateJurnal::find($template_id);

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
