<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JadwalKapalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'voyage' => $this->voyage,
            'kapal' => $this->kapal->nama,
            'etd' => date('d/m/y', strtotime($this->etd)),
        ];
    }
}
