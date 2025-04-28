<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Carbon\Carbon;
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
        $orderTruck = $this->orderTruck;

        return [
            'id' => $this->id,
            'job' => $orderTruck && $orderTruck->order
                ? $orderTruck->order->job . '-' . sprintf('%02d', $orderTruck->order->no_job)
                : '-',
            'tgl_muat' => $orderTruck && $orderTruck->tgl_muat 
            ? \Carbon\Carbon::parse($orderTruck->tgl_muat)->format('d/m/y') 
            : '-',
            'order_trucking_id' => $this->order_trucking_id,
            'tujuan' => optional($orderTruck?->tarif?->tujuan?->tujuanInfo)->nama ?? '-',
            'container' => $orderTruck?->container ?? '-',
            'seal' => $orderTruck?->seal ?? '-',
            'customer' => $orderTruck?->customer?->nama ?? '-',
            'sopir' => $orderTruck?->sopir?->nama ?? '-',
            'nopol' => $orderTruck && $orderTruck->kendaraan
                ? $orderTruck->kendaraan->nopol . ' | ' . $orderTruck->kendaraan->milik
                : '-',

            'tgl_sangu_kuli1' => $this->tgl_sangu_kuli1,
            'tgl_sangu_kuli2' => $this->tgl_sangu_kuli2,
            'tgl_sangu_kuli3' => $this->tgl_sangu_kuli3,

            'nominal_sangu_kuli1' => $this->nominal_sangu_kuli1,
            'nominal_sangu_kuli2' => $this->nominal_sangu_kuli2,
            'nominal_sangu_kuli3' => $this->nominal_sangu_kuli3,

            'nominal_tb_tl1' => $this->nominal_tb_tl1,
            'nominal_tb_tl2' => $this->nominal_tb_tl2,
            'tgl_tb_tl' => $this->tgl_tb_tl,
            'tgl_tb_tl1' => $this->tgl_tb_tl1,

            'nominal_stappel1' => $this->nominal_stappel1,
            'tgl_stappel' => $this->tgl_stappel,

            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
