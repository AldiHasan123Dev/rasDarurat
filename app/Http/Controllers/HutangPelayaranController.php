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
                $q->whereIn('jadwal_kapal.kapal_id',$kapal);
            }
            $q->select('order.job','order.tipe','hutang_pelayaran.is_lock','hutang_pelayaran.ut','dari.nama as dari','tujuan.nama as tujuan','order.tarif_id','order.container','order.seal','order.no_job','order.id','order.jadwal_kapal_id','jadwal_kapal.pelayaran_id','jadwal_kapal.kapal_id','jadwal_kapal.voyage','kapal.nama as nama_kapal','pelayaran.nama','shipments.nama as fit');
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
            $prop['status'] = 1;
            HutangPelayaran::find($id)->update($prop);
            array_push($ids,$id);
        }

        $tgl = [$data['no_bg_opp'],$data['no_bg_opt'],$data['no_bg_ut']];
        $tgl_group = array_filter(array_unique($tgl));
        $data_nomor = array();
        $no = Jurnal::where('tipe','TEST')->max('no') + 1;
        foreach($tgl_group as $tg){
            $nomor = 'TS/'.date('ymd').'/'.sprintf('%02d',$no);
            $data_nomor[$tg] = ['no'=>$no,'nomor'=>$nomor];
            $no++;
        }

        $lists = HutangPelayaran::with(['order','order.tarif','order.tarif.shipmentInfo','order.tarif.customer'])->whereIn('id',$ids)->get()->toArray();
        foreach ($lists as $item) {
            $opp = ['opp','apbs','cleaning','thc','lss','opp_stamp'];
            $opt = ['opt','opt_stamp'];
            $ut = ['ut','ut_stamp','bl'];
            foreach($opp as $a){
                if($a=='thc'){
                    $title = 'THC LOLO';
                }else if($a=='opp_stamp'){
                    $title = 'STAMP OPP';
                }else{
                    $title = strtoupper($a);
                }
                $name = $title.' (1X'.preg_replace("/[^0-9]/", "", $item['order']['tarif']['shipment_info']['nama'] ).' )  '.$item['order']['tarif']['customer']['nama'].' ( '.$item['order']['job'].'-'.sprintf('%02d',$item['order']['no_job']).')';
                if($item[$a]>0 && !is_null($item['no_bg_opp'])){
                    Jurnal::create([
                        'tipe' => 'TEST',
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
                    Jurnal::create([
                        'tipe' => 'TEST',
                        'no_bg' => $item['no_bg_opp'],
                        'tgl_bg' => $item['tgl_bg_opp'],
                        'nominal_bg' => $item['nominal_bg_opp'],
                        'coa_id' => 62,
                        'order_id' => $item['order_id'],
                        'nomor' => $data_nomor[$item['no_bg_opp']]['nomor'],
                        'no' => $data_nomor[$item['no_bg_opp']]['no'],
                        'nama' => $name,
                        'debit' => 0,
                        'credit' => $item[$a],
                    ]);
                }
            }
            foreach($opt as $a){
                if($a=='opt_stamp'){
                    $title = 'STAMP OPT';
                }else{
                    $title = strtoupper($a);
                }
                $name = $title.' (1X'.preg_replace("/[^0-9]/", "", $item['order']['tarif']['shipment_info']['nama'] ).' )  '.$item['order']['tarif']['customer']['nama'].' ( '.$item['order']['job'].'-'.sprintf('%02d',$item['order']['no_job']).')';
                if($item[$a]>0 && !is_null($item['no_bg_opt'])){
                    Jurnal::create([
                        'tipe' => 'TEST',
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
                    Jurnal::create([
                        'tipe' => 'TEST',
                        'no_bg' => $item['no_bg_opt'],
                        'tgl_bg' => $item['tgl_bg_opt'],
                        'nominal_bg' => $item['nominal_bg_opt'],
                        'coa_id' => 62,
                        'order_id' => $item['order_id'],
                        'nomor' => $data_nomor[$item['no_bg_opt']]['nomor'],
                        'no' => $data_nomor[$item['no_bg_opt']]['no'],
                        'nama' => $name,
                        'debit' => 0,
                        'credit' => $item[$a],
                    ]);
                }
            }
            foreach($ut as $a){
                if($a=='ut_stamp'){
                    $title = 'STAMP UT';
                }else{
                    $title = strtoupper($a);
                }
                $name = $title.' (1X'.preg_replace("/[^0-9]/", "", $item['order']['tarif']['shipment_info']['nama'] ).' )  '.$item['order']['tarif']['customer']['nama'].' ( '.$item['order']['job'].'-'.sprintf('%02d',$item['order']['no_job']).')';
                if($item[$a]>0 && !is_null($item['no_bg_ut'])){
                    Jurnal::create([
                        'tipe' => 'TEST',
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
                    Jurnal::create([
                        'tipe' => 'TEST',
                        'no_bg' => $item['no_bg_ut'],
                        'tgl_bg' => $item['tgl_bg_ut'],
                        'nominal_bg' => $item['nominal_bg_ut'],
                        'coa_id' => 62,
                        'order_id' => $item['order_id'],
                        'nomor' => $data_nomor[$item['no_bg_ut']]['nomor'],
                        'no' => $data_nomor[$item['no_bg_ut']]['no'],
                        'nama' => $name,
                        'debit' => 0,
                        'credit' => $item[$a],
                    ]);
                }
            }
        }

        return redirect()->route('hutang-pelayaran.cetak',['invoice'=>$code]);
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
        $data = HutangPelayaran::whereIn('order_id', $order_id)->orderBy('created_at')->get()->groupBy('job');
        $pelayaran = HutangPelayaran::whereIn('order_id', $order_id)->first()->pelayaran;
        // $data = HutangPelayaran::whereIn('order_id', $order_id)->orderBy('created_at')->get()->groupBy('job');

        return view('admin.hutangpelayaran.invoice', compact('data','pelayaran'));
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
        return view('admin.hutangpelayaran.print', compact('data','jadwal_kapal','jobs','hp'));
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
