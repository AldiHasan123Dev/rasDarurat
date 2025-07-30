<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LapPelayaranResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'pelayaran' => optional($this->pelayaran)->nama ?? '-',
            'tujuan' => optional($this->lokasi)->nama ?? '-',
           'jadwal_kapal' => $this->jadwalKapal? $this->jadwalKapal->kapal->nama . ' - ' . $this->jadwalKapal->voyage . ' / ETD (' . $this->jadwalKapal->etd . ')' : '-',
            'voyage' => optional($this->jadwalKapal)->voyage ?? '-',
            'shipments' => optional($this->shipment)->nama ?? '-',
            'kondisi' => optional($this->kondisi1)->nama ?? '-',
            'harga' => $this->harga ?? '-',
            'comodity' => $this->comodity ?? '-',
            'sales' => $this->sales ?? '-',
            'keterangan' => $this->keterangan ?? '-',
            'tgl_info' => $this->tgl_info ?? '-',
           'status' => $this->status == 1 ? 'AKTIF' : 'NON-AKTIF',
            'class' => $this->status != 1 ? 'bg-light-danger' : '',
        ];
    }
}
