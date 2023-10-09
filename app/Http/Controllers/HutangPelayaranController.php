<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Pelayaran;
use Illuminate\Http\Request;
use App\Models\TarifPelayaran;
use App\Models\HutangPelayaran;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Models\JadwalKapal;
use App\Models\Jurnal;
use Illuminate\Support\Facades\Hash;

class HutangPelayaranController extends Controller
{
    public function index()
    {
        $lists = HutangPelayaran::where('status',0)->pluck('order_id')->toArray();
        $kapal = [];
        $pelayaran = [];
        if(request('kapal')){
            $kapal = request('kapal');
        }
        if(request('pelayaran')){
            $pelayaran = request('pelayaran');
        }
        $q = Order::query();
            $q->join('jadwal_kapal','jadwal_kapal.id','=','order.jadwal_kapal_id');
            $q->join('pelayaran','pelayaran.id','=','jadwal_kapal.pelayaran_id');
            $q->join('kapal','kapal.id','=','jadwal_kapal.kapal_id');
            $q->join('tarif','tarif.id','=','order.tarif_id');
            $q->join('shipments','tarif.shipment','=','shipments.id');
            $q->join('lokasi as dari','dari.id','=','tarif.dari');
            $q->join('lokasi as tujuan','tujuan.id','=','tarif.tujuan');
            $q->join('hutang_pelayaran','hutang_pelayaran.order_id','=','order.id');
            $q->whereIn('order.id',$lists);
            $q->where('hutang_pelayaran.status',0);
            if(request('pelayaran')){
                $q->whereIn('jadwal_kapal.pelayaran_id',$pelayaran);
            }
            if(request('kapal')){
                $q->whereIn('kapal.nama',$kapal);
            }
            $q->select('order.job','order.tipe','hutang_pelayaran.is_lock','hutang_pelayaran.ut','dari.nama as dari','tujuan.nama as tujuan','order.tarif_id','order.container','order.seal','order.no_job','order.id','order.jadwal_kapal_id','jadwal_kapal.pelayaran_id','jadwal_kapal.kapal_id','jadwal_kapal.voyage','kapal.nama as nama_kapal','pelayaran.nama','shipments.nama as fit');
            $q->orderBy('order.job')->orderBy('order.no_job');
            $data = $q->get()->groupBy('jadwal_kapal.pelayaran_id','jadwal_kapal.kapal_id');
        return view('admin.hutangpelayaran.index', compact('data','pelayaran','kapal'));
    }

