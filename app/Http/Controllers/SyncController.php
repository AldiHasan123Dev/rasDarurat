<?php

namespace App\Http\Controllers;

use App\Models\Agen;
use App\Models\Barang;
use App\Models\BTTB;
use App\Models\COA;
use App\Models\Customer;
use App\Models\CustomerTrucking;
use App\Models\JadwalKapal;
use App\Models\Jurnal;
use App\Models\Kapal;
use App\Models\Order;
use App\Models\OrderTrucking;
use App\Models\SanguSopir;
use App\Models\Satuan;
use App\Models\Shipment;
use App\Models\SubMenu;
use App\Models\Tarif;
use App\Models\TemplateJurnal;
use App\Models\Transaksi;
use App\Models\HutangPelayaran;
use App\Models\TarifPelayaran;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

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

    public function resetTBTL()
    {
        $data = OrderTrucking::join('sopir','sopir.id','=','order_trucking.sopir_id')
                ->join('kendaraan','kendaraan.id','=','order_trucking.kendaraan_id')
                ->select('order_trucking.*','sopir.nama')
                ->where('kendaraan.milik','!=','vendor')
                ->whereNull('order_trucking.tgl_total')
                ->whereNotNull('order_trucking.sj_kembali_fa')
                ->update([
                    'ambil_empty_teluk_langon' => 0,
                    'ambil_empty_tambak_langon' => 0,
                    'bongkar_full_teluk_langon' => 0,
                    'tb_tl' => 0,
                ]);
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

    public function menu_link_alb()
    {
        $menu = SubMenu::all();
        foreach ($menu as $item ) {
            $url = str_replace('http://127.0.0.1:8000/','https://amelia.id/',$item->url);
            $item->update([
                'url' => $url
            ]);
        }

        return response('success');
    }

    public function menu_link_backup()
    {
        $menu = SubMenu::all();
        foreach ($menu as $item ) {
            $url = str_replace('https://ptras.id/','https://ptras.spydercode.my.id/',$item->url);
            $item->update([
                'url' => $url
            ]);
        }

        return response('success');
    }

    public function menu_link_ras()
    {
        $menu = SubMenu::all();
        foreach ($menu as $item ) {
            $url = str_replace('https://ptras.spydercode.my.id/','https://ptras.id/',$item->url);
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

    public function transaksi()
    {
        $data = Transaksi::all();
        foreach ($data as $item ) {
            $created = date('Y-m-d',strtotime($item->created_at));
            $item->update([
                'created_at' => $created
            ]);
        }

        return 'success';
    }

    public function trucking()
    {
        $data = OrderTrucking::whereHas('tarif')->get();
        $i = 0;
        foreach($data as $item){
            $pph_21 = 0;
            $pph_23 = 0;
            $price = $item->tarif->tarif;
            $tujuan = $item->tarif->tujuan->tujuanInfo->nama;
            $tb_tl = 0;
            if($item->customer_id!=2){
                if (($item->kendaraan->milik=='R2'||$item->kendaraan->milik=='vendor'||$item->customer->r2==1)&&$item->customer->pph_23==1) {
                    $pph_23 = $price * 0.02;
                }
            }else{
                if ($item->kendaraan->milik=='R1') {
                    $pph_21 = ($price / 0.97) * 0.03;
                }
            }

            if($item->ambil_empty_tambak_langon==1){
                if($item->tipe=='20'||$item->tipe=='COMBO'){
                    $tb_tl += 50000;
                }
                if($item->tipe=='40'){
                    $tb_tl += 75000;
                }
            }

            if($item->ambil_empty_teluk_langon==1){
                if($item->tipe=='20'||$item->tipe=='COMBO'){
                $tb_tl += 50000;
                }
                if($item->tipe=='40'){
                    $tb_tl += 75000;
                }
            }

            if($item->bongkar_full_teluk_langon==1){
                if($item->tipe=='20'||$item->tipe=='COMBO'){
                    $tb_tl += 50000;
                }
                if($item->tipe=='40'){
                    $tb_tl += 75000;
                }
            }

            $simpanan_kuli = $item->borongan_kuli - $item->kuli;
            $simpanan_sopir = $item->borongan - $item->sangu;
            if($simpanan_kuli < 0){
                $simpanan_kuli = 0;
            }

            $totalan = $simpanan_sopir + $simpanan_kuli + $tb_tl + $item->lain_lain + $item->stappel;
            $margin = $item->tarif->tarif - $item->borongan - $item->borongan_kuli - $item->uang_makan - $item->op - $item->cleaning;

            $item->update([
                'simpanan' => $simpanan_sopir,
                'simpanan_kuli' => $simpanan_kuli,
                'total_sopir' => $totalan,
                'margin' => $margin,
                'pph_21' => $pph_21,
                'pph_23' => $pph_23,
                'tb_tl' => $tb_tl,
            ]);
            $order = Order::where('container',$item->container)->where('seal',$item->seal)->first();
            if($order){
                $item->update(['order_id'=>$order->id]);
            }
            $i++;
        }

        return response('Data berhasil diupdate: '.$i);
    }

    public function sameData()
    {
        $barang = Barang::pluck('nama')->toArray();
        $data_barang = array_values(array_unique($barang));
        $satuan = Satuan::pluck('nama')->toArray();
        $data_satuan = array_values(array_unique($satuan));
        for ($i=0; $i < count($data_barang); $i++) {
            $item = Barang::where('nama',$data_barang[$i])->orderBy('created_at')->first();
            $items = Barang::where('nama',$data_barang[$i])->where('id','!=',$item->id)->pluck('id')->toArray();
            Order::whereIn('barang_id',$items)->update([
                'barang_id' => $item->id
            ]);
            BTTB::whereIn('barang_id',$items)->update([
                'barang_id' => $item->id
            ]);
            Barang::where('nama',$data_barang[$i])->where('id','!=',$item->id)->delete();
        }

        for ($i=0; $i < count($data_satuan); $i++) {
            $item = Satuan::where('nama',$data_satuan[$i])->orderBy('created_at')->first();
            $items = Satuan::where('nama',$data_satuan[$i])->where('id','!=',$item->id)->pluck('id')->toArray();
            Order::whereIn('satuan',$items)->update([
                'satuan' => $item->id
            ]);
            Tarif::whereIn('satuan',$items)->update([
                'satuan' => $item->id
            ]);
            BTTB::whereIn('satuan_id',$items)->update([
                'satuan_id' => $item->id
            ]);
            Satuan::where('nama',$data_satuan[$i])->where('id','!=',$item->id)->delete();
        }

        return response('success');
    }

    public function orderMenu()
    {
        $data = SubMenu::all()->groupBy('menu_id');
        $a = 1;
        foreach($data as $menu){
            $i = 1;
            $menu->first()->menu->update([
                'order' => $a
            ]);
            foreach($menu as $item){
                $item->update([
                    'order' => $i
                ]);
                $i++;
            }
            $a++;
        }

        return 'success';
    }

    public function pull()
    {
        // Print the exec output inside of a pre element
        $res = '';
        $res .= $this->execPrint("cd /var/www/vhosts/ptras.id/aplikasi; git pull");
        $res .= $this->execPrint("cd /var/www/vhosts/ptras.id/aplikasi; git status");
        return $res;
    }

    public function lock()
    {
        $data = Order::whereNotNull('invoice')->update([
            'lock_biaya' => 1,
        ]);

        return response('success');
    }

    function execPrint($command) {
        $html = '<pre>';
        $result = array();
        exec($command, $result);
        foreach ($result as $line) {
            $html .= $line . "\n";
        }
        $html .= '</pre>';
        return $html;
    }

    public function coa()
    {
        $data = COA::all();
        $i = 1;
        Schema::disableForeignKeyConstraints();
        foreach ($data as $item ) {
            $item->update([
                'id' => $i
            ]);
            Jurnal::where('coa_id',$item->id)->update([
                'coa_id' => $i
            ]);
            $i++;
        }

        Schema::enableForeignKeyConstraints();

        return 'success';
    }

    public function hutang_pelayaran()
    {
        $data = HutangPelayaran::get();
        foreach ($data as $item) {
            $opp = Jurnal::where('no_bg',$item->no_bg_opp)->whereIn('tipe',['JNL','TEST'])->where('order_id',$item->order_id)->first()->nomor ?? null;
            $opt = Jurnal::where('no_bg',$item->no_bg_opt)->whereIn('tipe',['JNL','TEST'])->where('order_id',$item->order_id)->first()->nomor ?? null;
            $ut = Jurnal::where('no_bg',$item->no_bg_ut)->whereIn('tipe',['JNL','TEST'])->where('order_id',$item->order_id)->first()->nomor ?? null;
            $item->update([
                'jurnal_opp' => $opp,
                'jurnal_opt' => $opt,
                'jurnal_ut' => $ut,
            ]);
        }
        return response('success');
    }

    public function jurnal()
    {
        // Jurnal::where('tipe','JNL')->whereDate('updated_at','2023-07-16')->forcedelete();
        $id = Transaksi::whereDate('updated_at','2023-07-16')->whereNotNull('tanggal_kirim')->pluck('order_id')->toArray();
        $data = Jurnal::whereIn('order_id',$id)->whereIn('coa_id',[46,56,86])->pluck('order_id')->toArray();
        $ids = array_diff($id,array_unique($data));
        $data = Transaksi::whereIn('order_id',$ids)->orderBy('invoice')->get();
        // dd($id,$data,array_unique(array_merge($id,$data)));
        // $data = Order::whereDate('updated_at','2023-07-15')->whereHas('transaksi', function($q){
        // $q->whereNotNull('tanggal_kirim');
        // })->whereHas('jurnals', function($q){
        //     $q->whereNotIn('coa_id',[46,86,56]);
        // })->with('jurnals')->get(['id','job']);
        // dd($data[1858]);
        foreach ($data as $transaksi ) {
            $template = TemplateJurnal::find(8);
            $no = Jurnal::where('tipe','JNL')->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->max('no') + 1;
            $nomor = sprintf('%02d',date('m')).'-'.sprintf('%03d',$no).'/'.date('y');
            $order = Order::find($transaksi->order_id);
            if($order->id!=2135){
                foreach ($template->template_items as $key => $item) {
                    $name = $item->keterangan;
                    $id_job = $order->job.'-'.sprintf('%02d',$order->no_job);
                    $cont = $order->container;
                    $seal = $order->seal;
                    $shipment = $order->tarif->shipmentInfo->nama;
                    $pembayar = $order->tarif->customer->nama ?? '-';
                    $kapal = $order->jadwal_kapal->kapal->nama ?? '-';
                    $voyage = $order->jadwal_kapal->voyage ?? '-';
                    $customer = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->customer->nama;
                    $shipment_trucking = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->tipe;
                    $tujuan_trucking = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->tarif->tujuan->tujuanInfo->nama;
                    $name = str_replace('[1]',$id_job,$name);
                    $name = str_replace('[2]',$cont,$name);
                    $name = str_replace('[3]',$seal,$name);
                    $name = str_replace('[4]',$kapal,$name);
                    $name = str_replace('[5]',$voyage,$name);
                    $name = str_replace('[6]',$shipment,$name);
                    $name = str_replace('[7]',$pembayar,$name);
                    $name = str_replace('[8]',$customer,$name);
                    $name = str_replace('[9]',$shipment_trucking,$name);
                    $name = str_replace('[10]',$tujuan_trucking,$name);
                    if($item->coa_debit_id){
                        Jurnal::create([
                            'coa_id' => $item->coa_debit_id,
                            'order_id' => $transaksi->order_id,
                            'nomor' => $nomor,
                            'nama' => $name,
                            'debit' => round($transaksi->total),
                            'credit' => 0,
                            'tipe' => 'JNL',
                            'no' => $no,
                            'created_at' => '2023-07-15',
                        ]);
                    }
                    if($item->coa_credit_id==86){
                        Jurnal::create([
                            'coa_id' => $item->coa_credit_id,
                            'order_id' => $transaksi->order_id,
                            'nomor' => $nomor,
                            'nama' => $name,
                            'credit' => round($transaksi->sub_total),
                            'debit' => 0,
                            'tipe' => 'JNL',
                            'no' => $no,
                            'created_at' => '2023-07-15',
                        ]);
                    }
                    if($item->coa_credit_id==56){
                        Jurnal::create([
                            'coa_id' => $item->coa_credit_id,
                            'order_id' => $transaksi->order_id,
                            'nomor' => $nomor,
                            'nama' => $name,
                            'credit' => round($transaksi->ppn),
                            'debit' => 0,
                            'tipe' => 'JNL',
                            'no' => $no,
                            'created_at' => '2023-07-15',
                        ]);
                    }
                    if($item->coa_credit_id==25){
                        foreach ($transaksi->jobs as $job) {
                            if (!is_null($job->asuransi_id)) {
                                $asuransi = ($job->asuransiInfo->rate/100) * $job->pertanggungan;
                                $admin = $job->asuransiInfo->admin;
                                Jurnal::create([
                                    'coa_id' => $item->coa_credit_id,
                                    'order_id' => $job->id,
                                    'nomor' => $nomor,
                                    'nama' => 'Asuransi '.$job->asuransiInfo->nama,
                                    'credit' => round($asuransi + $admin),
                                    'debit' => 0,
                                    'tipe' => 'JNL',
                                    'no' => $no,
                                    'created_at' => '2023-07-15',
                                ]);
                            }
                        }
                    }
                    if($item->coa_credit_id==28){
                        foreach ($transaksi->jobs as $job) {
                            if($job->tagihan->count()>0){
                                foreach ($job->tagihan as $tagihan) {
                                    Jurnal::create([
                                        'coa_id' => $item->coa_credit_id,
                                        'order_id' => $tagihan->order_id,
                                        'nomor' => $nomor,
                                        'nama' => $tagihan->nama,
                                        'credit' => round($tagihan->jumlah),
                                        'debit' => 0,
                                        'tipe' => 'JNL',
                                        'no' => $no,
                                        'created_at' => '2023-07-15',
                                    ]);
                                }
                            }
                        }
                    }
                }
                $transaksi->update([
                    'jurnal_piutang' => $nomor
                ]);
                $transaksi->jobs()->update([
                    'jurnal_piutang' => $nomor
                ]);
            }
        }
        return 'success';
    }

    public function penjurnal()
    {
        $data1 = Jurnal::whereHas('order')->whereNull('invoice')->whereNull('nopol')->whereNull('container')->get();
        $data2 = Jurnal::whereHas('order_trucking')->orWhereNull('nopol')->whereHas('order_trucking')->orWhereNull('container')->whereHas('order_trucking')->get();
        foreach($data1 as $item){
            $item->update([
                'invoice' => $item->order->invoice ?? null,
                'nopol' => $item->order->nopol ?? null,
                'container' => $item->order->container ?? null,
            ]);
        }
        foreach($data2 as $item){
            $item->update([
                'nopol' => $item->order_trucking->kendaraan->nopol ?? null,
                'container' => $item->order_trucking->container ?? null,
            ]);
        }

        return response('Success');
    }
    public function jurnalAsuransi()
    {
        $data = Jurnal::where('coa_id',25)->whereDate('created_at','2023-07-15')->get();
        foreach($data as $item){
            $jurnal = Jurnal::where('nomor',$item->nomor)->where('coa_id','!=',25)->first();
            $item->update([
                'created_at' => $jurnal->created_at
            ]);
        }

        return response('success');
    }

    public function penerimabl(){
        $data = Order::all()->whereNull('penerimabl');
        foreach ($data as $item) {
            if($item->agen=='AGEN'){
                $nama = Agen::find($item->agen_id)->nama ?? null;
            }else{
                $nama = Customer::find($item->penerima_bl_id)->nama ?? null;
            }
            $item->update([
                'penerimabl' => $nama
            ]);
        }

        return response('success');
    }

    public function port()
    {
        TarifPelayaran::whereIn('dari',[1,82,102])->update([
            'port_id' => 1
        ]);
        TarifPelayaran::where('dari',397)->update([
            'port_id' => 2
        ]);

        return response('success');
    }

    public function lokasi_agen()
    {
        $data = Agen::get();
        foreach($data as $item){
            $kota = $item->kota;
            $lokasi = Lokasi::where('nama','like',$kota)->first();
            if(!$lokasi){
                $lokasi = Lokasi::create([
                    'nama' => strtoupper($kota)
                ]);
            }
            $item->update([
                'lokasi_id' => $lokasi->id
            ]);
        }

        return response('success');
    }

    public function coa_name()
    {
        $data = COA::where('nama','LIKE','%opra%')->get();
        foreach ($data as $item) {
            $name = $item->nama;
            $name = str_replace(['opra','Opra'],'Opera',$name);
            $item->update([
                'nama' => $name
            ]);
        }

        return response('success');
    }

    public function jasa_kirim()
    {
        $data = Order::whereHas('jasa_kirim', function($q){
            $q->whereNotNull('tgl_kirim');
        })->get();
        foreach($data as $item){
            $item->update([
                'ba_kirim' => $item->jasa_kirim->tgl_kirim
            ]);
        }

        return response('success');
    }

    public function order_trucking()
    {
        $update = OrderTrucking::with('kendaraan')->whereNull('invoice')->whereNull('order_id')->get();
        $i = 0;
        foreach ($update as $item ) {
            $order = Order::where('container',$item->container)->where('seal',$item->seal)->where('nopol', $item->kendaraan->nopol)->first();
            if($order){
                $item->update(['order_id'=>$order->id]);
                $i++;
            }
        }

        return response('success data updated: '.$i);
    }

    public function jurnal_invoice()
    {
        $data = Jurnal::whereNotNull('order_id')->whereNull('order_trucking_id')->whereNull('invoice')->where('coa_id',46)->get();
        $i = 0;
        foreach($data as $item){
            $item->update([
                'invoice' => $item->order->invoice ?? null
            ]);
            $i++;
        }

        return response('success data updated: '.$i);
    }

    public function tarif_trucking()
    {
        $data = OrderTrucking::where('tarif_nominal',0)->get();
        $i = 0;
        foreach ($data as $item) {
            $item->update([
                'tarif_nominal' => $item->tarif->tarif
            ]);
            $i++;
        }

        return response('success data updated: '.$i);
    }
}
