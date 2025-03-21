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
        $keterangan = $this->keterangan.'; ';
        if($this->ambil_empty_tambak_langon==1){
            $keterangan .= 'Ambil Empty Tambak Langon; ';
        }
        if($this->ambil_empty_teluk_langon==1){
            $keterangan .= 'Ambil Empty Teluk Lamong; ';
        }
        if($this->bongkar_full_teluk_langon==1){
            $keterangan .= 'Bongkar Full Teluk Lamong; ';
        }

        $class = '';

        if(is_null($this->order_id) && $this->customer_id==2){
            $class = 'bg-light-dark';
        }
        if(!is_null($this->order_id) && $this->customer_id==2 && $this->kendaraan->milik=='R2'){
            $class = 'bg-light-success';
        }
        if(!is_null($this->sj_kembali_fa)&&is_null($this->tgl_total)){
            $class = 'bg-light-primary';
        }
        if(!is_null($this->sj_kembali_fa)&&!is_null($this->tgl_total)&&is_null($this->tgl_invoice)){
            $class = 'bg-light-warning';
        }
        if(!is_null($this->sj_kembali_fa)&&!is_null($this->tgl_total)&&!is_null($this->tgl_invoice)){
            $class = 'bg-light-danger';
        }
        if($this->kendaraan->milik=='vendor'){
            $class = 'bg-purple';
        }

        return [
            'class' => $class,
            'id' => $this->id,
            'order_id' => $this->order_id,
            'invoice' => $this->invoice ?? '-',
            'tgl_invoice' => $this->tgl_invoice ? date('d/m/y', strtotime($this->tgl_invoice)) : '-',
            'customer_id' => $this->customer_id,
            'kendaraan_id' => $this->kendaraan_id,
            'sopir_id' => $this->sopir_id,
            'sangu_id' => $this->tarif->tujuan_id ?? '-',
            'tanggal' => date('d/m/y', strtotime($this->created_at)),
            'tgl_muat' => $this->tgl_muat ? date('d/m/y', strtotime($this->tgl_muat)) : '-',
            'sj_kembali' => $this->sj_kembali?date('d/m/y', strtotime($this->sj_kembali)):'-',
            'sj_kembali_fa' => $this->sj_kembali_fa?date('d/m/y', strtotime($this->sj_kembali_fa)):'-',
            'date_sj_kembali' => $this->sj_kembali?date('Y-m-d', strtotime($this->sj_kembali)):'-',
            'date_sj_kembali_fa' => $this->sj_kembali_fa?date('Y-m-d', strtotime($this->sj_kembali_fa)):'-',
            'date_tgl_muat' => $this->tgl_muat?date('Y-m-d', strtotime($this->tgl_muat)):'-',
            'customer' => $this->customer->nama,
            'pembayar' => $this->order ? $this->order->tarif->customer->nama : '-',
            'kapal' => $this->order ? ($this->order->jadwal_kapal ? $this->order->jadwal_kapal->kapal->nama : '-') : '-',
            'voyage' => $this->order ? ($this->order->jadwal_kapal ? $this->order->jadwal_kapal->voyage : '-') : '-',
            'shipment' => $this->order ? $this->order->tarif->shipmentInfo->nama : '-',
            'job' => $this->order ? $this->order->job.'-'.sprintf('%02d',$this->order->no_job) : '-',
            'trucking' => $this->order ? $this->order->trucking: '-',
            'sopir' => $this->sopir->nama ?? '-',
            'nopol' => $this->kendaraan->nopol.' | '.$this->kendaraan->milik,
            'container' => $this->container ?? '-',
            'seal' => $this->seal ?? '-',
            'dari' => 'PERAK',
            'dari_xpdc' => $this->order ? $this->order->tarif->dari_lokasi->nama : '-',
            'tujuan' => $this->tarif->tujuan->tujuanInfo->nama ?? '-',
            'tipe' => $this->tipe,
            'tarif' => number_format($this->tarif_nominal),
            'tarif_vendor' => $this->tarif_vendor ? number_format($this->tarif_vendor) : '-',
            'sangu' => number_format($this->sangu),
            'simpanan' => number_format($this->simpanan),
            'kuli' => number_format($this->kuli),
            'borongan_kuli' => number_format($this->borongan_kuli),
            'simpanan_kuli' => number_format($this->simpanan_kuli),
            'sangu_kuli' => number_format($this->sangu_kuli),
            'op' => number_format($this->op),
            'cleaning' => number_format($this->cleaning),
            'stappel' => number_format($this->stappel),
            'lain_lain' => number_format($this->lain_lain),
            'lain' => number_format($this->lain),
            'pph_21' => number_format($this->pph_21),
            'pph_23' => number_format($this->pph_23),
            'borongan' => number_format($this->borongan),
            'tambah_isi' => number_format($this->tambah_isi),
            'tambah_solar' => number_format($this->tambah_solar),
            'tb_tl' => number_format($this->tb_tl),
            'tally' => number_format($this->tally),
            'uang_makan' => number_format($this->uang_makan),
            'add_cost' => number_format($this->tagihans->sum('jumlah')),
            'total_sopir' => number_format($this->total_sopir),
            'total_invoice' => number_format($this->total_invoice),
            'margin' => number_format($this->margin),
            'tgl_total' =>  $this->tgl_total?date('Y-m-d', strtotime($this->tgl_total)):'-',
            'keterangan' => $keterangan,
            'keterangan_lain' => $this->keterangan_lain,
            'ambil_empty_tambak_langon' => $this->ambil_empty_tambak_langon,
            'ambil_empty_teluk_langon' => $this->ambil_empty_teluk_langon,
            'bongkar_full_teluk_langon' => $this->bongkar_full_teluk_langon,
            'is_vendor' => $this->kendaraan->milik != 'vendor' ? false : true,
            'is_seal' => $this->is_seal == 1 ? 'Y' : '',
            'jurnal_piutang' => $this->jurnal_piutang,
            'jurnal' => implode(',',array_unique($this->jurnals()->pluck('nomor')->toArray())),
        ];
    }
}
