<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LaporanPPNResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'invoice' => $this->invoice,
            'npwp' => $this->pembayar->npwp ,
            'nik' => $this->pembayar->nik ,
            'nama' => $this->pembayar->nama ,
            'nama_npwp' => $this->pembayar->nama_npwp ,
            'alamat_npwp' => $this->pembayar->alamat_npwp,
            'tanggal_faktur' => date('d/m/y', strtotime($this->created_at)),
            'tujuan' => $this->tujuan ,
            'uraian' => $this->keterangan ,
            'daftar_faktur_pajak' => $this->nsfp ,
            'sub_total' => number_format(ceil($this->sub_total)),
            'ppn' => number_format($this->ppn),
            'total' => number_format(ceil($this->ppn + $this->sub_total)),
            'pph' =>  number_format($this->pph),
            'job' => $this->no_job(),
        ];
    }
}
