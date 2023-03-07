<?php

namespace App\Http\Controllers;

use App\Imports\OrderImport;
use App\Models\Barang;
use App\Models\Customer;
use App\Models\JadwalKapal;
use App\Models\Order;
use App\Models\Satuan;
use App\Models\Tarif;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    public function index()
    {
        $tarifs = Tarif::where('is_active',1)->get();
        $customers = Customer::pluck('nama','id');
        $barang = Barang::pluck('nama','id');
        $satuan = Satuan::pluck('nama','id');
        $pengirim = $customers;
        $tarif = array();
        foreach ($tarifs as $id => $item ) {
            $tarif[$item->id] = $item->customer->nama.' || '.$item->dari_lokasi->nama.' || '.$item->tujuan_lokasi->nama.' || '.$item->kondisiInfo->nama.' || '.$item->pelayaran->nama.' || '.$item->shipmentInfo->nama.' || '.$item->tarif;
        }
        return view('admin.order.index', compact('tarif','customers','barang','satuan','pengirim'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $barang = Barang::find($request->barang_id);
        if (!$barang) {
            $barang = Barang::create(['nama'=>$request->barang_id]);
        }
        $num = Order::max('no');
        $data['barang_id'] = $barang->id;
        $data['no'] = $num+1;
        $data['job'] = date('Ym').sprintf('%04d',$num+1);
        $data['no_job'] = 1;
        $satuan = Satuan::find($request->satuan);
        if(!$satuan){
            $satuan = Satuan::create(['nama'=>$request->satuan]);
        }
        $data['satuan'] = $satuan->id;
        $order = Order::create($data);
        return back()->with('success','Data berhasil disimpan');
    }

    public function update(Order $order, Request $request)
    {
        $data = $request->all();
        if ($request->ba_kembali && $request->invoice==1) {
            $data ['invoice'] = 'RAS/'.date('Ymd').'/'.sprintf('%03d',$order->id);
        }
        $barang = Barang::find($request->barang_id);
        if (!$barang) {
            $barang = Barang::create(['nama'=>$request->barang_id]);
        }
        $satuan = Satuan::find($request->satuan);
        if(!$satuan){
            $satuan = Satuan::create(['nama'=>$request->satuan]);
        }
        $data['satuan'] = $satuan->id;
        $data['barang_id'] = $barang->id;
        $order->update($data);

        return redirect()->route('order.index')->with('success','Data berhasil diupdate');
    }

    public function edit(Order $order)
    {
        $tarifs = Tarif::where('is_active',1)->get();
        $customers = Customer::pluck('nama','id');
        $barang = Barang::pluck('nama','id');
        $satuan = Satuan::pluck('nama','id');
        $pengirim = $customers;
        $tarif = array();
        foreach ($tarifs as $id => $item ) {
            $tarif[$item->id] = $item->customer->nama.' || '.$item->dari_lokasi->nama.' || '.$item->tujuan_lokasi->nama.' || '.$item->kondisiInfo->nama.' || '.$item->pelayaran->nama.' || '.$item->shipmentInfo->nama.' || '.$item->tarif;
        }
        return view('admin.order.edit', compact('order','tarif','customers','barang','satuan','pengirim'));
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function copy(Order $order)
    {
        $data = $order->toArray();
        $data['no_job'] = Order::where('job',$order->job)->max('no_job') + 1;
        Order::create($data);
        return back()->with('success','Copy data berhasil');
    }

    public function import(Request $request)
    {
        Excel::import(new OrderImport, $request->file);

        return back()->with('success', 'All good!');
    }

    public function datatable()
    {
        $limit = request('length');
        $start = request('start') * request('length');
        $data = Order::select('order.*');
        if(request('filter')&&request('filter')=='ba_kembali'){
            $data->whereNull('invoice');
        }
        if(request('filter')&&request('filter')=='invoice'){
            $data->whereNotNull('invoice');
        }

        $count = $data->count();

        return Datatables::of($data->offset($start)->limit($limit))
            ->setRowClass(function ($data) {
                $class = '';
                if($data->bttb->count()>0){
                    $class = 'bg-light-success';
                }
                if($data->jadwal_kapal->is_active != 1){
                    $class = 'bg-light-danger';
                }
                if(!is_null($data->invoice)){
                    $class = 'bg-light-warning';
                }

                return $class;
            })
            ->order(function ($query) {
                $query->orderBy('no', 'desc');
            })
            ->addColumn('tools', function($data){
                $ba_kembali = '';
                if (is_null($data->invoice)) {
                    $ba_kembali = '<li> <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#ba-'.$data->id.'">BA Kembali</button></li>';
                }
                $html = '<div class="dropend">
                            <button class="no-attr text-dark text-center dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size:.6rem"><i class="fas fa-list"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <form method="POST" action="'.route('order.copy',$data->id).'">
                                        <input type="hidden" name="_token" value="'.csrf_token().'" />
                                        <button onclick="return confirm(\'are you sure\')" type="submit" class="dropdown-item">Copy Order</a>
                                    </form>
                                </li>
                                <li><a class="dropdown-item" href="'.route('bttb.index',['order_id'=>$data->id]).'">BTTB</a></li>
                                <li><a class="dropdown-item" href="'.route('cetak.packingList',['order_id'=>$data->id]).'">Packing List</a></li>
                                <li><a class="dropdown-item" href="'.route('cetak.packingList.kubikasi',['order_id'=>$data->id]).'">Packing List Kubikasi</a></li>
                                '.$ba_kembali.'
                            </ul>
                        </div>

                        <div class="modal fade" id="ba-'.$data->id.'" tabindex="-1" aria-labelledby="ba-'.$data->id.'Label" aria-hidden="true">
                        <form action="'.route('order.update',$data).'" class="modal-dialog" method="post">
                            <input type="hidden" name="_token" value="'.csrf_token().'" />
                            <input type="hidden" name="_method" value="put" />
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="ba-'.$data->id.'Label">BA Kembali ('.$data->job.'-'.sprintf('%02d',$data->no_job).')</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col">
                                            <input type="date" name="ba_kembali" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" name="invoice" value="1" class="btn btn-primary" onclick="return confirm(\'are you sure?\')">Simpan</button>
                                </div>
                            </div>
                        </form>
                        </div>';
                return $html;
            })
            ->addColumn('no_job', function($data){
                return $data->job.'-'.sprintf('%02d',$data->no_job);
            })
            ->addColumn('marketing', function($data){
                return $data->tarif->customer->marketing->name ?? '-';
            })
            ->addColumn('cs', function($data){
                return $data->tarif->customer->cs->name ?? '-';
            })
            ->addColumn('pembayar', function($data){
                return $data->tarif->customer->nama ?? '-';
            })
            ->addColumn('pengirim', function($data){
                return $data->pengirim->nama ?? '-';
            })
            ->addColumn('penerima', function($data){
                return $data->penerima->nama ?? '-';
            })
            ->addColumn('dari', function($data){
                return $data->tarif->dari_lokasi->nama ?? '-';
            })
            ->addColumn('tujuan', function($data){
                return $data->tarif->tujuan_lokasi->nama ?? '-';
            })
            ->addColumn('shipment', function($data){
                return $data->tarif->shipmentInfo->nama ?? '-';
            })
            ->addColumn('kondisi', function($data){
                return $data->tarif->kondisiInfo->nama ?? '-';
            })
            ->addColumn('barang', function($data){
                return $data->barang->nama ?? '-';
            })
            ->addColumn('pelayaran', function($data){
                return $data->jadwal_kapal->pelayaran->nama ?? '-';
            })
            ->addColumn('kapal', function($data){
                return $data->jadwal_kapal->kapal->nama ?? '-';
            })
            ->addColumn('voyage', function($data){
                return $data->jadwal_kapal->voyage ?? '-';
            })
            ->addColumn('etd', function($data){
                return $data->jadwal_kapal->etd ?? '-';
            })
            ->addColumn('td', function($data){
                return $data->jadwal_kapal->td ?? '-';
            })
            ->addColumn('satuan', function($data){
                return $data->satuanInfo->nama ?? '-';
            })
            ->addColumn('unit', function($data){
                return $data->tarif->unitInfo->nama ?? '-';
            })
            ->addColumn('tarif', function($data){
                return number_format($data->tarif->tarif) ?? '-';
            })
            ->addColumn('penerima_bl_id', function($data){
                return $data->penerima_bl->nama ?? '-';
            })
            ->addColumn('action', function ($data) {
                // $tarifs = Tarif::where('is_active',1)->get();
                // $customers = Customer::pluck('nama','id');
                // $barang = Barang::pluck('nama','id');
                // $order = $data;
                // $tarif = array();
                // foreach ($tarifs as $id => $item ) {
                //     $tarif[$item->id] = $item->customer->nama.' || '.$item->dari_lokasi->nama.' || '.$item->tujuan_lokasi->nama.' || '.$item->kondisiInfo->nama.' || '.$item->jadwal_kapal->pelayaran->nama.' || '.$item->shipmentInfo->nama.' || '.$item->tarif.' || '.$item->jadwal_kapal->kapal->nama;
                // }
                // $view = view('admin.order.form',compact('tarif','customers','barang','order'))->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('order.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <a class="no-attr text-warning" title="Edit" href="'.route('order.edit',$data).'"><i class="fas fa-pencil"></i></a>
                        </div>';
                return $html;
            })
            ->rawColumns(['action','tools'])
            ->setFilteredRecords($count)
            ->toJson();
    }
}
