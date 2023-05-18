<?php

namespace App\Http\Controllers;

use App\Exports\OrderTruckingExport;
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
use Maatwebsite\Excel\Facades\Excel;
use Yajra\Datatables\Datatables;

class OrderTruckingController extends Controller
{
    public function index()
    {
        $data = OrderTrucking::all()->sortByDesc('tgl_muat');
        $data = OrderTruckingResource::collection($data);
        $kendaraan = Kendaraan::all()->where('is_active',1)->sortBy('nopol');
        $sopir = Sopir::where('is_active',1)->orderBy('nama','asc')->get();
        $tujuan = SanguSopir::join('lokasi','lokasi.id','=','sangu_sopir.tujuan')->select('sangu_sopir.*')->orderBy('lokasi.nama','asc')->get();
        $customers = CustomerTrucking::all()->sortBy('nama');
        $update = OrderTrucking::whereNull('order_id')->get();
        foreach ($update as $item ) {
            $order = Order::where('container',$item->container)->where('seal',$item->seal)->first();
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
        if(request('nopol')){
            $kendaraan = Kendaraan::where('nopol',request('nopol'))->first();
            if(!$kendaraan){
                $kendaraan = Kendaraan::create([
                    'nopol' => request('nopol'),
                    'tipe' => request('tipe'),
                    'milik' => 'vendor',
                    'is_active' => 0
                ]);
            }
            if(request('sopir_vendor')){
                $sopir = Sopir::where('nama',request('sopir_vendor'))->first();
                if(!$sopir){
                    $sopir = Sopir::create([
                        'nama' => request('sopir_vendor'),
                        'milik' => 'vendor',
                        'is_active' => 0
                    ]);
                }
            }
            $data['kendaraan_id'] = $kendaraan->id;
            $data['sopir_id'] = $sopir->id;
        }else{
            if($data['tipe']=='20'){
                $data['borongan'] = $sangu->ukuran_20;
                $data['borongan_kuli'] = $sangu->borongan_kuli_20;
            }
            if($data['tipe']=='40'){
                $data['borongan'] = $sangu->ukuran_40;
                $data['borongan_kuli'] = $sangu->borongan_kuli_40;
            }
            if($data['tipe']=='COMBO'){
                $data['borongan'] = $sangu->ukuran_combo;
                $data['borongan_kuli'] = $sangu->borongan_kuli_combo;
            }
        }
        $data['tujuan'] = $sangu->tujuanInfo->nama;
        $data['tarif_id'] = $tarif->id;

        $data['tb_tl'] = 0;
        if(empty($data['ambil_empty_tambak_langon'])){
            $data['ambil_empty_tambak_langon'] = 0;
        }else{
            if($data['tipe']=='20'||$data['tipe']=='COMBO'){
                $data['tb_tl'] += 50000;
            }
            if($data['tipe']=='40'){
                $data['tb_tl'] += 75000;
            }
        }
        if(empty($data['ambil_empty_teluk_langon'])){
            $data['ambil_empty_teluk_langon'] = 0;
        }else{
            if($data['tipe']=='20'||$data['tipe']=='COMBO'){
                $data['tb_tl'] += 50000;
            }
            if($data['tipe']=='40'){
                $data['tb_tl'] += 75000;
            }
        }
        if(empty($data['bongkar_full_teluk_langon'])){
            $data['bongkar_full_teluk_langon'] = 0;
        }else{
            if($data['tipe']=='20'||$data['tipe']=='COMBO'){
                $data['tb_tl'] += 50000;
            }
            if($data['tipe']=='40'){
                $data['tb_tl'] += 75000;
            }
        }

        $data['pph_21'] = 0;
        $data['pph_23'] = 0;
        $price = $tarif->tarif;
        $kendaraan = Kendaraan::find($data['kendaraan_id']);
        $cus = CustomerTrucking::find($data['customer_id']);
        if($data['customer_id']!=2){
            if (($kendaraan->milik=='R2'||$kendaraan->milik=='vendor')&&$cus->pph_23==1) {
                $data['pph_23'] = $price * 0.02;
            }
        }else{
            if ($kendaraan->milik=='R1') {
                $data['pph_21'] = ($price / 0.97) * 0.03;
            }
        }

        OrderTrucking::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(OrderTrucking $ordertrucking, Request $request)
    {
        $request->validate([
            'container' => 'nullable|unique:order_trucking,container,'.$ordertrucking->id,
            'seal' => 'nullable|unique:order_trucking,seal,'.$ordertrucking->id
        ]);

        $data = $request->all();
        $data['tipe'] = $ordertrucking->tipe;
        if(!empty($data['tujuan'])){
            $sangu = SanguSopir::find($data['tujuan']);
            if($sangu->tujuanInfo->nama!=$ordertrucking->tujuan){
                $tarif = TarifTrucking::where('customer_id',$data['customer_id'])->where('tujuan_id',$data['tujuan'])->where('tipe',$data['tipe'])->where('is_active',1)->first();
                if(!$tarif){
                    return back()->with('danger','Master Tarif Customer belum dibuat! Harap input master tarif terlebih dahulu dan pastikan tarif berstatus Aktif!');
                }
                if($ordertrucking->tipe==20){
                    if($sangu->ukuran_20!=$ordertrucking->borongan){
                        return back()->with('danger','Tidak bisa update data karena borongan sopir tidak sama! '.$sangu->ukuran_20.' != '.$ordertrucking->ukuran_20);
                    }
                }
                if($ordertrucking->tipe==40){
                    if($sangu->ukuran_40!=$ordertrucking->borongan){
                        return back()->with('danger','Tidak bisa update data karena borongan sopir tidak sama! '.$sangu->ukuran_40.' != '.$ordertrucking->ukuran_40);
                    }
                }
                if($ordertrucking->tipe=='COMBO'){
                    if($sangu->ukuran_combo!=$ordertrucking->borongan){
                        return back()->with('danger','Tidak bisa update data karena borongan sopir tidak sama! '.$sangu->ukuran_combo.' != '.$ordertrucking->ukuran_combo);
                    }
                }
                $data['tujuan'] = $sangu->tujuanInfo->nama;
                $data['tarif_id'] = $tarif->id;
            }
        }
        if($request->borongan){
            $data['borongan'] = str_replace(['.',','],'',$request->borongan);
        }
        if($request->sangu){
            $data['sangu'] = str_replace(['.',','],'',$request->sangu);
            $data['simpanan'] = $data['borongan'] - $data['sangu'];
        }
        if($request->borongan_kuli){
            $data['borongan_kuli'] = str_replace(['.',','],'',$request->borongan_kuli);
        }
        if($request->kuli){
            $data['kuli'] = str_replace(['.',','],'',$request->kuli);
            $data['simpanan_kuli'] = $data['borongan_kuli'] - $data['kuli'];
            if($data['simpanan_kuli']<=0){
                $data['simpanan_kuli'] = 0;
            }
        }
        // if($request->simpanan){
        //     $data['simpanan'] = str_replace(['.',','],'',$request->simpanan);
        // }
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
        if($request->op){
            $data['op'] = str_replace(['.',','],'',$request->op);
        }
        if($request->cleaning){
            $data['cleaning'] = str_replace(['.',','],'',$request->cleaning);
        }
        if($request->stappel){
            $data['stappel'] = str_replace(['.',','],'',$request->stappel);
        }
        if($request->lain_lain){
            $data['lain_lain'] = str_replace(['.',','],'',$request->lain_lain);
        }

        $data['tb_tl'] = 0;
        if(empty($data['ambil_empty_tambak_langon'])){
            $data['ambil_empty_tambak_langon'] = 0;
        }else{
            if($ordertrucking->tipe=='20'||$ordertrucking->tipe=='COMBO'){
                $data['tb_tl'] += 50000;
            }
            if($ordertrucking->tipe=='40'){
                $data['tb_tl'] += 75000;
            }
        }
        if(empty($data['ambil_empty_teluk_langon'])){
            $data['ambil_empty_teluk_langon'] = 0;
        }else{
            if($ordertrucking->tipe=='20'||$ordertrucking->tipe=='COMBO'){
                $data['tb_tl'] += 50000;
            }
            if($ordertrucking->tipe=='40'){
                $data['tb_tl'] += 75000;
            }
        }
        if(empty($data['bongkar_full_teluk_langon'])){
            $data['bongkar_full_teluk_langon'] = 0;
        }else{
            if($ordertrucking->tipe=='20'||$ordertrucking->tipe=='COMBO'){
                $data['tb_tl'] += 50000;
            }
            if($ordertrucking->tipe=='40'){
                $data['tb_tl'] += 75000;
            }
        }

        $ordertrucking->update($data);
        $order = OrderTrucking::find($ordertrucking->id);
        $totalan = $order->simpanan + $order->simpanan_kuli + $order->tb_tl + $order->lain_lain + $order->stappel;
        $margin = $order->tarif->tarif - $order->borongan - $order->borongan_kuli - $order->uang_makan - $order->op - $order->cleaning;
        $order->update([
            'total_sopir' => $totalan,
            'margin' => $margin
        ]);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(OrderTrucking $ordertrucking)
    {
        $ordertrucking->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function export()
    {
        return Excel::download(new OrderTruckingExport(), 'laporan_order_trucking.xlsx');
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
