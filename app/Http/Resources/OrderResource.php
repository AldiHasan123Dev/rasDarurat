<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $class = '';
        $barang = '';
        if($this->bttb->count()>0){
            $class = 'bg-light-success';
        }
        if($this->jadwal_kapal->is_active != 1){
            $class = 'bg-light-danger';
        }
        if(!is_null($this->invoice)){
            $class = 'bg-light-warning';
        }
        foreach ($this->bttb as $brg ) {
            $barang .= $brg->barang->nama.'; ';
        }
        return [
            'id' => $this->id,
            'invoice' => $this->invoice ?? '-',
            'job' => $this->job ?? '-',
            'no' => $this->job.'-'.sprintf('%02d',$this->no_job) ?? '-',
            'asuransi' => $this->asuransi,
            'pembayar' => $this->tarif->customer->nama ?? '-',
            'marketing' => $this->tarif->customer->marketing->name ?? '-',
            'cs' => $this->tarif->customer->cs->name ?? '-',
            'pengirim' => $this->pengirim->nama ?? '-',
            'penerima' => $this->penerima->nama ?? '-',
            'dari' => $this->tarif->dari_lokasi->nama ?? '-',
            'tujuan' => $this->tarif->tujuan_lokasi->nama ?? '-',
            'shipment' => $this->tarif->shipmentInfo->nama ?? '-',
            'kondisi' => $this->tarif->kondisiInfo->nama ?? '-',
            'barang' => $this->barang->nama ?? '-',
            'pelayaran' => $this->jadwal_kapal->pelayaran->nama ?? '-',
            'kapal' => $this->jadwal_kapal->kapal->nama ?? '-',
            'voyage' => $this->jadwal_kapal->voyage ?? '-',
            'tgl_muat' => is_null($this->truckingInfo)?'-':date('d-m-Y',strtotime($this->truckingInfo->tgl_muat)),
            'etd' => is_null($this->jadwal_kapal->etd)?'-':date('d-m-Y',strtotime($this->jadwal_kapal->etd)),
            'td' => is_null($this->jadwal_kapal->td)?'-':date('d-m-Y',strtotime($this->jadwal_kapal->td)),
            'closing' => is_null($this->jadwal_kapal->closing)?'-':date('d-m-Y',strtotime($this->jadwal_kapal->closing)),
            'ba_kirim' => is_null($this->ba_kirim)?'-':date('d-m-Y',strtotime($this->ba_kirim)),
            'nopol' => $this->nopol,
            'trucking' => $this->trucking,
            'container' => $this->container,
            'seal' => $this->seal,
            'stuffing' => is_null($this->stuffing)?'-':date('d-m-Y',strtotime($this->stuffing)),
            'stuffing_type' => $this->tarif->stuffing ?? '-',
            'full' => is_null($this->full)?'-':date('d-m-Y',strtotime($this->full)),
            'barang_diantar' => is_null($this->barang_diantar)?'-':date('d-m-Y',strtotime($this->barang_diantar)),
            'ba_kembali' => is_null($this->ba_kembali)?'-':date('d-m-Y',strtotime($this->ba_kembali)),
            'satuan' => $this->satuanInfo->nama ?? '-',
            'unit' => $this->tarif->satuanInfo->nama ?? '-',
            'tarif' => is_null($this->tarif) ? '-' :  number_format($this->tarif->tarif),
            'agen' => $this->agen,
            'penerima_bl' => $this->agen=='AGEN'?($this->agent->nama??'-'):($this->penerima_bl->nama??'-'),
            'keterangan' => $this->keterangan,
            'class' => $class,
            'tanggal' => date('d/m/y', strtotime($this->created_at)),
            'created_at' => date('d/m/y', strtotime($this->created_at)),
            'barang_detail' => $barang,
            'koli' => $this->bttb->sum('qty'),
            'm3' => $this->bttb->sum('vol'),
            'berat' => $this->bttb->sum('berat'),
        ];
    }
}
