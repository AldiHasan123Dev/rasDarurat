<?php

namespace App\Http\Controllers;

use App\Exports\OrderTruckingExport;
use App\Models\CustomerTrucking;
use App\Models\Kendaraan;
use App\Models\Order;
use App\Models\OrderTrucking;
use App\Models\OrderBiayaTruck;
use App\Models\SanguSopir;
use App\Models\Sopir;
use App\Models\TarifTrucking;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;

class OrderTruckingController extends Controller
{
    public function index()
    {
        $kendaraan = Kendaraan::all()->where('is_active', 1)->sortBy('nopol');
        $sopir = Sopir::where('is_active', 1)->orderBy('nama', 'asc')->get();
        $tujuan = SanguSopir::join('lokasi', 'lokasi.id', '=', 'sangu_sopir.tujuan')->select('sangu_sopir.*')->where('sangu_sopir.is_active', 1)->orderBy('lokasi.nama', 'asc')->get();
        $customers = CustomerTrucking::all()->sortBy('nama');
        $now = Carbon::now()->addMonths(1)->format('Y-m-d');
        $last = Carbon::now()->subMonths(6)->format('Y-m-d');
        $nopol = Kendaraan::whereIn('milik', ['R1', 'R2'])->pluck('nopol')->toArray();
        $query = Order::query();
        $query->whereBetween('order.created_at', [$last, $now]);
        $query->where('order.trucking', 'XPDC');
        $query->whereIn('order.nopol', $nopol);
        $query->whereDoesntHave('truckingInfo');
        $query->orderBy('order.job');
        $query->orderBy('order.no_job');
        $no_order = $query->get();
        return view('admin.ordertrucking.index', compact('kendaraan', 'sopir', 'tujuan', 'customers', 'no_order'));
    }

    public function monitoring_biaya_truck(){
        return view('admin.monitoring-biaya-truck.monitoring_biaya_truck');   
    }

    public function updateSangu(Request $request)
{
    $request->validate([
        'id' => 'required|exists:order_biaya_truck,id',
    ]);

    $orderBiaya = OrderBiayaTruck::find($request->id);

   
        $orderBiaya->update([
            'tgl_sangu_kuli1' => $request->tgl_sangu_kuli1 ?? null,
            'tgl_sangu_kuli2' => $request->tgl_sangu_kuli2 ?? null,
            'tgl_sangu_kuli3' => $request->tgl_sangu_kuli3 ?? null,
            'nominal_sangu_kuli1' => $request->nominal_sangu_kuli1 ?? 0,
            'nominal_sangu_kuli2' => $request->nominal_sangu_kuli2 ?? 0,
            'nominal_sangu_kuli3' => $request->nominal_sangu_kuli3 ?? 0
        ]);

    

    return response()->json(['status' => 'success', 'message' => 'Data berhasil diperbarui.']);
}

public function updateTbTl(Request $request)
{
    $request->validate([
        'id' => 'required|exists:order_biaya_truck,id',
    ]);

    $orderBiaya = OrderBiayaTruck::find($request->id);
        $orderBiaya->update([
            'tgl_tb_tl' => $request->tgl_tb_tl ?? null,
            'nominal_tb_tl1' => $request->nominal_tb_tl1 ?? 0,
            'tgl_tb_tl1' => $request->tgl_tb_tl1,
            'nominal_tb_tl2' => $request->nominal_tb_tl2
        ]);
    return response()->json(['status' => 'success', 'message' => 'Data berhasil diperbarui.']);
}

public function updateStappel(Request $request)
{
    $request->validate([
        'id' => 'required|exists:order_biaya_truck,id',
    ]);


    $orderBiaya = OrderBiayaTruck::find($request->id);
        $orderBiaya->update([
            'tgl_stappel' => $request->tgl_stappel ?? null,
            'nominal_stappel1' => $request->nominal_stappel1 ?? 0,
        ]);
    return response()->json(['status' => 'success', 'message' => 'Data berhasil diperbarui.']);
}

    

