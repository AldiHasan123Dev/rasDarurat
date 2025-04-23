<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderBiayaTruckResource extends JsonResource
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
            'job' => $this->orderTruck->order ? $this->orderTruck->order->job.'-'.sprintf('%02d',$this->orderTruck->order->no_job) : '-',
            'order_trucking_id' => $this->order_trucking_id,
            'tujuan' => $this->orderTruck->tarif->tujuan->tujuanInfo->nama ?? '-',
            'container' => $this->orderTruck->container ?? '-',
            'seal' => $this->orderTruck->seal ?? '-',
            'customer' => $this->orderTruck->customer->nama,
            'sopir' => $this->orderTruck->sopir->nama ?? '-',
            'nopol' => $this->orderTruck->kendaraan->nopol.' | '.$this->orderTruck->kendaraan->milik,
            'tgl_sangu_kuli1' => $this->tgl_sangu_kuli1,
            'tgl_sangu_kuli2' => $this->tgl_sangu_kuli2,
            'tgl_sangu_kuli3' => $this->tgl_sangu_kuli3,
            'nominal_sangu_kuli1' => $this->nominal_sangu_kuli1,
            'nominal_sangu_kuli2' => $this->nominal_sangu_kuli2,
            'nominal_sangu_kuli3' => $this->nominal_sangu_kuli3,
            'nominal_tb_tl1' => $this->nominal_tb_tl1,
            'tgl_tb_tl' => $this->tgl_tb_tl,
            'nominal_stappel1' => $this->nominal_stappel1,
            'tgl_stappel' => $this->tgl_stappel,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
