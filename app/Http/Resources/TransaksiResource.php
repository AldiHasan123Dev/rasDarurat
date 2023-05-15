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
            'tipe_invoice' => $this->tipe_invoice,
            'order_id' => $this->order_id,
            'invoice' => $this->invoice,
            'npwp' => $this->pembayar->npwp ?? '-',
            'nik' => $this->pembayar->nik ?? '-',
            'nama' => $this->pembayar->nama ?? '-',
            'nama_npwp' => $this->pembayar->nama_npwp ?? '-',
            'alamat_npwp' => $this->pembayar->alamat_npwp ?? '-',
            'tujuan' => $this->tujuan ,
            'uraian' => $this->keterangan ,
            'daftar_faktur_pajak' => $this->nsfp ,
            'job' => $this->job,
            'no_job' => $this->job.'-01/'.sprintf('%02d',$this->jobs->count()),
            'pembayar' => $this->pembayar->nama ?? '-',
            'tanggal_kirim' => is_null($this->tanggal_kirim) ? '-' : date('d/m/y', strtotime($this->tanggal_kirim)),
            'tanggal_kirim_format' => is_null($this->tanggal_kirim) ? '-' : date('Y-m-d', strtotime($this->tanggal_kirim)),
            'tanggal' => is_null($this->created_at) ? '-' : date('d/m/y', strtotime($this->created_at)),
            'tanggal_format' => is_null($this->created_at) ? '-' : date('Y-m-d', strtotime($this->created_at)),
            'total' => number_format(ceil($this->total)),
            'sub_total' => number_format(ceil($this->sub_total)),
            'ppn' => number_format(round($this->ppn)),
            'pph' =>  number_format(round($this->pph)),
            'bupot' =>  is_null($this->bupot) ? '-' : number_format(round($this->bupot)),
            'no_bupot' => $this->no_bupot ?? '-',
            'selisih_bupot' => $this->selisih_bupot ?? '-',
            'masa_bupot' => $this->masa_bupot ?? '-',
            'tanggal_bupot' => is_null($this->tanggal_bupot) ? '-' : date('d/m/y', strtotime($this->tanggal_bupot)),
            'ppn_subtotal' =>  number_format(round($this->ppn) + ceil($this->sub_total)),
        ];
    }
}
