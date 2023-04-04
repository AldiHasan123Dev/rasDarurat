<?php

namespace App\Http\Controllers;

use App\Models\BTTB;
use App\Models\CustomerTrucking;
use App\Models\JadwalKapal;
use App\Models\Kapal;
use App\Models\Order;
use App\Models\SanguSopir;
use App\Models\Satuan;
use App\Models\Shipment;
use App\Models\SubMenu;
use App\Models\Tarif;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    public function import()
    {
        $data = JadwalKapal::where('id','>',6)->get();
        foreach ($data as $item ) {
            $item->kapal->update([
                'nama' => $item->voyage
            ]);
            $item->update([
                'voyage' => $item->kapal_id
            ]);
        }

        $data = Order::get();
        foreach ($data as $order ) {
            $order->update([
                'trucking' => $order->container,
                'container' => $order->seal,
                'nopol' => $order->trucking
            ]);
        }

        return response('successss');
    }

    public function sync()
    {
        $data = Order::all();
        $tarif = Tarif::all();
        foreach ($data as $item ) {
            if (substr($item->job,0,2)==23||substr($item->job,0,2)=='23') {
                $job = substr($item->job,2,8);
                $new = '2023'.$job;
                $asuransi = null;
                if(!is_null($item->asuransi)){
                    if($item->asuransi==1||$item->asuransi=='1'){
                        $asuransi = 'ADA';
                    }
                    if($item->asuransi==0||$item->asuransi=='0'){
                        $asuransi = 'TIDAK';
                    }
                }

                $sat = null;
                if ($item->tarif) {
                    $sat =  $item->tarif->satuan ?? null;
                }
                $item->update([
                    'satuan' => $sat,
                    'job' => $new,
                    'asuransi' => $asuransi
                ]);

                foreach ($tarif as $item ) {
                    $tipe = $item->shipmentInfo->nama[0];
                    if($tipe=='F'||$tipe=='f'){
                        $item->update([
                            'satuan' => 1
                        ]);
                    }else{
                        $item->update([
                            'satuan' => 2
                        ]);
                    }
                }
            }

        }


        return response('success');
    }

    public function invoice()
    {
        $transaksi = Transaksi::pluck('job')->toArray();
        Order::whereNotIn('job',$transaksi)->update([
            'invoice' => null
        ]);

        $order = Order::whereDate('created_at','2023-03-05')->get();
        foreach ($order as $item) {
            $bln = substr($item->job,4,2);
            $item->update([
                'created_at' => '2023-'.$bln.'-01'
            ]);
        }

        return response('Data berhasil di update');
    }

    public function customerTrucking()
    {
        $order = Order::where('trucking','XPDC')->get();
        $i = 0;
        foreach ($order as $item ) {
            $customer = $item->pengirim;
            if ($customer) {
                $nama = $customer->nama;
                $user = CustomerTrucking::where('nama',$nama)->first();
                if (!$user) {
                    CustomerTrucking::create([
                        'nama' => $nama,
                        'alamat' => $customer->alamat,
                        'hp' => $customer->hp,
                        'nik' => $customer->nik,
                        'npwp' => $customer->npwp,
                        'nama_npwp' => $customer->nama_npwp,
                        'alamat_npwp' => $customer->alamat_npwp,
                    ]);
                    $i++;
                }
            }
        }

        return response('Berhasil mengupdate '.$i.' Data');
    }

    public function kuli()
    {
        $data = SanguSopir::all();
        foreach ($data as $item ) {
            $item->update([
                'borongan_kuli_20' => 15000,
                'borongan_kuli_combo' => 15000,
                'borongan_kuli_40' => 25000,
            ]);
        }

        return 'success';
    }

    public function data()
    {
        Tarif::whereIn('shipment',[7,9])->update([
            'shipment' => 1
        ]);
        Tarif::whereIn('shipment',[8])->update([
            'shipment' => 10
        ]);
        Tarif::whereIn('satuan',[82,119,263,264,265,316,317,686,771,816,878,879,881,882,977,978,979,1173,1165,1365])->update([
            'satuan' => 1
        ]);
        Order::whereIn('satuan',[82,119,263,264,265,316,317,686,771,816,878,879,881,882,977,978,979,1173,1165,1365])->update([
            'satuan' => 1
        ]);
        BTTB::whereIn('satuan_id',[82,119,263,264,265,316,317,686,771,816,878,879,881,882,977,978,979,1173,1165,1365])->update([
            'satuan_id' => 1
        ]);
        Satuan::whereIn('id',[82,119,263,264,265,316,317,686,771,816,878,879,881,882,977,978,979,1173,1165,1365])->delete();
        Shipment::whereIn('id',[7,8,9])->delete();
        return response('Berhasil');
    }

    public function agen()
    {
        $orders = Order::whereIn('penerima_bl_id',[1929,1930])->whereNull('agen')->get();
        $i = 0;
        foreach ($orders as $item ) {
            $agen = $item->penerima_bl->nama;
            $item->update([
                'agen' => $agen,
                'penerima_bl_id' => null
            ]);
            $i++;
        }

        return response('berhasil mengupdate '.$i.' data');
    }

    public function pph()
    {
        $i = 0;
        $transaksi = Transaksi::where('pph',0)->get();
        foreach ($transaksi as $item ) {
            $orders = $item->jobs;
            $doc = 0;
            foreach ($orders as $order ) {
                if($order->tarif){
                    if ($order->tarif->kondisi==1||$order->tarif->kondisi==6) {
                        $doc++;
                    }
                }
            }
            if($doc>0){
                $pph = (500000 * $doc) * 0.02;
            }else{
                $pph = $item->sub_total * 0.02;
            }
            $item->update([
                'pph' => $pph
            ]);
            $i++;
        }
        return response('berhasil mengupdate '.$i.' data');
    }

    public function menu_link()
    {
        $menu = SubMenu::all();
        foreach ($menu as $item ) {
            $url = str_replace('https://ptras.id/','http://127.0.0.1:8000/',$item->url);
            $item->update([
                'url' => $url
            ]);
        }

        return response('success');
    }

    public function kapal()
    {
        JadwalKapal::whereIn('kapal_id',[109,128,152,210])->update([
            'kapal_id' => 2
        ]);
        JadwalKapal::whereIn('kapal_id',[201])->update([
            'kapal_id' => 110
        ]);
        JadwalKapal::whereIn('kapal_id',[117,149,195,226])->update([
            'kapal_id' => 6
        ]);
        JadwalKapal::whereIn('kapal_id',[176])->update([
            'kapal_id' => 127
        ]);
        JadwalKapal::whereIn('kapal_id',[186,219])->update([
            'kapal_id' => 137
        ]);
        JadwalKapal::whereIn('kapal_id',[170,209])->update([
            'kapal_id' => 132
        ]);
        JadwalKapal::whereIn('kapal_id',[125,227])->update([
            'kapal_id' => 5
        ]);
        JadwalKapal::whereIn('kapal_id',[204])->update([
            'kapal_id' => 158
        ]);
        JadwalKapal::whereIn('kapal_id',[157, 228])->update([
            'kapal_id' => 4
        ]);
        JadwalKapal::whereIn('kapal_id',[138,156,184,225])->update([
            'kapal_id' => 3
        ]);
        JadwalKapal::whereIn('kapal_id',[230])->update([
            'kapal_id' => 236
        ]);
        JadwalKapal::whereIn('kapal_id',[144,172,205])->update([
            'kapal_id' => 143
        ]);
        JadwalKapal::whereIn('kapal_id',[207])->update([
            'kapal_id' => 183
        ]);
        JadwalKapal::whereIn('kapal_id',[174])->update([
            'kapal_id' => 111
        ]);
        JadwalKapal::whereIn('kapal_id',[199,197])->update([
            'kapal_id' => 124
        ]);
        JadwalKapal::whereIn('kapal_id',[198])->update([
            'kapal_id' => 116
        ]);
        JadwalKapal::whereIn('kapal_id',[213])->update([
            'kapal_id' => 163
        ]);
        JadwalKapal::whereIn('kapal_id',[173])->update([
            'kapal_id' => 133
        ]);
        JadwalKapal::whereIn('kapal_id',[187])->update([
            'kapal_id' => 153
        ]);
        JadwalKapal::whereIn('kapal_id',[160,185])->update([
            'kapal_id' => 129
        ]);
        JadwalKapal::whereIn('kapal_id',[159])->update([
            'kapal_id' => 120
        ]);
        JadwalKapal::whereIn('kapal_id',[167,202,221])->update([
            'kapal_id' => 114
        ]);
        JadwalKapal::whereIn('kapal_id',[192])->update([
            'kapal_id' => 142
        ]);
        JadwalKapal::whereIn('kapal_id',[180,168,206])->update([
            'kapal_id' => 130
        ]);
        JadwalKapal::whereIn('kapal_id',[182])->update([
            'kapal_id' => 139
        ]);
        JadwalKapal::whereIn('kapal_id',[141,175,214])->update([
            'kapal_id' => 126
        ]);
        JadwalKapal::whereIn('kapal_id',[164])->update([
            'kapal_id' => 151
        ]);
        JadwalKapal::whereIn('kapal_id',[212])->update([
            'kapal_id' => 200
        ]);
        JadwalKapal::whereIn('kapal_id',[223])->update([
            'kapal_id' => 190
        ]);
        JadwalKapal::whereIn('kapal_id',[217])->update([
            'kapal_id' => 208
        ]);
        JadwalKapal::whereIn('kapal_id',[148,216])->update([
            'kapal_id' => 122
        ]);
        JadwalKapal::whereIn('kapal_id',[140,169])->update([
            'kapal_id' => 113
        ]);
        JadwalKapal::whereIn('kapal_id',[220])->update([
            'kapal_id' => 147
        ]);
        JadwalKapal::whereIn('kapal_id',[194])->update([
            'kapal_id' => 145
        ]);
        JadwalKapal::whereIn('kapal_id',[154,162])->update([
            'kapal_id' => 121
        ]);
        JadwalKapal::whereIn('kapal_id',[150])->update([
            'kapal_id' => 123
        ]);

        Kapal::whereIn('id',[109,128,152,210,201,117,149,195,226,176,186,219,170,209,125,227,204,157, 228,138,156,184,225,230,144,172,205,207,174,199,198,213,173,187,160,185,159,167,202,221,192,180,168,206,182,141,175,214,164,212,223,217,148,216,140,169,220,194,154,162,150,197])->delete();

        return 'success';
    }
}