    public function store(Request $request)
    {
        $data = $request->all();
        $sangu = SanguSopir::find($data['tujuan']);
        $tj = $sangu->tujuan;
        $tarif = TarifTrucking::where('customer_id', $data['customer_id'])->whereHas('tujuan', function ($q) use ($tj) {
            $q->where('tujuan', $tj);
        })->where('tipe', $data['tipe'])->where('is_active', 1)->first();
        if (!$tarif) {
            return back()->with('danger', 'Master Tarif Customer belum dibuat! Harap input master tarif terlebih dahulu dan pastikan tarif berstatus Aktif!');
        }
        $cek = OrderTrucking::where('seal', $request->seal)->get();
        if ($cek->count() > 1) {
            return back()->with('danger', 'Nomer Seal Sama dengan order trucking ID ' . json_encode($cek->pluck('id')));
        }
        if (request('nopol')) {
            $kendaraan = Kendaraan::where('nopol', request('nopol'))->first();
            if (!$kendaraan) {
                $kendaraan = Kendaraan::create([
                    'nopol' => request('nopol'),
                    'tipe' => request('tipe'),
                    'milik' => 'vendor',
                    'is_active' => 0
                ]);
            }
            if (request('sopir_vendor')) {
                $sopir = Sopir::where('nama', request('sopir_vendor'))->first();
                if (!$sopir) {
                    $sopir = Sopir::create([
                        'nama' => request('sopir_vendor'),
                        'milik' => 'vendor',
                        'is_active' => 0
                    ]);
                }
            }
            $data['kendaraan_id'] = $kendaraan->id;
            $data['sopir_id'] = $sopir->id;
        } else {
            if ($data['tipe'] == '20') {
                $data['borongan'] = $sangu->ukuran_20;
                $data['borongan_kuli'] = $sangu->borongan_kuli_20;
            }
            if ($data['tipe'] == '40') {
                $data['borongan'] = $sangu->ukuran_40;
                $data['borongan_kuli'] = $sangu->borongan_kuli_40;
            }
            if ($data['tipe'] == 'COMBO') {
                $data['borongan'] = $sangu->ukuran_combo;
                $data['borongan_kuli'] = $sangu->borongan_kuli_combo;
            }
        }
        $data['tujuan'] = $sangu->tujuanInfo->nama;
        $data['tarif_id'] = $tarif->id;

        $data['tb_tl'] = 0;
        if (empty($data['ambil_empty_tambak_langon'])) {
            $data['ambil_empty_tambak_langon'] = 0;
        } else {
            if ($data['tipe'] == '20' || $data['tipe'] == 'COMBO') {
                $data['tb_tl'] += 50000;
            }
            if ($data['tipe'] == '40') {
                $data['tb_tl'] += 75000;
            }
        }
        if (empty($data['ambil_empty_teluk_langon'])) {
            $data['ambil_empty_teluk_langon'] = 0;
        } else {
            if ($data['tipe'] == '20' || $data['tipe'] == 'COMBO') {
                $data['tb_tl'] += 50000;
            }
            if ($data['tipe'] == '40') {
                $data['tb_tl'] += 75000;
            }
        }
        if (empty($data['bongkar_full_teluk_langon'])) {
            $data['bongkar_full_teluk_langon'] = 0;
        } else {
            if ($data['tipe'] == '20' || $data['tipe'] == 'COMBO') {
                $data['tb_tl'] += 50000;
            }
            if ($data['tipe'] == '40') {
                $data['tb_tl'] += 75000;
            }
        }

        $data['pph_21'] = 0;
        $data['pph_23'] = 0;
        $price = $tarif->tarif;
        $data['tarif_nominal'] = $price;
        $kendaraan = Kendaraan::find($data['kendaraan_id']);
        $cus = CustomerTrucking::find($data['customer_id']);
        if ($data['customer_id'] != 2) {
            if (($kendaraan->milik == 'R2' || $kendaraan->milik == 'vendor' || $cus->r2 == 1) && $cus->pph_23 == 1) {
                $data['pph_23'] = $price * 0.02;
            }
        } else {
            if ($kendaraan->milik == 'R1') {
                $data['pph_21'] = ($price / 0.97) * 0.03;
            }
        }

        $order = OrderTrucking::create($data);
        OrderBiayaTruck::create([
            'order_trucking_id' => $order->id
        ]);

        return back()->with('success', 'Data berhasil disimpan');
    }
  public function massUpdateSJ(Request $request)
{
    $request->validate([
        'ids' => 'required|array',
        'sj_kembali' => 'required',
    ]);

    // Ambil daftar container berdasarkan id
    $containers = \App\Models\OrderTrucking::whereIn('id', $request->ids)
        ->pluck('container')
        ->filter() // hilangkan null/kosong
        ->toArray();

    // Update semua data yang dipilih
    \App\Models\OrderTrucking::whereIn('id', $request->ids)
        ->update(['sj_kembali' => $request->sj_kembali]);

    // Gabungkan dengan newline
    $containerList = implode("\n", $containers);

    // Pesan dengan newline
    $message = "Container berikut berhasil diperbarui:\n{$containerList}";

    return response()->json([
        'message' => $message
    ]);
}




