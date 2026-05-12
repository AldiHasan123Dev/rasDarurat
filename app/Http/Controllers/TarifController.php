<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\JadwalKapal;
use App\Models\Kondisi;
use App\Models\Lokasi;
use App\Models\Order;
use App\Models\Pelayaran;
use App\Models\Satuan;
use App\Models\Shipment;
use App\Models\Tarif;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class TarifController extends Controller
{
    public function index()
    {
        $jadwal_kapal = JadwalKapal::where('is_active',1)->get();
        $customer = Customer::pluck('nama','id');
        $lokasi = Lokasi::pluck('nama','id');
        $satuan = Satuan::pluck('nama','id');
        $kondisi = Kondisi::pluck('nama','id');
        $shipment = Shipment::pluck('nama','id');

        $kapal = array();
        foreach ($jadwal_kapal as $id => $item ) {
            $kapal[$item->id] = $item->kapal->nama.'('.$item->voyage.') || '.$item->pelayaran->nama.' || ETD '.date('d/m/y',strtotime($item->etd)).' || '.$item->rute;
        }
        return view('admin.tarif.index', compact('kapal','customer','lokasi','satuan','kondisi','shipment'));
    }

   public function store(Request $request)
    {
        $data = $request->all();
        $shipment = Shipment::find($request->shipment);
        $dari = Lokasi::find($request->dari);
        $tujuan = Lokasi::find($request->tujuan);
        $kondisi = Kondisi::find($request->kondisi);
        $satuan = Satuan::find($request->satuan);
        if(!$shipment){
            $shipment = Shipment::create(['nama'=>$request->shipment]);
        }
        if(!$dari){
            $dari = Lokasi::create(['nama'=>$request->dari]);
        }
        if(!$tujuan){
            $tujuan = Lokasi::create(['nama'=>$request->tujuan]);
        }
        if(!$kondisi){
            $kondisi = Kondisi::create(['nama'=>$request->kondisi]);
        }
        if($shipment->nama[0]=='F'||$shipment->nama[0]=='f'){
            $satuan = 1;
        }else{
            $satuan = 2;
        }
        $data['shipment'] = $shipment->id;
        $data['dari'] = $dari->id;
        $data['tujuan'] = $tujuan->id;
        $data['kondisi'] = $kondisi->id;
        $data['satuan'] = $satuan;
        $data['satuan_inv'] = $request->satuan_inv;
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        Tarif::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(Tarif $tarif, Request $request)
    {
        $data = $request->all();
        $cek = Order::where('tarif_id',$tarif->id)->whereHas('jadwal_kapal', function($q){
            $q->whereNotNull('td');
        })->count();

        if(!request('change_active')){
            if($cek>0){
                return back()->with('danger','Data tidak bisa diedit!');
            }
            
            $shipment = Shipment::find($request->shipment);
            $dari = Lokasi::find($request->dari);
            $tujuan = Lokasi::find($request->tujuan);
            $kondisi = Kondisi::find($request->kondisi);
            $satuan = Satuan::find($request->satuan);
            $satuan_inv = Satuan::find($request->satuan_inv);
            if(!$shipment){
                $shipment = Shipment::create(['nama'=>$request->shipment]);
            }
            if(!$dari){
                $dari = Lokasi::create(['nama'=>$request->dari]);
            }
            if(!$tujuan){
                $tujuan = Lokasi::create(['nama'=>$request->tujuan]);
            }
            if(!$kondisi){
                $kondisi = Kondisi::create(['nama'=>$request->kondisi]);
            }
            $data = $request->all();
            if($shipment->nama[0]=='F'||$shipment->nama[0]=='f'){
                $satuan = 1;
            }else{
                $satuan = 2;
            }
            $data['shipment'] = $shipment->id;
            $data['dari'] = $dari->id; 
            $data['tujuan'] = $tujuan->id;
             $data['satuan_inv'] = $satuan_inv->nama;
            $data['kondisi'] = $kondisi->id;
            $data['satuan'] = $satuan;
        }
        $tarif->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(Tarif $tarif)
    {
        $tarif->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function edit(Tarif $tarif)
    {
        $jadwal_kapal = JadwalKapal::where('is_active',1)->get();
        $customer = Customer::pluck('nama','id');
        $lokasi = Lokasi::pluck('nama','id'); 
        $satuan = Satuan::pluck('nama','id');
        $kondisi = Kondisi::pluck('nama','id');
        $shipment = Shipment::pluck('nama','id');
        $pelayaran = Pelayaran::pluck('nama','id');
        return view('admin.tarif.edit', compact('tarif','pelayaran','customer','lokasi','satuan','kondisi','shipment'));
    }

     public function editMarketing(Tarif $tarif)
    {
        $idMarketing = Auth::id();
        $jadwal_kapal = JadwalKapal::where('is_active',1)->get();
        $customer = Customer::pluck('nama','id');
        $lokasi = Lokasi::pluck('nama','id');
        $satuan = Satuan::pluck('nama','id');
        $kondisi = Kondisi::pluck('nama','id');
        $shipment = Shipment::pluck('nama','id');
        $pelayaran = Pelayaran::pluck('nama','id');
        return view('admin.tarif.edit_marketing', compact('tarif','pelayaran','customer','lokasi','satuan','kondisi','shipment','idMarketing'));
    }

    public function datatable()
    {
        $limit = request('length');
        $start = request('start') * request('length');
        $q = Tarif::query();
        $q->join('lokasi','lokasi.id','=','tarif.tujuan')->select('tarif.*','lokasi.nama');
        $data = $q->limit($start)->offset($limit);
        $count =  Tarif::query()->join('lokasi','lokasi.id','=','tarif.tujuan')->select('tarif.id')->count();
        if(request('customer_id')||!is_null(request('customer_id'))){
            $data = Tarif::query()->join('lokasi','lokasi.id','=','tarif.tujuan')->where('tarif.customer_id',request('customer_id'))->select('tarif.*','lokasi.nama')->limit($start)->offset($limit);
            $count = Tarif::query()->join('lokasi','lokasi.id','=','tarif.tujuan')->where('tarif.customer_id',request('customer_id'))->select('id')->count();
        }

        return Datatables::of($data)
            // ->addColumn('jadwal_kapal_id', function($data){
            //     return  $data->jadwal_kapal->kapal->nama.'('.$data->jadwal_kapal->voyage.') || '.$data->jadwal_kapal->pelayaran->nama.' || ETD '.date('d/m/y',strtotime($data->jadwal_kapal->etd)).' || '.$data->jadwal_kapal->rute ?? '-';
            // })
            ->order(function ($data){
                $data->orderBy('tarif.created_at','desc');
            })
            ->addColumn('updated_at', function($data){
                return  date('d/m/y', strtotime($data->updated_at));
            })
            ->addColumn('created_at', function($data){
                return  date('d/m/y', strtotime($data->created_at));
            })
            ->addColumn('pelayaran_id', function($data){
                return  $data->pelayaran->nama ?? '-';
            })
            ->addColumn('dari', function($data){
                return  $data->dari_lokasi->nama ?? '-';
            })
            ->addColumn('tujuan', function($data){
                return  $data->tujuan_lokasi->nama ?? '-';
            })
            ->addColumn('shipment', function($data){
                return  $data->shipmentInfo->nama ?? '-';
            })
            ->addColumn('kondisi', function($data){
                return  $data->kondisiInfo->nama ?? '-';
            })
            ->addColumn('satuan', function($data){
                return  $data->satuanInfo->nama ?? '-';
            })
            ->addColumn('customer_id', function($data){
                return  $data->customer->nama ?? '-';
            })
            ->addColumn('tarif', function($data){
                return  'Rp. '.number_format($data->tarif) ?? '-';
            })
            ->addColumn('status', function($data){
                $val = $data->is_active==1?0:1;
                $checked = $data->is_active==1?'checked':'';
                $name = $data->is_active==1?'active':'unactive';
                $html = '<form method="post" action="'.route('tarif.update',$data).'">
                            <input type="hidden" name="_token" value="'.csrf_token().'" />
                            <input type="hidden" name="_method" value="PUT" />
                            <input type="hidden" name="is_active" value="'.$val.'" />
                            <input type="hidden" name="change_active" value="true" />
                            <div class="form-check form-switch">
                                <input class="form-check-input" onchange="changeActive('.$data->id.','.$val.')" value="'.$val.'" type="checkbox" name="is_active" role="switch" id="flexSwitchCheckDefault" '.$checked.'>
                                <label class="form-check-label" for="flexSwitchCheckDefault">'.$name.'</label>
                            </div>
                        </form>';
                return  $html;
            })
            ->addColumn('action', function ($data) {
                // $tarif = $data;
                // $jadwal_kapal = JadwalKapal::where('is_active',1)->get();
                // $customer = Customer::pluck('nama','id');
                // $lokasi = Lokasi::pluck('nama','id');
                // $satuan = Satuan::pluck('nama','id');
                // $kondisi = Kondisi::pluck('nama','id');
                // $shipment = Shipment::pluck('nama','id');
                // $kapal = array();
                // foreach ($jadwal_kapal as $id => $item ) {
                //     $kapal[$item->id] = $item->kapal->nama.'('.$item->voyage.') || '.$item->pelayaran->nama.' || ETD '.date('d/m/y',strtotime($item->etd)).' || '.$item->rute;
                // }
                // $view = view('admin.tarif.form',compact('kapal','customer','lokasi','satuan','kondisi','shipment','tarif'))->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('tarif.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <a href="'.route('tarif.edit',$data).'" class="no-attr text-primary" title="Edit"><i class="fas fa-pencil"></i></a>
                        </div>';
                return $html;
            })
            ->rawColumns(['action','status'])
            ->setFilteredRecords($count)
            ->make(true);
    }

        public function datatable1()
    {
        $limit = request('length');
        $idMarketing = Auth::id();
        $start = request('start') * request('length');
        $q = Tarif::query();
        $q->join('lokasi', 'lokasi.id', '=', 'tarif.tujuan')
        ->join('customers', 'customers.id', '=', 'tarif.customer_id')
        ->where('customers.marketing_id',$idMarketing);
        $data = $q->limit($start)->offset($limit);
        $count =  Tarif::query()->join('lokasi','lokasi.id','=','tarif.tujuan')->join('customers', 'customers.id', '=', 'tarif.customer_id') ->where('customers.marketing_id',$idMarketing)->select('tarif.id')->count();
        if(request('customer_id')||!is_null(request('customer_id'))){
            $data = Tarif::query()->join('lokasi','lokasi.id','=','tarif.tujuan')->where('tarif.customer_id',request('customer_id'))->select('tarif.*','lokasi.nama')->limit($start)->offset($limit);
            $count = Tarif::query()->join('lokasi','lokasi.id','=','tarif.tujuan')->where('tarif.customer_id',request('customer_id'))->select('id')->count();
        }

        return Datatables::of($data)
            // ->addColumn('jadwal_kapal_id', function($data){
            //     return  $data->jadwal_kapal->kapal->nama.'('.$data->jadwal_kapal->voyage.') || '.$data->jadwal_kapal->pelayaran->nama.' || ETD '.date('d/m/y',strtotime($data->jadwal_kapal->etd)).' || '.$data->jadwal_kapal->rute ?? '-';
            // })
            ->order(function ($data){
                $data->orderBy('tarif.created_at','desc');
            })
            ->addColumn('updated_at', function($data){
                return  date('d/m/y', strtotime($data->updated_at));
            })
            ->addColumn('created_at', function($data){
                return  date('d/m/y', strtotime($data->created_at));
            })
            ->addColumn('pelayaran_id', function($data){
                return  $data->pelayaran->nama ?? '-';
            })
            ->addColumn('dari', function($data){
                return  $data->dari_lokasi->nama ?? '-';
            })
            ->addColumn('tujuan', function($data){
                return  $data->tujuan_lokasi->nama ?? '-';
            })
            ->addColumn('shipment', function($data){
                return  $data->shipmentInfo->nama ?? '-';
            })
            ->addColumn('kondisi', function($data){
                return  $data->kondisiInfo->nama ?? '-';
            })
            ->addColumn('satuan', function($data){
                return  $data->satuanInfo->nama ?? '-';
            })
            ->addColumn('customer_id', function($data){
                return  $data->customer->nama ?? '-';
            })
            ->addColumn('tarif', function($data){
                return  'Rp. '.number_format($data->tarif) ?? '-';
            })
            ->addColumn('status', function($data){
                $val = $data->is_active==1?0:1;
                $checked = $data->is_active==1?'checked':'';
                $name = $data->is_active==1?'active':'unactive';
                $html = '<form method="post" action="'.route('tarif.update',$data).'">
                            <input type="hidden" name="_token" value="'.csrf_token().'" />
                            <input type="hidden" name="_method" value="PUT" />
                            <input type="hidden" name="is_active" value="'.$val.'" />
                            <input type="hidden" name="change_active" value="true" />
                            <div class="form-check form-switch">
                                <input class="form-check-input" onchange="changeActive('.$data->id.','.$val.')" value="'.$val.'" type="checkbox" name="is_active" role="switch" id="flexSwitchCheckDefault" '.$checked.'>
                                <label class="form-check-label" for="flexSwitchCheckDefault">'.$name.'</label>
                            </div>
                        </form>';
                return  $html;
            })
            ->addColumn('action', function ($data) {
                // $tarif = $data;
                // $jadwal_kapal = JadwalKapal::where('is_active',1)->get();
                // $customer = Customer::pluck('nama','id');
                // $lokasi = Lokasi::pluck('nama','id');
                // $satuan = Satuan::pluck('nama','id');
                // $kondisi = Kondisi::pluck('nama','id');
                // $shipment = Shipment::pluck('nama','id');
                // $kapal = array();
                // foreach ($jadwal_kapal as $id => $item ) {
                //     $kapal[$item->id] = $item->kapal->nama.'('.$item->voyage.') || '.$item->pelayaran->nama.' || ETD '.date('d/m/y',strtotime($item->etd)).' || '.$item->rute;
                // }
                // $view = view('admin.tarif.form',compact('kapal','customer','lokasi','satuan','kondisi','shipment','tarif'))->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('tarif.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <a href="'.route('tarif.edit_marketing',$data).'" class="no-attr text-primary" title="Edit"><i class="fas fa-pencil"></i></a>
                        </div>';
                return $html;
            })
            ->rawColumns(['action','status'])
            ->setFilteredRecords($count)
            ->make(true);
    }
}
