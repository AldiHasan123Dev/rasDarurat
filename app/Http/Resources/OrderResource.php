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
        if($this->jadwal_kapal){
            if($this->jadwal_kapal->is_active != 1){
                $class = 'bg-light-danger';
            }
        }
        if(!is_null($this->invoice)){
            $class = 'bg-light-warning';
        }
        foreach ($this->bttb as $brg ) {
            $barang .= $brg->barang->nama.'; ';
        }
        return [
            'id' => $this->id,
            'tarif_id' => $this->tarif_id,
            'invoice' => $this->invoice ?? '-',
            'job' => $this->job ?? '-',
            'cek_kuli' => $this->cek_kuli ?? '-',
            'cek_ops' => $this->cek_ops ?? '-',
            'cek_checker' => $this->cek_checker ?? '-',
            'no' => $this->job.'-'.sprintf('%02d',$this->no_job) ?? '-',
            'asuransi' => $this->asuransi,
            'customer_trucking' => is_null($this->truckingInfo) ? '-' : $this->truckingInfo->customer->nama,
            'shipment_trucking' => is_null($this->truckingInfo) ? '-' : $this->truckingInfo->tipe,
            'tujuan_trucking' => is_null($this->truckingInfo) ? '-' : $this->truckingInfo->tujuan,
            'ppn' => is_null($this->transaksi) ? '-' : $this->transaksi->ppn,
            'subtotal' => is_null($this->transaksi) ? '-' : $this->transaksi->total,
            'pembayar' => $this->tarif->customer->nama ?? '-',
            'marketing' => $this->tarif->customer->marketing->name ?? '-',
            'marketing_id' => $this->tarif->customer->marketing->id ?? '-',
          'syarat_ba' => optional(optional($this->tarif)->customer)->ba_kembali == 1
    ? 'Iya'
    : (optional(optional($this->tarif)->customer)->ba_kembali == 0 ? 'Tidak' : '-'),
            'cs' => $this->tarif->customer->cs->name ?? '-',
            'cs_id' => $this->tarif->customer->cs->id ?? '-',
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
            'eta' => $this->jadwal_kapal->eta ?? '-',
            'tgl_muat' => is_null($this->truckingInfo)?'-':date('d-m-Y',strtotime($this->truckingInfo->tgl_muat)),
            'etd' => is_null($this->jadwal_kapal)?'-':(!$this->jadwal_kapal->etd?'-':date('d-m-Y',strtotime($this->jadwal_kapal->etd))),
            'td' => is_null($this->jadwal_kapal)?'-':(!$this->jadwal_kapal->td?'-':date('d-m-Y',strtotime($this->jadwal_kapal->td))),
            'closing' => is_null($this->jadwal_kapal)?'-':(!$this->jadwal_kapal->closing?'-':date('d-m-Y',strtotime($this->jadwal_kapal->closing))),
            'ba_kirim' => is_null($this->ba_kirim)?'-':date('d-m-Y',strtotime($this->ba_kirim)),
            'invoice_bayar' => is_null($this->invoice_bayar)?'-':date('d-m-Y',strtotime($this->invoice_bayar)),
            'tgl_komisi' => is_null($this->tgl_komisi)?'-':date('d-m-Y',strtotime($this->tgl_komisi)),
            'komisi_print' => is_null($this->komisi_print)?'-':date('d-m-Y',strtotime($this->komisi_print)),
            'ba_kirim_date' => is_null($this->ba_kirim)?'-':date('Y-m-d',strtotime($this->ba_kirim)),
            'barang_diantar_date' => is_null($this->barang_diantar)?'-':date('Y-m-d',strtotime($this->barang_diantar)),
            'nopol' => $this->nopol,
            'trucking' => $this->trucking,
            'container' => $this->container,
            'seal' => $this->seal,
            'lock_biaya' => $this->lock_biaya,
            'stuffing' => is_null($this->stuffing)?'-':date('d-m-Y',strtotime($this->stuffing)),
            'stuffing_type' => $this->tarif->stuffing ?? '-',
            'full' => is_null($this->full)?'-':date('d-m-Y',strtotime($this->full)),
            'tgl_potong' => is_null($this->tgl_potong)?'-':date('d-m-Y',strtotime($this->tgl_potong)),
            'barang_diantar' => is_null($this->barang_diantar)?'-':date('d-m-Y',strtotime($this->barang_diantar)),
            'ba_kembali' => is_null($this->ba_kembali)?'-':date('d-m-Y',strtotime($this->ba_kembali)),
            'ba_diantar_sby' => is_null($this->ba_diantar_sby)?'-':date('d-m-Y',strtotime($this->ba_diantar_sby)),
            'satuan' => $this->satuanInfo->nama ?? '-',
            'unit' => $this->tarif->satuanInfo->nama ?? '-',
            'tarif' => is_null($this->tarif) ? '-' :  number_format($this->tarif->tarif),
            'tarif1' => is_null($this->tarif) ? '-' :  $this->tarif->tarif,
            'komisi' => is_null($this->komisi) ? '-' :  number_format($this->komisi),
            'agen' => $this->agen,
            'penerima_bl' => $this->agen=='AGEN'?($this->agent->nama??'-'):($this->penerima_bl->nama??'-'),
            'keterangan' => $this->keterangan,
            'class' => $class,
            'tanggal' => date('d/m/y', strtotime($this->created_at)),
            'created_at' => date('d/m/y', strtotime($this->created_at)),
            'barang_detail' => $barang,
            'koli' => $this->bttb->sum('qty') ?? 0,
            'm3' => $this->bttb->sum('vol'),
            'berat' => $this->bttb->sum('berat'),
            'add_cost' => number_format($this->tagihan->sum('jumlah')),
            'jurnal_piutang' => $this->jurnal_piutang,
            'jurnal' => implode(',',array_unique($this->jurnals()->pluck('nomor')->toArray())),
        ];
    }
}
