<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderTruckingResource;
use App\Models\CustomerTrucking;
use App\Models\Kendaraan;
use App\Models\Order;
use App\Models\OrderTrucking;
use App\Models\SanguSopir;
use App\Models\Sopir;
use App\Models\TarifTrucking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class OrderTruckingController extends Controller
{
    public function index()
    {
        $data = OrderTrucking::all();
        $data = OrderTruckingResource::collection($data);
        $kendaraan = Kendaraan::all()->where('is_active',1)->sortBy('nopol');
        $sopir = Sopir::where('is_active',1)->orderBy('nama','asc')->get();
        $tujuan = SanguSopir::join('lokasi','lokasi.id','=','sangu_sopir.tujuan')->select('sangu_sopir.*')->orderBy('lokasi.nama','asc')->get();
        $customers = CustomerTrucking::all()->sortBy('nama');
        $update = OrderTrucking::whereNull('order_id')->get();
        foreach ($update as $item ) {
            $order = Order::where('container',$item->container)->first();
            if($order){
                $item->update(['order_id'=>$order->id]);
            }
        }
        return view('admin.ordertrucking.index', compact('data','kendaraan','sopir','tujuan','customers'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $sangu = SanguSopir::find($data['tujuan']);
        $tarif = TarifTrucking::where('customer_id',$data['customer_id'])->where('tujuan_id',$data['tujuan'])->where('tipe',$data['tipe'])->where('is_active',1)->first();
        if(!$tarif){
            return back()->with('danger','Master Tarif Customer belum dibuat! Harap input master tarif terlebih dahulu dan pastikan tarif berstatus Aktif!');
        }
        if($data['tipe']=='20'){
            $data['sangu'] = $sangu->sangu_20;
            $data['simpanan'] = $sangu->ukuran_20 - $sangu->sangu_20;
        }
        if($data['tipe']=='40'){
            $data['sangu'] = $sangu->sangu_40;
            $data['simpanan'] = $sangu->ukuran_40 - $sangu->sangu_40;
        }
        if($data['tipe']=='COMBO'){
            $data['sangu'] = $sangu->sangu_combo;
            $data['simpanan'] = $sangu->ukuran_combo - $sangu->sangu_combo;
        }
        $data['tujuan'] = $sangu->tujuanInfo->nama;
        $data['tarif_id'] = $tarif->id;
        OrderTrucking::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(OrderTrucking $ordertrucking, Request $request)
    {
        $data = $request->all();
        if(!empty($data['tujuan'])){
            $sangu = SanguSopir::find($data['tujuan']);
            $tarif = TarifTrucking::where('customer_id',$data['customer_id'])->where('tujuan_id',$data['tujuan'])->where('tipe',$data['tipe'])->where('is_active',1)->first();
            if(!$tarif){
                return back()->with('danger','Master Tarif Customer belum dibuat! Harap input master tarif terlebih dahulu dan pastikan tarif berstatus Aktif!');
            }
            $data['tujuan'] = $sangu->tujuanInfo->nama;
            $data['tarif_id'] = $tarif->id;
        }
        if($request->sangu){
            $data['sangu'] = str_replace(['.',','],'',$request->sangu);
        }
        if($request->simpanan){
            $data['simpanan'] = str_replace(['.',','],'',$request->simpanan);
        }
        if($request->borongan){
            $data['borongan'] = str_replace(['.',','],'',$request->borongan);
        }
        if($request->tambah_isi){
            $data['tambah_isi'] = str_replace(['.',','],'',$request->tambah_isi);
        }
        if($request->tambah_solar){
            $data['tambah_solar'] = str_replace(['.',','],'',$request->tambah_solar);
        }
        if($request->tb_tl){
            $data['tb_tl'] = str_replace(['.',','],'',$request->tb_tl);
        }
        if($request->tally){
            $data['tally'] = str_replace(['.',','],'',$request->tally);
        }
        if($request->uang_makan){
            $data['uang_makan'] = str_replace(['.',','],'',$request->uang_makan);
        }
        if($request->kuli){
            $data['kuli'] = str_replace(['.',','],'',$request->kuli);
        }
        if(empty($data['ambil_empty_tambak_langon'])){
            $data['ambil_empty_tambak_langon'] = 0;
        }
        if(empty($data['ambil_empty_teluk_langon'])){
            $data['ambil_empty_teluk_langon'] = 0;
        }
        if(empty($data['bongkar_full_teluk_langon'])){
            $data['bongkar_full_teluk_langon'] = 0;
        }
        $ordertrucking->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(OrderTrucking $ordertrucking)
    {
        $ordertrucking->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = OrderTrucking::all()->sortByDesc('created_at');

        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                $view = view('admin.ordertrucking.form',['ordertrucking'=>$data])->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('ordertrucking.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasOrderTruckingUpdate'.$data->id.'" aria-controls="offcanvasOrderTruckingUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasOrderTruckingUpdate'.$data->id.'" aria-labelledby="offcanvasOrderTruckingUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasOrderTruckingUpdate'.$data->id.'Label">Form OrderTrucking</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('ordertrucking.update',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                    <input type="hidden" name="_method" value="PUT" />
                                    '.$view.'
                                </form>
                            </div>
                        </div>';
                return $html;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
