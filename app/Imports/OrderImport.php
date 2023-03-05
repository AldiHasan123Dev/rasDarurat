<?php

namespace App\Imports;

use App\Models\Barang;
use App\Models\Customer;
use App\Models\JadwalKapal;
use App\Models\Kapal;
use App\Models\Kondisi;
use App\Models\Lokasi;
use App\Models\Order;
use App\Models\Pelayaran;
use App\Models\Satuan;
use App\Models\Shipment;
use App\Models\Tarif;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class OrderImport implements ToModel, WithStartRow
{
    public function model(array $row)
    {
        if (!is_null($row[17])) {
            $pelayaran = Pelayaran::where('nama', $row[17])->first();
            if (!$pelayaran) {
                if(!is_null($row[17])){
                    $pelayaran = Pelayaran::create([
                        'kode' => 'PL'.sprintf("%02d",Pelayaran::select('id')->count()),
                        'nama' => $row[17]
                    ]);
                }
            }

            $kapal = Kapal::where('nama',$row[18])->first();
            if (!$kapal) {
                $kapal = Kapal::create([
                    'nama' => $row[18]
                ]);
            }
            
            $etd = null;
            $td = null;
            $ba_kirim = null;
            $stufing = null;
            $full = null;
            $barang_diantar = null;
            $ba_kembali = null;
            $asuransi = null;
            $agen = null;

            if($row[21]!=''||$row[21]!=0||!empty($row[21])||$row[21]){
                $etd = substr($row[21],6,4).'-'.substr($row[21],0,2).'-'.substr($row[21],3,2);
                // $etd = date('Y-m-d',strtotime( str_replace(["'","`"],'',$row[21]) ));
            }
            if($row[22]!=''||$row[22]!=0||!empty($row[22])||$row[22]){
                $td = substr($row[22],6,4).'-'.substr($row[22],0,2).'-'.substr($row[22],3,2);
                // $td = date('Y-m-d',strtotime( str_replace(["'","`"],'',$row[22]) ));
            }
            if($row[23]!=''||$row[23]!=0||!empty($row[23])||$row[23]){
                $ba_kirim = substr($row[23],6,4).'-'.substr($row[23],3,2).'-'.substr($row[23],0,2);
                // $ba_kirim = date('Y-m-d',strtotime( str_replace(["'","`"],'',$row[23]) ));
            }
            if($row[28]!=''||$row[28]!=0||!empty($row[28])||$row[28]){
                $stufing = substr($row[28],6,4).'-'.substr($row[28],0,2).'-'.substr($row[28],3,2);
                // $stufing = date('Y-m-d',strtotime( str_replace(["'","`"],'',$row[28]) ));
            }
            if($row[30]!=''||$row[30]!=0||!empty($row[30])||$row[30]){
                $full = substr($row[30],6,4).'-'.substr($row[30],0,2).'-'.substr($row[30],3,2);
                // $full = date('Y-m-d',strtotime( str_replace(["'","`"],'',$row[30]) ));
            }
            if($row[31]!=''||$row[31]!=0||!empty($row[31])||$row[31]){
                $barang_diantar = substr($row[31],6,4).'-'.substr($row[31],3,2).'-'.substr($row[31],0,2);
                // $barang_diantar = date('Y-m-d',strtotime( str_replace(["'","`"],'',$row[31]) ));
            }
            if($row[32]!=''||$row[32]!=0||!empty($row[32])||$row[32]){
                $ba_kembali = substr($row[32],6,4).'-'.substr($row[32],3,2).'-'.substr($row[32],0,2);
                // $ba_kembali = date('Y-m-d',strtotime( str_replace(["'","`"],'',$row[32]) ));
            }
            if($row[5]!=''||$row[5]!=0||!empty($row[5])||$row[5]){
                if ($row[5]=='TIDAK') {
                    $asuransi = 0;
                }else{
                    $asuransi = 1;
                }
            }
            if($row[42]!=''||$row[42]!=0||!empty($row[42])||$row[42]){
                if ($row[42]=='AGEN') {
                    $agen = 1;
                }else{
                    $agen = 0;
                }
            }

            $jadwal_kapal = JadwalKapal::where('kapal_id',$kapal->id)->where('pelayaran_id',$pelayaran->id ?? null)->where('voyage',$row[19])->whereDate('etd',$etd)->whereDate('td',$td)->first();
            if(!$jadwal_kapal){
                $jadwal_kapal = JadwalKapal::create([
                    'kapal_id' => $kapal->id,
                    'pelayaran_id' => $pelayaran->id ?? '',
                    'voyage' => $row[19],
                    'etd' => $etd,
                    'td' => $td,
                    'is_active' => is_null($td)?1:0
                ]);
            }

            $pembayar = Customer::where('nama',$row[9])->first();
            if (!$pembayar) {
                $pembayar = Customer::create([
                    'nama' => $row[9]
                ]);
            }

            $pengirim = Customer::where('nama',$row[10])->first();
            if (!$pengirim) {
                if (!is_null($row[10])) {
                    $pengirim = Customer::create([
                        'nama' => $row[10]
                    ]);
                }
            }

            $penerima = Customer::where('nama',$row[11])->first();
            if (!$penerima) {
                $penerima = Customer::create([
                    'nama' => $row[11]
                ]);
            }

            $penerima_bl = Customer::where('nama',$row[43])->first();
            if (!$penerima_bl) {
                $penerima = Customer::create([
                    'nama' => $row[43]
                ]);
            }
            
            $dari = Lokasi::where('nama',$row[12])->first();
            if (!$dari) {
                $dari = Lokasi::create([
                    'nama' => $row[12]
                ]);
            }

            $tujuan = Lokasi::where('nama',$row[13])->first();
            if (!$tujuan) {
                $tujuan = Lokasi::create([
                    'nama' => $row[13]
                ]);
            }
            
            $shipment = Shipment::where('nama',$row[14])->first();
            if (!$shipment) {
                $shipment = Shipment::create([
                    'nama' => $row[14]
                ]);
            }

            $kondisi = Kondisi::where('nama',$row[15])->first();
            if (!$kondisi) {
                $kondisi = Kondisi::create([
                    'nama' => $row[15]
                ]);
            }

            $satuan = Satuan::where('nama',$row[34])->first();
            if (!$satuan) {
                $satuan = Satuan::create([
                    'nama' => $row[34]
                ]);
            }

            $barang = Barang::where('nama',$row[16])->first();
            if (!$barang) {
                if (!is_null($row[16])) {
                    $barang = Barang::create([
                        'nama' => $row[16]
                    ]);
                }
            }

            $price = (int)str_replace([',','.'],'',$row[36]);
            $tarif = Tarif::where('customer_id',$pembayar->id)->where('jadwal_kapal_id',$jadwal_kapal->id)->where('dari', $dari->id)->where('tujuan',$tujuan->id)->where('shipment',$shipment->id)->where('kondisi',$kondisi->id)->where('satuan',$satuan->id)->where('tarif',$price)->first();
            if(!$tarif){
                $tarif = Tarif::create([
                    'customer_id' => $pembayar->id,
                    'jadwal_kapal_id' => $jadwal_kapal->id,
                    'dari' => $dari->id,
                    'tujuan' => $tujuan->id,
                    'shipment' => $shipment->id,
                    'kondisi' => $kondisi->id,
                    'satuan' => $satuan->id,
                    'tarif' => $price,
                    'is_active' => $jadwal_kapal->is_active
                ]);
            }

            // $data = [
            //     'created_at' => date('Y-m-d', strtotime($row[0])),
            //     'invoice' => $row[1],
            //     'job' => $row[2],
            //     'no_job' => $row[4],
            //     'asuransi' => $row[5],
            //     'marketing_id' => $row[7],
            //     'cs_id' => $row[8],
            //     'pembayar_id' => $row[9],
            //     'pengirim_id' => $row[10],
            //     'penerima_id' => $row[11],
            //     'dari' => $row[12],
            //     'tujuan' => $row[13],
            //     'shipment' => $row[14],
            //     'kondisi' => $row[15],
            //     'jenis_barang' => $row[16],
            //     'pelayaran' => $row[17],
            //     'kapal' => $row[18],
            //     'voyage' => $row[19],
            //     'etd' => $row[21],
            //     'td' => $row[21],
            //     'ba_kirim' => $row[22],
            //     'nopol' => $row[23],
            //     'trucking' => $row[24],
            //     'container' => $row[25],
            //     'seal' => $row[26],
            //     'stuffing' => $row[27],
            //     'stuffing_type' => $row[28],
            //     'full' => $row[29],
            //     'barang_diantar' => $row[30],
            //     'ba_kembali' => $row[31],
            //     'koli' => $row[32],
            //     'satuan' => $row[33],
            //     'm3' => $row[34],
            //     'tarif' => $row[35],
            //     'unit' => $row[36],
            //     'sub_total' => $row[37],
            //     'keterangan' => $row[38],
            //     'status' => $row[39],
            //     'no_resi' => $row[40],
            //     'agen' => $row[42],
            //     'penerima_bl_id' => $row[43],
            // ];

            $order = Order::create([
                'no' => (int)substr($row[2],-3),
                'invoice' => $row[1],
                'job' => $row[2],
                'no_job' => (int)$row[4],
                'tarif_id' => $tarif->id,
                'pengirim_id' => $pengirim->id ?? null,
                'penerima_id' => $penerima->id ?? null,
                'penerima_bl_id' => $penerima_bl->id ?? null,
                'barang_id' => $barang->id ?? null,
                'ba_kirim' => $ba_kirim,
                'stufing' => $stufing,
                'stufing_type' => $row[28]??null,
                'full' => $full,
                'barang_diantar' => $barang_diantar,
                'ba_kembali' => $ba_kembali,
                'resi' => $row[40]??null,
                'nopol' => $row[23]??null,
                'container' => $row[25],
                'seal' => $row[26],
                'asuransi' => $asuransi,
                'agen' => $agen,
                'trucking' => $row[24] ?? null
            ]);
        }
    }

    public function startRow(): int
    {
        return 3;
    }
}