    public function cetak()
    {
        $data = HutangPelayaran::where('status',1)->whereNotNull('invoice')->get()->groupBy('invoice');
        return view('admin.hutangpelayaran.cetak', compact('data'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $ids = array();
        $n = HutangPelayaran::max('no') + 1;
        $code = 'HP/'.date('ymd').'/'.sprintf('%02d',$n);
        foreach ($data['data'] as $id => $item) {
            $prop = $item;
            $prop['no'] = $n;
            $prop['invoice'] = $code;
            $prop['tgl_invoice'] = date('Y-m-d');
            $prop['tgl_bg_opp'] = $data['tanggal_bg_opp'];
            $prop['tgl_bg_opt'] = $data['tanggal_bg_opt'];
            $prop['tgl_bg_ut'] = $data['tanggal_bg_ut'];
            $prop['no_bg_opp'] = $data['no_bg_opp'];
            $prop['no_bg_opt'] = $data['no_bg_opt'];
            $prop['no_bg_ut'] = $data['no_bg_ut'];
            $prop['nominal_bg_opp'] = $data['nominal_bg_opp'] ?? 0;
            $prop['nominal_bg_opt'] = $data['nominal_bg_opt'] ?? 0;
            $prop['nominal_bg_ut'] = $data['nominal_bg_ut'] ?? 0;
            $prop['pph'] = $data['pph'];
            $prop['pembulatan'] = $data['pembulatan'];
            $prop['penambahan'] = $data['penambahan'];
            $prop['penambahan_nominal'] = $data['penambahan_nominal'];
            $prop['status'] = 1;
            $hp =  HutangPelayaran::where('order_id',$id)->first();
            $hp->update($prop);
            array_push($ids,$hp->id);
        }

        $tgl = [$data['no_bg_opp'],$data['no_bg_opt'],$data['no_bg_ut']];
        $tgl_group = array_filter(array_unique($tgl));
        $data_nomor = array();
        $no = Jurnal::where('tipe','JNL')->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->max('no') + 1;
        foreach($tgl_group as $tg){
            $nomor = sprintf('%02d',date('m')).'-'.sprintf('%03d',$no).'/'.date('y');
            $data_nomor[$tg] = ['no'=>$no,'nomor'=>$nomor];
            $no++;
        }

        $opp_total = 0;
        $opt_total = 0;
        $ut_total = 0;
        $hp = HutangPelayaran::whereIn('id',$ids)->first();
        $lists = HutangPelayaran::with(['order','order.tarif','order.tarif.shipmentInfo','order.tarif.customer'])->whereIn('id',$ids)->get()->toArray();
        foreach ($lists as $item) {
            $opp = ['opp','apbs','cleaning','thc','lss','opp_stamp'];
            $opt = ['opt','opt_stamp'];
            $ut = ['ut','ut_stamp','bl','ut_cleaning'];
            foreach($opp as $a){
                if($a=='thc'){
                    $title = 'THC LOLO';
                }else if($a=='opp_stamp'){
                    $title = 'STAMP OPP';
                }else{
                    $title = strtoupper($a);
                }
                $name = $title.' '.$hp->order->jadwal_kapal->kapal->nama.' V. '.$hp->order->jadwal_kapal->voyage.' (1X'.preg_replace("/[^0-9]/", "", $item['order']['tarif']['shipment_info']['nama'] ).' )  '.$item['order']['tarif']['customer']['nama'].' ( '.$item['order']['job'].'-'.sprintf('%02d',$item['order']['no_job']).')';
                if($item[$a]>0 && !is_null($item['no_bg_opp'])){
                    Jurnal::create([
                        'tipe' => 'JNL',
                        'no_bg' => $item['no_bg_opp'],
                        'tgl_bg' => $item['tgl_bg_opp'],
                        'nominal_bg' => $item['nominal_bg_opp'],
                        'coa_id' => 31,
                        'order_id' => $item['order_id'],
                        'nomor' => $data_nomor[$item['no_bg_opp']]['nomor'],
                        'no' => $data_nomor[$item['no_bg_opp']]['no'],
                        'nama' => $name,
                        'debit' => $item[$a],
                        'credit' => 0,
                    ]);

                    $opp_total += $item[$a];
                }
            }
            foreach($opt as $a){
                if($a=='opt_stamp'){
                    $title = 'STAMP OPT';
                }else{
                    $title = strtoupper($a);
                }
                $name = $title.' '.$hp->order->jadwal_kapal->kapal->nama.' V. '.$hp->order->jadwal_kapal->voyage.' (1X'.preg_replace("/[^0-9]/", "", $item['order']['tarif']['shipment_info']['nama'] ).' )  '.$item['order']['tarif']['customer']['nama'].' ( '.$item['order']['job'].'-'.sprintf('%02d',$item['order']['no_job']).')';
                if($item[$a]>0 && !is_null($item['no_bg_opt'])){
                    Jurnal::create([
                        'tipe' => 'JNL',
                        'no_bg' => $item['no_bg_opt'],
                        'tgl_bg' => $item['tgl_bg_opt'],
                        'nominal_bg' => $item['nominal_bg_opt'],
                        'coa_id' => 31,
                        'order_id' => $item['order_id'],
                        'nomor' => $data_nomor[$item['no_bg_opt']]['nomor'],
                        'no' => $data_nomor[$item['no_bg_opt']]['no'],
                        'nama' => $name,
                        'debit' => $item[$a],
                        'credit' => 0,
                    ]);

                    $opt_total += $item[$a];
                }
            }
            foreach($ut as $a){
                if($a=='ut_stamp'){
                    $title = 'STAMP UT';
                }elseif($a=='ut_cleaning'){
                    $title = 'CLEANING';
                }else{
                    $title = strtoupper($a);
                }
                $name = $title.' '.$hp->order->jadwal_kapal->kapal->nama.' V. '.$hp->order->jadwal_kapal->voyage.' (1X'.preg_replace("/[^0-9]/", "", $item['order']['tarif']['shipment_info']['nama'] ).' )  '.$item['order']['tarif']['customer']['nama'].' ( '.$item['order']['job'].'-'.sprintf('%02d',$item['order']['no_job']).')';
                if($item[$a]>0 && !is_null($item['no_bg_ut'])){
                    Jurnal::create([
                        'tipe' => 'JNL',
                        'no_bg' => $item['no_bg_ut'],
                        'tgl_bg' => $item['tgl_bg_ut'],
                        'nominal_bg' => $item['nominal_bg_ut'],
                        'coa_id' => 31,
                        'order_id' => $item['order_id'],
                        'nomor' => $data_nomor[$item['no_bg_ut']]['nomor'],
                        'no' => $data_nomor[$item['no_bg_ut']]['no'],
                        'nama' => $name,
                        'debit' => $item[$a],
                        'credit' => 0,
                    ]);

                    $ut_total += $item[$a];
                }
            }
        }

        if($hp->pph>0){
            Jurnal::create([
                'tipe' => 'JNL',
                'no_bg' => $hp->no_bg_opp,
                'tgl_bg' => $hp->tgl_bg_opp,
                'nominal_bg' => $hp->nominal_bg_opp,
                'coa_id' => 73,
                'nomor' => $data_nomor[$hp->no_bg_opp]['nomor'],
                'no' => $data_nomor[$hp->no_bg_opp]['no'],
                'nama' => 'Potongan PPH 23 '.$hp->order->jadwal_kapal->kapal->nama.' V. '.$hp->order->jadwal_kapal->voyage,
                'debit' => 0,
                'credit' => $hp->pph,
            ]);
        }
        if($hp->pembulatan>0){
            $opp_total += $hp->pembulatan;
            Jurnal::create([
                'tipe' => 'JNL',
                'no_bg' => $hp->no_bg_opp,
                'tgl_bg' => $hp->tgl_bg_opp,
                'nominal_bg' => $hp->nominal_bg_opp,
                'coa_id' => 130,
                'nomor' => $data_nomor[$hp->no_bg_opp]['nomor'],
                'no' => $data_nomor[$hp->no_bg_opp]['no'],
                'nama' => 'Pembulatan OPP '.$hp->order->jadwal_kapal->kapal->nama.' V. '.$hp->order->jadwal_kapal->voyage,
                'debit' => $hp->pembulatan,
                'credit' => 0,
            ]);
        }
        Jurnal::create([
            'tipe' => 'JNL',
            'no_bg' => $hp->no_bg_opp,
            'tgl_bg' => $hp->tgl_bg_opp,
            'nominal_bg' => $hp->nominal_bg_opp,
            'coa_id' => 62,
            'nomor' => $data_nomor[$hp->no_bg_opp]['nomor'],
            'no' => $data_nomor[$hp->no_bg_opp]['no'],
            'nama' => 'Hutang '.$hp->pelayaran->nama.' : '.$hp->order->jadwal_kapal->kapal->nama.' V. '.$hp->order->jadwal_kapal->voyage.' BG: '.$hp->no_bg_opp.' ('.date('d/m/y',strtotime($hp->tgl_bg_opp)).')',
            'debit' => 0,
            'credit' => $opp_total - $hp->pph,
        ]);
        if (!is_null($hp->no_bg_opt)) {
            Jurnal::create([
                'tipe' => 'JNL',
                'no_bg' => $hp->no_bg_opt,
                'tgl_bg' => $hp->tgl_bg_opt,
                'nominal_bg' => $hp->nominal_bg_opt,
                'coa_id' => 62,
                'nomor' => $data_nomor[$hp->no_bg_opt]['nomor'],
                'no' => $data_nomor[$hp->no_bg_opt]['no'],
                'nama' => 'Hutang '.$hp->pelayaran->nama.' : '.$hp->order->jadwal_kapal->kapal->nama.' V. '.$hp->order->jadwal_kapal->voyage.' BG: '.$hp->no_bg_opt.' ('.date('d/m/y',strtotime($hp->tgl_bg_opt)).')',
                'debit' => 0,
                'credit' => $opt_total,
            ]);
        }
        if (!is_null($hp->no_bg_ut)) {
            Jurnal::create([
                'tipe' => 'JNL',
                'no_bg' => $hp->no_bg_ut,
                'tgl_bg' => $hp->tgl_bg_ut,
                'nominal_bg' => $hp->nominal_bg_ut,
                'coa_id' => 62,
                'nomor' => $data_nomor[$hp->no_bg_ut]['nomor'],
                'no' => $data_nomor[$hp->no_bg_ut]['no'],
                'nama' => 'Hutang '.$hp->pelayaran->nama.' : '.$hp->order->jadwal_kapal->kapal->nama.' V. '.$hp->order->jadwal_kapal->voyage.' BG: '.$hp->no_bg_ut.' ('.date('d/m/y',strtotime($hp->tgl_bg_ut)).')',
                'debit' => 0,
                'credit' => $ut_total + $hp->penambahan_nominal,
            ]);

            if(!is_null($hp->penambahan)){
                if($hp->penambahan_nominal!=0){
                    if($hp->penambahan_nominal>0){
                        Jurnal::create([
                            'tipe' => 'JNL',
                            'no_bg' => $hp->no_bg_ut,
                            'tgl_bg' => $hp->tgl_bg_ut,
                            'nominal_bg' => $hp->nominal_bg_ut,
                            'coa_id' => 23,
                            'nomor' => $data_nomor[$hp->no_bg_ut]['nomor'],
                            'no' => $data_nomor[$hp->no_bg_ut]['no'],
                            'nama' => $hp->penambahan,
                            'debit' => $hp->penambahan_nominal,
                            'credit' => 0,
                        ]);
                    }else{
                        Jurnal::create([
                            'tipe' => 'JNL',
                            'no_bg' => $hp->no_bg_ut,
                            'tgl_bg' => $hp->tgl_bg_ut,
                            'nominal_bg' => $hp->nominal_bg_ut,
                            'coa_id' => 23,
                            'nomor' => $data_nomor[$hp->no_bg_ut]['nomor'],
                            'no' => $data_nomor[$hp->no_bg_ut]['no'],
                            'nama' => $hp->penambahan,
                            'debit' => 0,
                            'credit' => $hp->penambahan_nominal * -1,
                        ]);
                    }
                }
            }
        }


        return redirect()->route('hutang-pelayaran.print',['invoice'=>$code]);
    }

    public function delete(Request $request)
    {
        $order_id = explode(',', $request->order_id);
        HutangPelayaran::whereIn('order_id',$order_id)->delete();
        return back()->with('success','Data berhasil dihapus');
    }

    public function tarik(Request $request)
    {
        $data = HutangPelayaran::where('invoice',$request->invoice)->get();
        $hp = $data->first();
        if($hp->no_bg_opp){
            Jurnal::where('no_bg',$hp->no_bg_opp)->delete();
        }
        if($hp->no_bg_opt){
            Jurnal::where('no_bg',$hp->no_bg_opt)->delete();
        }
        if($hp->no_bg_ut){
            Jurnal::where('no_bg',$hp->no_bg_ut)->delete();
        }
        HutangPelayaran::where('invoice',$request->invoice)->update([
            'invoice' => null,
            'tgl_invoice' => null,
            'status' => 0
        ]);
        return back()->with('success','Data berhasil ditarik!');
    }

    public function update(HutangPelayaran $hutangpelayaran, Request $request)
    {
        $data = $request->all();
        $hutangpelayaran->update($data);

        return back()->with('success', 'Data berhasil diupdate');
    }

    public function destroy(HutangPelayaran $hutangpelayaran)
    {
        $hutangpelayaran->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = HutangPelayaran::all()->sortByDesc('created_at');

        return Datatables::of($data)
            ->addColumn('tarif_pelayaran_id', function ($data) {
                return $data->tarif_pelayaran->pelayaran->nama;
            })
            ->addColumn('order_id', function ($data) {
                return $data->order->job . '-' . sprintf('%02d', $data->no_job);
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function cetak_invoice(Request $request)
    {
        $ids = $request->order_id;
        $order_id = explode(',', $request->order_id);
        // // dd($request->all());

        if (count($order_id) <= 1 && $order_id[0] == "") {
            return back()->with('danger', 'Harap checklist terlebih dahulu!');
        }

        $cek = HutangPelayaran::whereIn('order_id', $order_id)->get()->groupBy('pelayaran_id');
        if(count($cek)>1){
            return back()->with('danger', 'Harap checklist pelayaran yang sama!');
        }
        $cek = HutangPelayaran::whereIn('order_id', $order_id)->where('is_lock',0)->get();
        if(count($cek)>0){
            return back()->with('danger', 'Harap lock harga terlebih dahulu!');
        }
        $cek = Order::whereIn('id', $order_id)->get()->groupBy('jadwal_kapal_id');
        if(count($cek)>1){
            return back()->with('danger', 'Data Kapal dan Voyage yang dipilih tidak sama!');
        }
        $data = HutangPelayaran::join('order','order.id','hutang_pelayaran.order_id')->whereIn('hutang_pelayaran.order_id', $order_id)->orderBy('order.job')->orderBy('order.no_job')->get()->groupBy('order.job');
        $data_bl = HutangPelayaran::join('order','order.id','hutang_pelayaran.order_id')->whereIn('hutang_pelayaran.order_id', $order_id)->orderBy('order.job')->orderBy('order.no_job')->get()->groupBy('order.penerimabl');
        $pelayaran = HutangPelayaran::whereIn('order_id', $order_id)->first()->pelayaran;
        $hp = HutangPelayaran::whereIn('order_id', $order_id)->first();
        // $data = HutangPelayaran::whereIn('order_id', $order_id)->orderBy('created_at')->get()->groupBy('job');

        return view('admin.hutangpelayaran.invoice', compact('data','pelayaran','ids','hp','data_bl'));
    }

    public function cetak_invoice_get()
    {
        $order_id = request('order_id');
        $order = Order::where('id', $order_id)->first();
        if (!$order) {
            return back()->with('danger', 'Invoice Tidak ditemukan!');
        }
        $hutangpelayaran = HutangPelayaran::where('order_id', request('order_id'))->first();
        $nama = $hutangpelayaran->order->jadwal_kapal->pelayaran->nama;
        $data = Order::where('order_id', $order_id)->orderBy('tgl_muat')->get()->groupBy('job');
        return view('admin.hutangpelayaran.invoice', compact('order', 'data', 'nama'));
    }

    public function print()
    {
        $data = HutangPelayaran::with('order')->where('invoice',request('invoice'))->get();
        if($data->count()<=0){
            return back()->with('danger','Data tidak ditemukan!');
        }
        $hp = $data->first();
        $jobs = $data->groupBy('order.job');
        $jadwal_kapal = JadwalKapal::find($data->first()->order->jadwal_kapal_id);
        $opp = 0;
        $opt = 0;
        $ut = 0;
        if($hp->pph>0){
            $opp+=1;
        }
        if($hp->pembulatan>0){
            $opp+=1;
        }
        if($hp->penambahan_nominal!=0){
            $ut+=1;
        }
        foreach ($jobs as $list){
            $a = $list->where('opp','>',0)->groupBy('opp')->count();
            $b = $list->where('thc','>',0)->groupBy('thc')->count();
            $c = $list->where('apbs','>',0)->groupBy('apbs')->count();
            $d = $list->where('cleaning','>',0)->groupBy('cleaning')->count();
            $e = $list->where('opp_stamp','>',0)->groupBy('opp_stamp')->count();
            $f = $list->where('lss','>',0)->groupBy('lss')->count();
            $g = $list->where('opt','>',0)->groupBy('opt')->count();
            $h = $list->where('opt_stamp','>',0)->groupBy('opt_stamp')->count();
            $i = $list->where('ut','>',0)->groupBy('ut')->count();
            $j = $list->where('bl','>',0)->count();
            $k = $list->where('ut_stamp','>',0)->groupBy('ut_stamp')->count();
            $l = $list->where('ut_cleaning','>',0)->groupBy('ut_cleaning')->count();
            if($a>0){
                $opp+=$a;
            }
            if($b>0){
                $opp+=$b;
            }
            if($c>0){
                $opp+=$c;
            }
            if($d>0){
                $opp+=$d;
            }
            if($e>0){
                $opp+=$e;
            }
            if($f>0){
                $opp+=$f;
            }
            if($g>0){
                $opt+=$g;
            }
            if($h>0){
                $opt+=$h;
            }
            if($i>0){
                $ut+=$i;
            }
            if($j>0){
                $ut+=1;
            }
            if($k>0){
                $ut+=$k;
            }
            if($l>0){
                $ut+=$l;
            }
        }
        return view('admin.hutangpelayaran.print', compact('data','jadwal_kapal','jobs','hp','opp','opt','ut'));
    }

    function groupByValue($array) {
        $groups = [];

        foreach ($array as $item) {
            $groups[$item][] = $item;
        }

        return array_values($groups);
    }

    // public function show()
    // {
    //     return view('admin.hutangpelayaran.invoice');
    // }
}