    public function update(OrderTrucking $ordertrucking, Request $request)
    {
        if (request('sj_kembali')) {
            dd(request('sj_kembali'), $ordertrucking);
            $ordertrucking->update([
                'sj_kembali' => request('sj_kembali')
            ]);

            return back()->with('success', 'Data berhasil disimpan!');
        }
        $cek = OrderTrucking::where('seal', $request->seal)->get();
        if ($cek->count() > 1) {
            return response('Nomer Seal Sama dengan order trucking ID ' . json_encode($cek->pluck('id')));
        }
        // $request->validate([
        //     // 'container' => 'nullable|unique:order_trucking,container,'.$ordertrucking->id,
        //     'seal' => 'nullable|unique:order_trucking,seal,'.$ordertrucking->id
        // ]);

        $data = $request->all();
        $kuli = str_replace(['.', ','], '', $request->kuli);
        $sangu = str_replace(['.', ','], '', $request->sangu);
        $stappel = str_replace(['.', ','], '', $request->stappel);
        $tbtl = 0;
        if (!empty($data['ambil_empty_tambak_langon'])) {
            if ($data['ambil_empty_tambak_langon'] == "true") {
                if ($ordertrucking->tipe == '20' || $ordertrucking->tipe == 'COMBO') {
                    $tbtl += 50000;
                }
                if ($ordertrucking->tipe == '40') {
                    $tbtl += 75000;
                }
            }
        } 
        $data['tipe'] = $ordertrucking->tipe;
        $data['customer_id'] = $ordertrucking->customer_id;
        if (!empty($data['tujuan'])) {
            $sangu = SanguSopir::find($data['tujuan']);
            $milik = $ordertrucking->kendaraan->milik;
            $tj = $sangu->tujuan;
            if ($sangu->tujuanInfo->nama != $ordertrucking->tujuan && $milik != 'vendor') {
                $tarif = TarifTrucking::where('customer_id', $data['customer_id'])->whereHas('tujuan', function ($q) use ($tj) {
                    $q->where('tujuan', $tj);
                })->where('tipe', $data['tipe'])->where('is_active', 1)->first();
                if (!$tarif) {
                    return response('Master Tarif Customer belum dibuat! Harap input master tarif terlebih dahulu dan pastikan tarif berstatus Aktif!');
                }
                if ($ordertrucking->tipe == 20) {
                    if ($sangu->ukuran_20 != $ordertrucking->borongan) {
                        return response('Tidak bisa update data karena borongan sopir tidak sama! ' . $sangu->ukuran_20 . ' != ' . $ordertrucking->ukuran_20);
                    }
                }
                if ($ordertrucking->tipe == 40) {
                    if ($sangu->ukuran_40 != $ordertrucking->borongan) {
                        return response('Tidak bisa update data karena borongan sopir tidak sama! ' . $sangu->ukuran_40 . ' != ' . $ordertrucking->ukuran_40);
                    }
                }
                if ($ordertrucking->tipe == 'COMBO') {
                    if ($sangu->ukuran_combo != $ordertrucking->borongan) {
                        return response('Tidak bisa update data karena borongan sopir tidak sama! ' . $sangu->ukuran_combo . ' != ' . $ordertrucking->ukuran_combo);
                    }
                }
                $data['tujuan'] = $sangu->tujuanInfo->nama;
                $data['tarif_id'] = $tarif->id;
                $data['tarif_nominal'] = $tarif->tarif;
            }
        }
        if ($request->borongan) {
            $data['borongan'] = str_replace(['.', ','], '', $request->borongan);
        }
        if ($request->sangu) {
            $data['sangu'] = str_replace(['.', ','], '', $request->sangu);
            $data['simpanan'] = $data['borongan'] - $data['sangu'];
        }
        if ($request->borongan_kuli) {
            $data['borongan_kuli'] = str_replace(['.', ','], '', $request->borongan_kuli);
        }
        if ($request->kuli) {
            $data['kuli'] = str_replace(['.', ','], '', $request->kuli);
            $data['simpanan_kuli'] = $data['borongan_kuli'] - $data['kuli'];
            if ($data['simpanan_kuli'] <= 0) {
                $data['simpanan_kuli'] = 0;
            }
        }
        // if($request->simpanan){
        //     $data['simpanan'] = str_replace(['.',','],'',$request->simpanan);
        // }
        if ($request->tambah_isi) {
            $data['tambah_isi'] = str_replace(['.', ','], '', $request->tambah_isi);
        }
        if ($request->tambah_solar) {
            $data['tambah_solar'] = str_replace(['.', ','], '', $request->tambah_solar);
        }
        if ($request->tb_tl) {
            $data['tb_tl'] = str_replace(['.', ','], '', $request->tb_tl);
        }
        if ($request->tally) {
            $data['tally'] = str_replace(['.', ','], '', $request->tally);
        }
        if ($request->uang_makan) {
            $data['uang_makan'] = str_replace(['.', ','], '', $request->uang_makan);
        }
        if ($request->kuli) {
            $data['kuli'] = str_replace(['.', ','], '', $request->kuli);
        }
        if ($request->op) {
            $data['op'] = str_replace(['.', ','], '', $request->op);
        }
        if ($request->cleaning) {
            $data['cleaning'] = str_replace(['.', ','], '', $request->cleaning);
        }
        if ($request->stappel) {
            $data['stappel'] = str_replace(['.', ','], '', $request->stappel);
        }
        if ($request->lain_lain) {
            $data['lain_lain'] = str_replace(['.', ','], '', $request->lain_lain);
        }
        if ($request->lain) {
            $data['lain'] = str_replace(['.', ','], '', $request->lain);
        }

        $data['tb_tl'] = 0;
        if (!empty($data['ambil_empty_tambak_langon'])) {
            if ($data['ambil_empty_tambak_langon'] == "true") {
                if ($ordertrucking->tipe == '20' || $ordertrucking->tipe == 'COMBO') {
                    $data['tb_tl'] += 50000;
                }
                if ($ordertrucking->tipe == '40') {
                    $data['tb_tl'] += 75000;
                }
                $data['ambil_empty_tambak_langon'] = 1;
            } else {
                $data['ambil_empty_tambak_langon'] = 0;
            }
        }
        if (!empty($data['is_seal'])) {
            if ($data['is_seal'] == "true") {
                $data['is_seal'] = 1;
            } else {
                $data['is_seal'] = 0;
            }
        }
        if (!empty($data['ambil_empty_teluk_langon'])) {
            if ($data['ambil_empty_teluk_langon'] == "true") {
                if ($ordertrucking->tipe == '20' || $ordertrucking->tipe == 'COMBO') {
                    $data['tb_tl'] += 50000;
                }
                if ($ordertrucking->tipe == '40') {
                    $data['tb_tl'] += 75000;
                }
                $data['ambil_empty_teluk_langon'] = 1;
            } else {
                $data['ambil_empty_teluk_langon'] = 0;
            }
        }
        if (!empty($data['bongkar_full_teluk_langon'])) {
            if ($data['bongkar_full_teluk_langon'] == "true") {
                if ($ordertrucking->tipe == '20' || $ordertrucking->tipe == 'COMBO') {
                    $data['tb_tl'] += 50000;
                }
                if ($ordertrucking->tipe == '40') {
                    $data['tb_tl'] += 75000;
                }
                $data['bongkar_full_teluk_langon'] = 1;
            } else {
                $data['bongkar_full_teluk_langon'] = 0;
            }
        }

        $data['order_id'] = null;
        $orderE = Order::where('container', $data['container'])->where('seal', $data['seal'])->first();
        if ($orderE) {
            $data['order_id'] = $orderE->id;
        }

        $ordertrucking->update($data);
        $order = OrderTrucking::find($ordertrucking->id);
        $totalan = $order->simpanan + $order->simpanan_kuli + $order->tb_tl + $order->lain_lain + $order->stappel;
        $margin = $order->tarif->tarif - $order->borongan - $order->borongan_kuli - $order->uang_makan - $order->op - $order->cleaning;
        $order->update([
            'total_sopir' => $totalan,
            'margin' => $margin
        ]);
        
        // $orderBiaya = OrderBiayaTruck::where('order_trucking_id', $order->id)->get();
        // if ( $order->kuli || $order->tb_tl || $order->stappel){
        //     if ($orderBiaya->isEmpty()) {
        //         OrderBiayaTruck::create([
        //             'order_trucking_id' => $order->id,
        //             'nominal_sangu' => $order->sangu,
        //             'nominal_sangu_kuli' => $order->kuli,
        //             'nominal_tb_tl' => $order->tb_tl,
        //             'nominal_stappel' => $order->stappel,
        //         ]);
        //     } else  {
        //        $orderBiayaTruck = OrderBiayaTruck::where('order_trucking_id',$order->id)->first();
        //        $orderBiayaTruck1 =  OrderBiayaTruck::find($orderBiayaTruck->id);
        //        if ($cekKuli) {
        //             $orderBiayaTruck1->update([
        //                     'nominal_sangu_kuli' => $order->kuli
        //                 ]);                    
        //         } 
        //     } // Jika tidak ada perubahan, tidak melakukan update sangu & kuli
        //     if ($cekStappel) {
        //         $orderBiayaTruck1->update([
        //         'nominal_stappel' => $order->stappel
        //         ]);
        //     }
        // if ($cekTbtl) {
        //     if ($orderBiayaTruck1->nominal_tb_tl1 == 0){
        //         $orderBiayaTruck1->update([
        //         'nominal_tb_tl' => $order->tb_tl
        //         ]);
        //     }
        // }
        // }
        return response('Data berhasil di update!');
        return back()->with('success', 'Data berhasil diupdate');
    }

    public function destroy(OrderTrucking $ordertrucking)
    {
        $ordertrucking->delete();

        return back()->with('success', 'Data berhasil dihapus');
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
                $view = view('admin.ordertrucking.form', ['ordertrucking' => $data])->render();
                $html = '<div class="d-flex gap-1">
                            <form action="' . route('ordertrucking.destroy', $data) . '" method="post">
                                <input type="hidden" name="_token" value="' . csrf_token() . '" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasOrderTruckingUpdate' . $data->id . '" aria-controls="offcanvasOrderTruckingUpdate' . $data->id . '"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasOrderTruckingUpdate' . $data->id . '" aria-labelledby="offcanvasOrderTruckingUpdate' . $data->id . 'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasOrderTruckingUpdate' . $data->id . 'Label">Form OrderTrucking</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="' . route('ordertrucking.update', $data) . '" method="post">
                                <input type="hidden" name="_token" value="' . csrf_token() . '" />
                                    <input type="hidden" name="_method" value="PUT" />
                                    ' . $view . '
                                </form>
                            </div>
                        </div>';
                return $html;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
