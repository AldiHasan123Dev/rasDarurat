<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderBiayaResource extends JsonResource
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
            'job' => $this->order->job,
            'no_job' => $this->order->job.'-'.sprintf($this->order->no_job),
            'pembayar' => $this->order->tarif->customer->nama,
            'penerima' => $this->order->penerima->nama,
            'tujuan' => $this->order->tarif->tujuan_lokasi->nama,
            'shipment' => $this->order->tarif->shipmentInfo->nama,
            'container' => $this->order->container,
            'seal' => $this->order->seal,
            'kapal' => $this->order->jadwal_kapal->kapal->nama,
            'tgl_dcf' => $this->tgl_dcf ? date('d/m/y',strtotime($this->tgl_dcf)) : '-',
            'tgl_opt' => $this->tgl_opt ? date('d/m/y',strtotime($this->tgl_opt)) : '-',
            'tgl_truk' => $this->tgl_truk ? date('d/m/y',strtotime($this->tgl_truk)) : '-',
            'tgl_kuli' => $this->tgl_kuli ? date('d/m/y',strtotime($this->tgl_kuli)) : '-',
            'nominal_do' => $this->nominal_do,
            'nominal_cleaning' => $this->nominal_cleaning,
            'nominal_fee' => number_format($this->nominal_fee),
            'nominal_opt' => number_format($this->nominal_opt),
            'nominal_truk' => number_format($this->nominal_truk),
            'nominal_kuli' => number_format($this->nominal_kuli),
        ];
    }
}
