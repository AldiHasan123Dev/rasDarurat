<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TarifAgenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $class = '';
        if($this->is_active!=1){
            $class = 'bg-light-danger';
        }
        return [
            'id' => $this->id,
            'agen_id' => $this->agen_id,
            'penerima_id' => $this->penerima_id,
            'pembayar_id' => $this->pembayar_id,
            'agen' => $this->agen->nama,
            'tanggal' => is_null($this->tanggal) ? '-' : date('d/m/y',strtotime($this->tanggal)),
            'dari' => $this->dariInfo->nama,
            'tujuan' => $this->tujuanInfo->nama,
            'tipe' => $this->shipment->nama,
            'pembayar' => $this->pembayar->nama ?? '-',
            'penerima' => $this->penerima->nama ?? '-',
            'tarif' => number_format($this->tarif),
            'kubikasi' => number_format($this->kubikasi),
            'keterangan' => $this->keterangan,
            'is_active' => $this->is_active==1?'AKTIF':'NON AKTIF',
            'class' => $class,
            'dari_id' => $this->dari,
            'tujuan_id' => $this->tujuan,
            'tipe_id' => $this->tipe,
            'date_tanggal' => $this->tanggal,
            'tarif_nominal' => $this->tarif,
            'kubikasi_nominal' => $this->kubikasi,
        ];
    }
}
