<?php

namespace App\Http\Resources;

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
        return [
            'id' => $this->id,
            'nomor' => $this->nomor,
            'coa_id' => $this->coa_id,
            'coa' => $this->coa,
            'coa_nama' => $this->coa->nama,
            'coa_kode' => $this->coa->kode,
            'order_id' => $this->order_id,
            'order' => $this->order,
            'order_trucking_id' => $this->order_trucking_id,
            'order_trucking' => $this->order_trucking,
            'nama' => $this->nama,
            'invoice' => $this->invoice,
            'container' => $this->container,
            'nopol' => $this->nopol,
            'debit' => $this->debit,
            'credit' => $this->credit,
            'created_at' => date('d/m/y',strtotime($this->created_at)),
        ];
    }
}
