<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransaksiTruckingResource extends JsonResource
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
            'jurnal_piutang' => $this->jurnal_piutang,
            'jurnal_hutang' => $this->jurnal_hutang,
            'customer' => $this->customer->nama,
            'invoice' => $this->invoice,
            'tgl_invoice' => date('d/m/y', strtotime($this->tgl_invoice)),
            'rit' => $this->rit.' RIT',
            'lain_lain' => number_format($this->lain_lain),
            'pph' => number_format($this->pph),
            'total' => number_format($this->total),
            'total_pph' => number_format($this->total - $this->pph),
        ];
    }
}
