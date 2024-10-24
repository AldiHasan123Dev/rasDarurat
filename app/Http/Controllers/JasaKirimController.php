<?php

namespace App\Http\Controllers;

use App\Models\Agen;
use App\Models\COA;
use App\Models\JasaKirim;
use App\Models\Jurnal;
use App\Models\Lokasi;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class JasaKirimController extends Controller
{
    public function index()
    {
        $loc_id = JasaKirim::pluck('lokasi_id')->toArray();
        $loc_id = array_unique($loc_id);
        $lokasi = Lokasi::whereIn('id',$loc_id)->orderBy('nama')->get(['id','nama']);
        $start_date = request('start_date') ?? null;
        $end_date = request('end_date') ?? null;
        $tujuan = request('tujuan') ?? null;
        $search = request('searching') ?? null;
        $barcode = request('barcode') ?? null;
        $role = request('role') ?? 'all';
        $data = JasaKirim::whereNotNull('invoice')->orderBy('invoice','desc')->get()->groupBy('invoice');
        return view('admin.jasakirim.index',compact('lokasi','start_date','end_date','tujuan','role','data','search','barcode'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        JasaKirim::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(JasaKirim $jasakirim, Request $request)
    {
        $data = $request->all();
        $jasakirim->update($data);
        if ($request->tgl_kirim) {
            if(!is_null($request->tgl_kirim)){
                Order::where('jasa_kirim_id',$jasakirim->id)->update([
                    'ba_kirim' => $request->tgl_kirim
                ]);
            }
        }

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(JasaKirim $jasakirim)
    {
        $jasakirim->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function syncNominal()
    {
        $data = JasaKirim::join('lokasi','lokasi.id','=','jasa_kirim.lokasi_id')
                    ->select('jasa_kirim.*','lokasi.nama')
                    ->whereNull('nominal')
                    ->orWhere('nominal',0)
                    ->orderBy('lokasi.nama')
                    ->get();
        foreach ($data as $item) {
            $agen = Agen::find($item->agen_id);
            $lokasi = Lokasi::find($agen->lokasi_id);
            $item->update([
                'nominal' => $lokasi->harga
            ]);
        }

        return back()->with('success','Sinkronisasi data berhasil');
    }

    public function syncData()
    {
        $data = JasaKirim::join('lokasi','lokasi.id','=','jasa_kirim.lokasi_id')
                    ->select('jasa_kirim.*','lokasi.nama')
                    ->orderBy('lokasi.nama')
                    ->get();
        foreach ($data as $item) {
            $agen = Agen::find($item->agen_id);
            $lokasi = Lokasi::find($agen->lokasi_id);
            $item->update([
                'nominal' => $lokasi->harga
            ]);
        }
        return back()->with('success','Sinkronisasi data berhasil');
    }

    public function jurnal()
    {
        $inv = request('invoice');
        if(!$inv){
            return back()->with('danger', 'Kode Draf tidak ditemukan!');
        }

        $data = JasaKirim::where('invoice',$inv)->get();
        return view('admin.jasakirim.jurnal',compact('data'));
    }

    public function generateJurnal(Request $request)
    {
        $jasa_kirim_id = JasaKirim::where('invoice',$request->invoice)->pluck('id')->toArray();
        $order_id = Order::whereIn('jasa_kirim_id',$jasa_kirim_id)->pluck('id')->toArray();
        $generate_jurnal = $this->check_omset($order_id);
        $this->jurnalTemplate($request->invoice, $request->nomor, $request->no, $request->created_at);
        return redirect()->route('jasakirim.index',['role'=>'jurnal'])->with('success','Jurnal berhasil disimpan!');
    }

    private function check_omset($order_id)
    {
        foreach($order_id as $id){
            $jurnals = Jurnal::where('order_id',$id)->where('coa_id',93)->where('debit','>',0)->get();
            $order = Order::find($id);
            if($jurnals->count()>0 && $order){
                return false;
            }
        }
        return true;
    }


    public function jurnalTemplate($invoice, $nomor, $no, $created_at)
    {
        $data = JasaKirim::where('invoice',$invoice)->get();
        $err = [];
        foreach ($data as $idx => $item) {
            $count = $item->orders->count() + $item->kirim_dokumen->count();
            if($count==0){
                array_push($err, $item->barcode);
            }
        }
        if(count($err)>0){
            return back()->with('danger', 'Jasa Kirim '.json_encode($err).' tidak memiliki ID JOB!');
        }
        $c76 = COA::where('coa_ras', 76)->first()->id ?? 76;
        $c31 = COA::where('coa_ras', 31)->first()->id ?? 31;
        $c63 = COA::where('coa_ras', 63)->first()->id ?? 63;
        foreach ($data as $idx => $item) {
            $is_first = true;
            $count = $item->orders->count() + $item->kirim_dokumen->count();
            $price = (int)($item->nominal / $count);
            $selisih = $item->nominal - ($price * $count);
            foreach($item->orders as $fs => $order){
                if($is_first){
                    $price2 = (int)($item->nominal / $count) + $selisih;
                    $is_first = false;
                    Jurnal::create([
                        'tipe' => 'JNL',
                        'coa_id' => ($order->checkOmset() ? $c76 : $c31),
                        'order_id' => $order->id,
                        'nomor' => $nomor,
                        'nama' => 'Biaya Pengiriman Dokumen '. ($order->agent->nama ?? '-') .' ('.($order->agent->lokasi->nama ?? '-').')',
                        'debit' => $price2,
                        'created_at' => $created_at,
                        'no' => $no
                    ]);
                }else{
                    Jurnal::create([
                        'tipe' => 'JNL',
                        'coa_id' => ($order->checkOmset() ? $c76 : $c31),
                        'order_id' => $order->id,
                        'nomor' => $nomor,
                        'nama' => 'Biaya Pengiriman Dokumen '. ($order->agent->nama ?? '-') .' ('.($order->agent->lokasi->nama ?? '-').')',
                        'debit' => $price,
                        'created_at' => $created_at,
                        'no' => $no
                    ]);
                }
            }
            foreach($item->kirim_dokumen as $kirim){
                Jurnal::create([
                    'tipe' => 'JNL',
                    'coa_id' => ($kirim->order->checkOmset() ? $c76 : $c31),
                    'order_id' => $kirim->order_id,
                    'nomor' => $nomor,
                    'nama' => $kirim->nama,
                    'debit' => $price,
                    'created_at' => $created_at,
                    'no' => $no
                ]);
            }
            $item->update([
                'status' => 1,
                'jurnal' => $nomor
            ]);
        }
        Jurnal::create([
            'tipe' => 'JNL',
            'coa_id' => $c63,
            'order_id' => $order->id,
            'nomor' => $nomor,
            'nama' => 'Hutang Agen ('.$invoice.')',
            'credit' => $data->sum('nominal'),
            'created_at' => $created_at,
            'no' => $no
        ]);

        return true;
    }

    public function syncJurnal(Request $request)
    {
        $invoice = $request->invoice;
        $nomor = JasaKirim::where('invoice',$invoice)->whereNotNull('jurnal')->first();
        $data = JasaKirim::where('invoice',$invoice)->get();
        if(!$nomor){
            return back()->with('danger', 'Kode Draf tidak ditemukan!');
        }
        $err = [];
        foreach ($data as $idx => $item) {
            $count = $item->orders->count() + $item->kirim_dokumen->count();
            if($count==0){
                array_push($err, $item->barcode);
            }
        }
        if(count($err)>0){
            return back()->with('danger', 'Jasa Kirim '.json_encode($err).' tidak memiliki ID JOB!');
        }
        DB::transaction(function () use($nomor, $invoice) {
            $data = JasaKirim::where('invoice',$invoice)->get();
            $jurnal = Jurnal::where('nomor',$nomor->jurnal)->first();
            Jurnal::where('nomor',$nomor->jurnal)->delete();
            foreach ($data as $idx => $item) {
                $is_first = true;
                $count = $item->orders->count() + $item->kirim_dokumen->count();
                $price = (int)($item->nominal / $count);
                $selisih = $item->nominal - ($price * $count);
                foreach($item->orders as $fs => $order){
                    if($is_first){
                        $price2 = (int)($item->nominal / $count) + $selisih;
                        $is_first = false;
                        Jurnal::create([
                            'tipe' => 'JNL',
                            'coa_id' => 31,
                            'order_id' => $order->id,
                            'nomor' => $jurnal->nomor,
                            'nama' => 'Biaya Pengiriman Dokumen '. ($order->agent->nama ?? '-') .' ('.($order->agent->lokasi->nama ?? '-').')',
                            'debit' => $price2,
                            'created_at' => $jurnal->created_at,
                            'no' => $jurnal->no
                        ]);
                    }else{
                        Jurnal::create([
                            'tipe' => 'JNL',
                            'coa_id' => 31,
                            'order_id' => $order->id,
                            'nomor' => $jurnal->nomor,
                            'nama' => 'Biaya Pengiriman Dokumen '. ($order->agent->nama ?? '-') .' ('.($order->agent->lokasi->nama ?? '-').')',
                            'debit' => $price,
                            'created_at' => $jurnal->created_at,
                            'no' => $jurnal->no
                        ]);
                    }
                }
                foreach($item->kirim_dokumen as $kirim){
                    Jurnal::create([
                        'tipe' => 'JNL',
                        'coa_id' => 31,
                        'order_id' => $kirim->order_id,
                        'nomor' => $jurnal->nomor,
                        'nama' => $kirim->nama,
                        'debit' => $price,
                        'created_at' => $jurnal->created_at,
                        'no' => $jurnal->no
                    ]);
                }
                $item->update([
                    'status' => 1,
                    'jurnal' => $jurnal->nomor
                ]);
            }
            Jurnal::create([
                'tipe' => 'JNL',
                'coa_id' => 63,
                'order_id' => $order->id,
                'nomor' => $jurnal->nomor,
                'nama' => 'Hutang Agen ('.$invoice.')',
                'credit' => $data->sum('nominal'),
                'created_at' => $jurnal->created_at,
                'no' => $jurnal->no
            ]);
        });

        return redirect()->route('jasakirim.index',['role'=>'jurnal'])->with('success','Jurnal berhasil disinkronisasi!');
    }

    public function datatable()
    {
        $role = request('role');
        if(request('nominal')==1){
            $query = JasaKirim::query();
            $query->join('lokasi','lokasi.id','=','jasa_kirim.lokasi_id');
            $query->select('jasa_kirim.*','lokasi.nama');
            $query->whereNull('merger');
            $query->whereNotNull('nominal');
            $query->where('nominal','>',0);
            if(!is_null(request('start_date')) && !is_null(request('end_date'))){
                $query->whereBetween('tgl_kirim',[request('start_date'),request('end_date')]);
            }
            if(!is_null(request('tujuan'))){
                $query->where('lokasi_id',request('tujuan'));
            }
            if(request('role')=='cs'){
                $query->whereNull('tgl_terima');
            }
            if(request('role')=='kasir'){
                $query->whereNull('jurnal');
                $query->whereNull('invoice');
            }
            if(!is_null(request('searching'))){
                $query = JasaKirim::query();
                $full_job = explode('-',request('searching'));
                $query->orWhereHas('orders', function($q) use($full_job){
                    $q->where('job','like','%'.$full_job[0].'%');
                    if(!empty($full_job[1])){
                        $q->where('no_job','like','%'.(int)$full_job[1].'%');
                    }
                });
            }
            if(!is_null(request('barcode'))){
                $query->where('barcode','LIKE','%'.request('barcode').'%');
            }
            $query->orderBy('tgl_kirim','desc');
            $data = $query->get();
        }else{
            $data = JasaKirim::join('lokasi','lokasi.id','=','jasa_kirim.lokasi_id')
                    ->select('jasa_kirim.*','lokasi.nama')
                    ->whereNull('nominal')
                    ->orWhere('nominal',0)
                    ->orderBy('lokasi.nama')
                    ->get();
        }

        return Datatables::of($data)
            ->addColumn('lokasi_id', function($data){
                return $data->lokasi->nama;
            })
            ->addColumn('kota', function($data){
                return $data->agen->lokasi->nama ?? '-';
            })
            ->addColumn('nominal', function($data){
                return $data->nominal ? number_format($data->nominal) : '-';
            })
            ->addColumn('orders', function($data){
                // $name = '';
                // foreach ($data->orders as $item ) {
                //     $name .= $item->job.'-'.sprintf('%02d',$item->no_job).'; ';
                // }
                return $data->order_name();
            })
            ->addColumn('action', function ($data) use($role) {
                $view = view('admin.jasakirim.form',['jasakirim'=>$data,'role'=>$role])->render();
                if($role=='kasir'){
                    $html = '<div class="d-flex gap-1">
                                <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasJasaKirimUpdate'.$data->id.'" aria-controls="offcanvasJasaKirimUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                            </div>

                            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasJasaKirimUpdate'.$data->id.'" aria-labelledby="offcanvasJasaKirimUpdate'.$data->id.'Label">
                                <div class="offcanvas-header">
                                    <h5 class="offcanvas-title" id="offcanvasJasaKirimUpdate'.$data->id.'Label">Form JasaKirim</h5>
                                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                </div>
                                <div class="offcanvas-body">
                                    <form action="'.route('jasakirim.update',$data).'" method="post">
                                    <input type="hidden" name="_token" value="'.csrf_token().'" />
                                        <input type="hidden" name="_method" value="PUT" />
                                        '.$view.'
                                    </form>
                                </div>
                            </div>';
                }else{
                    // <form action="'.route('jasakirim.destroy',$data).'" method="post">
                    //                 <input type="hidden" name="_token" value="'.csrf_token().'" />
                    //                 <input type="hidden" name="_method" value="delete" />
                    //                 <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                    //             </form>
                    $html = '<div class="d-flex gap-1">

                                <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasJasaKirimUpdate'.$data->id.'" aria-controls="offcanvasJasaKirimUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                                <a href="'.route('cetak.dooring',['jadwal_kapal_id'=>$data->jadwal_kapal_id,'tujuan'=>$data->lokasi_id,'agent'=>$data->agen_id]).'" class="text-success"><i class="fas fa-print"></i></a>
                            </div>

                            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasJasaKirimUpdate'.$data->id.'" aria-labelledby="offcanvasJasaKirimUpdate'.$data->id.'Label">
                                <div class="offcanvas-header">
                                    <h5 class="offcanvas-title" id="offcanvasJasaKirimUpdate'.$data->id.'Label">Form JasaKirim</h5>
                                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                </div>
                                <div class="offcanvas-body">
                                    <form action="'.route('jasakirim.update',$data).'" method="post">
                                    <input type="hidden" name="_token" value="'.csrf_token().'" />
                                        <input type="hidden" name="_method" value="PUT" />
                                        '.$view.'
                                    </form>
                                </div>
                            </div>';
                }
                return $html;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

}
