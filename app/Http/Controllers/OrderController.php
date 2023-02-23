<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Customer;
use App\Models\Order;
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

        $tarif = array();
        foreach ($tarifs as $id => $item ) {
            $tarif[$item->id] = $item->customer->nama.' || '.$item->dari_lokasi->nama.' || '.$item->tujuan_lokasi->nama.' || '.$item->kondisiInfo->nama.' || '.$item->jadwal_kapal->pelayaran->nama.' || '.$item->shipmentInfo->nama.' || '.$item->tarif.' || '.$item->jadwal_kapal->kapal->nama;
        }
        return view('admin.order.index', compact('tarif','customers','barang'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $barang = Barang::where('nama',$request->barang_id)->first();
        if (!$barang) {
            $barang = Barang::create(['nama'=>$request->barang_id]);
        }
        $data['barang_id'] = $barang->id;
        $data['job'] = date('Ymd').sprintf('%02d',1);
        $data['no_job'] = sprintf('%02d',1);
        $order = Order::create($data);
        $order->update([
            'job' => date('Ymd').sprintf('%02d',$order->id)
        ]);
        return back()->with('success','Data berhasil disimpan');
    }

    public function update(Order $order, Request $request)
    {
        $data = $request->all();
        $order->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = Order::all();

        return Datatables::of($data)
            ->addColumn('no_job', function($data){
                return $data->job.'-'.$data->no_job;
            })
            ->addColumn('marketing', function($data){
                return $data->tarif->customer->marketing->nama ?? '-';
            })
            ->addColumn('cs', function($data){
                return $data->tarif->customer->cs->nama ?? '-';
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
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasOrderUpdate'.$data->id.'" aria-controls="offcanvasOrderUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
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
            ->rawColumns(['action'])
            ->make(true);
    }
}
