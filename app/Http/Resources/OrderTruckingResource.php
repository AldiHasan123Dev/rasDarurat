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
        $keterangan = '';
        if($this->ambil_empty_tambak_langon==1){
            $keterangan .= 'Ambil Empty Tambak Langon; ';
        }
        if($this->ambil_empty_teluk_langon==1){
            $keterangan .= 'Ambil Empty Teluk Lamong; ';
        }
        if($this->bongkar_full_teluk_langon==1){
            $keterangan .= 'Bongkar Full Teluk Lamong; ';
        }
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'customer_id' => $this->customer_id,
            'kendaraan_id' => $this->kendaraan_id,
            'sopir_id' => $this->sopir_id,
            'sangu_id' => $this->tarif->tujuan_id,
            'tanggal' => date('d/m/y', strtotime($this->created_at)),
            'sj_kembali' => $this->sj_kembali?date('d/m/y', strtotime($this->sj_kembali)):'-',
            'sj_kembali_fa' => $this->sj_kembali_fa?date('d/m/y', strtotime($this->sj_kembali_fa)):'-',
            'date_sj_kembali' => $this->sj_kembali?date('Y-m-d', strtotime($this->sj_kembali)):'-',
            'date_sj_kembali_fa' => $this->sj_kembali_fa?date('Y-m-d', strtotime($this->sj_kembali_fa)):'-',
            'invoice' => $this->invoice,
            'customer' => $this->customer->nama,
            'pembayar' => $this->order ? $this->order->tarif->customer->nama : '-',
            'job' => $this->order ? $this->order->job.'-'.sprintf('%02d',$this->order->no_job) : '-',
            'sopir' => $this->sopir->nama,
            'nopol' => $this->kendaraan->nopol,
            'container' => $this->container ?? '-',
            'seal' => $this->seal ?? '-',
            'dari' => 'PERAK',
            'tujuan' => $this->tujuan,
            'tipe' => $this->tipe,
            'tarif' => $this->tarif ? number_format($this->tarif->tarif,0,',','.') : '-',
            'sangu' => number_format($this->sangu,0,',','.'),
            'simpanan' => number_format($this->simpanan,0,',','.'),
            'kuli' => number_format($this->kuli,0,',','.'),
            'borongan' => number_format($this->borongan,0,',','.'),
            'tambah_isi' => number_format($this->tambah_isi,0,',','.'),
            'tambah_solar' => number_format($this->tambah_solar,0,',','.'),
            'tb_tl' => number_format($this->tb_tl,0,',','.'),
            'tally' => number_format($this->tally,0,',','.'),
            'uang_makan' => number_format($this->uang_makan,0,',','.'),
            'total_sopir' => number_format($this->total_sopir,0,',','.'),
            'tgl_total' =>  $this->tgl_total?date('Y-m-d', strtotime($this->tgl_total)):'-',
            'keterangan' => $keterangan,
            'ambil_empty_tambak_langon' => $this->ambil_empty_tambak_langon,
            'ambil_empty_teluk_langon' => $this->ambil_empty_teluk_langon,
            'bongkar_full_teluk_langon' => $this->bongkar_full_teluk_langon,
        ];
    }
}
