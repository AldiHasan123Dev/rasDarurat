<?php

namespace App\Http\Resources;

use App\Models\Jurnal;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JurnalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $class = '';
        $debit = Jurnal::where('nomor',$this->nomor)->sum('debit');
        $credit = Jurnal::where('nomor',$this->nomor)->sum('credit');
        if($debit!=$credit){
            $class = 'bg-light-danger';
        }
        return [
            'class' => $class,
            'id' => $this->id,
            'nomor' => $this->nomor,
            'coa_id' => $this->coa_id,
            'coa' => $this->coa,
            'coa_nama' => $this->coa->nama,
            'coa_kode' => $this->coa->kode,
            'order_id' => $this->order_id,
            'order' => $this->order,
            'job' => $this->order->job ?? '-',
            'no_job' => $this->order ? $this->order->job.'-'.sprintf('%02d',$this->order->no_job): '-',
            'order_trucking_id' => $this->order_trucking_id,
            'order_trucking' => $this->order_trucking,
            'nama' => $this->nama,
            'invoice' => $this->invoice,
            'container' => $this->container,
            'nopol' => $this->nopol,
            'debit' => number_format($this->debit,2,',','.'),
            'credit' => number_format($this->credit,2,',','.'),
            'created_at' => date('d/m/y',strtotime($this->created_at)),
        ];
    }
}
