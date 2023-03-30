<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderTruckingResource extends JsonResource
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
            'order_id' => $this->order_id,
            'tanggal' => date('d/m/y', strtotime($this->created_at)),
            'sj_kembali' => $this->sj_kembali?date('d/m/y', strtotime($this->sj_kembali)):'-',
            'sj_kembali_fa' => $this->sj_kembali_fa?date('d/m/y', strtotime($this->sj_kembali_fa)):'-',
            'date_sj_kembali' => $this->sj_kembali?date('Y-m-d', strtotime($this->sj_kembali)):'-',
            'date_sj_kembali_fa' => $this->sj_kembali_fa?date('Y-m-d', strtotime($this->sj_kembali_fa)):'-',
            'invoice' => $this->invoice,
            'customer' => $this->customer->nama,
            'job' => $this->order ? $this->order->job.'-'.sprintf('%02d',$this->order->no_job) : '-',
            'sopir' => $this->sopir->nama,
            'nopol' => $this->kendaraan->nopol,
            'container' => $this->container ?? '-',
            'seal' => $this->seal ?? '-',
            'dari' => 'PERAK',
            'tujuan' => $this->tujuan,
            'tipe' => $this->tipe,
            'tagihan' => number_format($this->tagihan,0,',','.'),
            'sangu' => number_format($this->sangu,0,',','.'),
            'simpanan' => number_format($this->simpanan,0,',','.'),
            'kuli' => number_format($this->kuli,0,',','.'),
        ];
    }
}
