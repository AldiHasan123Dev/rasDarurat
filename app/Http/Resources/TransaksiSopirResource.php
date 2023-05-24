<?php

namespace App\Http\Resources;

use App\Models\OrderTrucking;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransaksiSopirResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $container  = '';
        $orders = json_decode($this->order_id);
        $orders = OrderTrucking::whereIn('id',$orders)->get();
        foreach ($orders as $order ) {
            $container .= $order->container.' / '.$order->seal .'; ';
        }
        return [
            'tgl_invoice' => date('d/m/y', strtotime($this->tgl_invoice)),
            'invoice' => $this->invoice,
            'sopir' => $this->sopir->nama ?? '-',
            'total' => number_format($this->total),
            'container' => $container,
        ];
    }
}
