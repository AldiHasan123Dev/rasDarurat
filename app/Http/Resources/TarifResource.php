<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TarifResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'customer' => $this->customer->nama ?? '-',
            'jadwal_kapal' => new JadwalKapalResource($this->jadwal_kapal),
            'dari' => $this->dari_lokasi->nama ?? '-',
            'tujuan' => $this->tujuan_lokasi->nama ?? '-',
            'shipment' => $this->shipmentInfo->nama ?? '-',
            'kondisi' => $this->kondisiInfo->nama ?? '-',
            'satuan' => $this->satuanInfo->nama ?? '-',
            'tarif' => $this->tarif,
            'keterangan' => $this->keterangan,
            'unit' => $this->unit,
            'min_qty' => $this->min_qty,
        ];
    }
}
