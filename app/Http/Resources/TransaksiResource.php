<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransaksiResource extends JsonResource
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
            'invoice' => $this->invoice,
            'npwp' => $this->pembayar->npwp ,
            'nik' => $this->pembayar->nik ,
            'nama' => $this->pembayar->nama ,
            'nama_npwp' => $this->pembayar->nama_npwp ,
            'alamat_npwp' => $this->pembayar->alamat_npwp,
            'tujuan' => $this->tujuan ,
            'uraian' => $this->keterangan ,
            'daftar_faktur_pajak' => $this->nsfp ,
            'job' => $this->job,
            'no_job' => $this->job.'-01/'.sprintf('%02d',$this->jobs->count()),
            'pembayar' => $this->pembayar->nama,
            'tanggal_kirim' => is_null($this->tanggal_kirim) ? '-' : date('d/m/y', strtotime($this->tanggal_kirim)),
            'tanggal' => is_null($this->created_at) ? '-' : date('d/m/y', strtotime($this->created_at)),
            'total' => number_format(ceil($this->total)),
            'sub_total' => number_format(ceil($this->sub_total)),
            'ppn' => number_format($this->ppn),
            'pph' =>  number_format($this->pph),
        ];
    }
}
