<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Satuan;
use App\Models\Tarif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

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
            $tarif[$item->id] = $item->customer->nama.' || '.$item->dari_lokasi->nama.' || '.$item->tujuan_lokasi->nama.' || '.$item->kondisiInfo->nama.' || '.$item->jadwal_kapal->pelayaran->nama.' || '.$item->shipmentInfo->nama.' || '.$item->tarif.' || '.$item->jadwal_kapal->kapal->nama;
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
        $data['barang_id'] = $barang->id;
        $data['job'] = date('Ymd').sprintf('%02d',1);
        $data['no_job'] = 1;
        $order = Order::create($data);
        $order->update([
            'job' => date('Ymd').sprintf('%02d',$order->id)
        ]);
        return back()->with('success','Data berhasil disimpan');
    }

    public function update(Order $order, Request $request)
    {
        $data = $request->all();
        if ($request->ba_kembali && $request->invoice==1) {
            $data ['invoice'] = 'RAS/'.date('Ymd').'/'.sprintf('%03d',$order->id);
        }
        $order->update($data);

        return back()->with('success','Data berhasil diupdate');
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

    public function datatable()
    {
        $data = Order::query();

        if(request('filter')&&request('filter')=='ba_kembali'){
            $data->whereNull('invoice');
        }
        if(request('filter')&&request('filter')=='invoice'){
            $data->whereNotNull('invoice');
        }

        return Datatables::of($data)
            ->setRowClass(function ($data) {
                $class = '';
                if($data->bttb->count()>0){
                    $class = 'bg-light-success';
                }
                if($data->tarif->jadwal_kapal->is_active != 1){
                    $class = 'bg-light-danger';
                }
                if(!is_null($data->invoice)){
                    $class = 'bg-light-warning';
                }

                return $class;
            })
            ->orderColumns(['job'], '-:column $1')
            ->addColumn('tools', function($data){
                $ba_kembali = '';
                if (is_null($data->invoice)) {
                    $ba_kembali = '<li> <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#ba-'.$data->id.'">BA Kembali</button></li>';
                }
                $html = '<div class="dropend">
                            <button class="no-attr text-dark text-center dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size:.6rem"><i class="fas fa-list"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <form method="POST" action="'.route('order.copy',$data).'">
                                        <input type="hidden" name="_token" value="'.csrf_token().'" />
                                        <button onclick="return confirm(\'are you sure\')" type="submit" class="dropdown-item">Copy Order</a>
                                    </form>
                                </li>
                                <li><a class="dropdown-item" href="'.route('bttb.index',['order_id'=>$data->id]).'">BTTB</a></li>
                                <li><a class="dropdown-item" href="'.route('cetak.packingList',['order_id'=>$data->id]).'">Packing List</a></li>
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
                return $data->tarif->jadwal_kapal->pelayaran->nama ?? '-';
            })
            ->addColumn('kapal', function($data){
                return $data->tarif->jadwal_kapal->kapal->nama ?? '-';
            })
            ->addColumn('voyage', function($data){
                return $data->tarif->jadwal_kapal->voyage ?? '-';
            })
            ->addColumn('etd', function($data){
                return $data->tarif->jadwal_kapal->etd ?? '-';
            })
            ->addColumn('td', function($data){
                return $data->tarif->jadwal_kapal->td ?? '-';
            })
            ->addColumn('satuan', function($data){
                return $data->tarif->satuanInfo->nama ?? '-';
            })
            ->addColumn('unit', function($data){
                return $data->tarif->unitInfo->nama ?? '-';
            })
            ->addColumn('tarif', function($data){
                return number_format($data->tarif->tarif) ?? '-';
            })
            ->addColumn('action', function ($data) {
                $tarifs = Tarif::where('is_active',1)->get();
                $customers = Customer::pluck('nama','id');
                $barang = Barang::pluck('nama','id');
                $order = $data;
                $tarif = array();
                foreach ($tarifs as $id => $item ) {
                    $tarif[$item->id] = $item->customer->nama.' || '.$item->dari_lokasi->nama.' || '.$item->tujuan_lokasi->nama.' || '.$item->kondisiInfo->nama.' || '.$item->jadwal_kapal->pelayaran->nama.' || '.$item->shipmentInfo->nama.' || '.$item->tarif.' || '.$item->jadwal_kapal->kapal->nama;
                }
                $view = view('admin.order.form',compact('tarif','customers','barang','order'))->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('order.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-warning" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasOrderUpdate'.$data->id.'" aria-controls="offcanvasOrderUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvasOrderUpdate'.$data->id.'" aria-labelledby="offcanvasOrderUpdate'.$data->id.'Label" style="height:700px">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasOrderUpdate'.$data->id.'Label">Form Order</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('order.update',$data).'" method="post" id="update">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                    <input type="hidden" name="_method" value="PUT" />
                                    '.$view.'
                                </form>
                            </div>
                        </div>
                        <script>
                                var val = '.$data->tarif_id.';
                                console.log(val);
                                $.ajax({
                                    type: "POST",
                                    url: "'.route('api.tarif.getOne').'",
                                    data: {id:val},
                                    success: function (response) {
                                        let data = response;
                                        let tarif = data.tarif;
                                        $("form#update #tarif").val("Rp. "+tarif.toLocaleString("en-US"));
                                        $("form#update #dari").val(data.dari);
                                        $("form#update #tujuan").val(data.tujuan);
                                        $("form#update #shipment").val(data.shipment);
                                        $("form#update #kondisi").val(data.kondisi);
                                        $("form#update #satuan").val(data.satuan);
                                    }
                                });
                        </script>';
                return $html;
            })
            ->rawColumns(['action','tools'])
            ->toJson();
    }
}
