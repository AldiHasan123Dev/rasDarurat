<?php

namespace App\Http\Controllers;

use App\Models\NSFP;
use App\Models\Order;
use App\Models\Tagihan;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class NSFPController extends Controller
{
    public function index()
    {
        return view('admin.nsfp.index');
    }

    public function cancel()
    {
        return view('admin.nsfp.tarik');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        NSFP::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(NSFP $nsfp, Request $request)
    {
        $data = $request->all();
        $nsfp->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(NSFP $nsfp)
    {
        $nsfp->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function revisi(Request $request)
    {
        $nsfp = NSFP::find($request->id);
        $no = substr($nsfp->nomor,3,20);
        $new = '051'.$no;

        $order = Order::where('invoice',$nsfp->invoice)->first();
        $type = strtoupper(strtolower($order->tarif->shipmentInfo->nama[0]));
        if ($type=='F') {
            $invoice = $this->FCL($order);
        }else{
            $invoice = $this->LCL($order);
        }

        if ($order->tarif->customer->all_in==1) {
            $sub_total = $invoice['sub_total'];
            $ppn = $sub_total * 0.011;
            $asuransi = $invoice['asuransi_total'];
            $total = $sub_total + $ppn + $asuransi;
            Transaksi::where('invoice',$nsfp->invoice)->update([
                'nsfp' => $new,
                'sub_total' => $sub_total,
                'ppn' => $ppn,
                'asuransi' => $asuransi,
                'total' => $total
            ]);
        }else{
            Transaksi::where('invoice',$nsfp->invoice)->update([
                'nsfp' => $new,
                'sub_total' => $invoice['sub_total'],
                'ppn' => $invoice['ppn'],
                'asuransi' => $invoice['asuransi_total'],
                'total' => $invoice['total']
            ]);
        }

        Order::where('nsfp',$nsfp->nomor)->update([
            'nsfp' => $new
        ]);
        $nsfp->update([
            'nomor' => $new,
            'status' => 'revisi'
        ]);

        if ($nsfp->status=='revisi') {
            return back()->with('success','Faktur berhasil direvisi!');
        }
        return back()->with('success','Revisi Faktur Berhasil di buat!');
    }

    public function revisi_non_faktur(Request $request)
    {
        $nsfp = NSFP::find($request->id);
        $order = Order::where('invoice',$nsfp->invoice)->first();
        $type = strtoupper(strtolower($order->tarif->shipmentInfo->nama[0]));
        if ($type=='F') {
            $invoice = $this->FCL($order);
        }else{
            $invoice = $this->LCL($order);
        }

        if ($order->tarif->customer->all_in==1) {
            $sub_total = $invoice['sub_total'];
            $ppn = $sub_total * 0.011;
            $asuransi = $invoice['asuransi_total'];
            $total = $sub_total + $ppn + $asuransi;
            Transaksi::where('invoice',$nsfp->invoice)->update([
                'sub_total' => $sub_total,
                'ppn' => $ppn,
                'asuransi' => $asuransi,
                'total' => $total
            ]);
        }else{
            Transaksi::where('invoice',$nsfp->invoice)->update([
                'sub_total' => $invoice['sub_total'],
                'ppn' => $invoice['ppn'],
                'asuransi' => $invoice['asuransi_total'],
                'total' => $invoice['total']
            ]);
        }

        return back()->with('success','Revisi Faktur Berhasil di simpan!');
    }

    public function tarik(Request $request)
    {
        $nsfp = NSFP::find($request->id);
        $nsfp->update([
            'status' => 'tarik'
        ]);

        return back()->with('success','Faktur Berhasil di tarik!');
    }

    public function deleteAll()
    {
        NSFP::whereNull('invoice')->delete();
        return back()->with('success','NSFP berhasil dihapus!');
    }

    public function datatable()
    {
        $data = NSFP::query();
        if(request('filter')=='available'){
            $data->whereNull('invoice');
        }
        if(request('filter')=='tarik'){
            $data->where('status','tarik');
        }
        if(request('filter')=='invoice'){
            // $data->where('status','!=','tarik');
            // $data->orWhereNull('status');
            $data->whereNotNull('invoice');
        }

        return Datatables::of($data)
            ->setRowClass(function ($data) {
                $class = '';
                if($data->status=='tarik'){
                    $class = 'bg-light-danger';
                }
                if($data->status=='revisi'){
                    $class = 'bg-light-primary';
                }

                return $class;
            })
            ->addIndexColumn()
            ->order(function ($data) {
                $data->orderBy('available','desc');
                $data->orderBy('nomor','asc');
            })
            ->addColumn('available',function($data){
                return $data->available==1?'IYA':'TIDAK';
            })
            ->addColumn('action', function ($data) {
                if ($data->available) {
                    $view = view('admin.nsfp.form',['nsfp'=>$data])->render();
                    $html = '<div class="d-flex gap-1">
                                <form action="'.route('nsfp.destroy',$data).'" method="post">
                                    <input type="hidden" name="_token" value="'.csrf_token().'" />
                                    <input type="hidden" name="_method" value="delete" />
                                    <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                                </form>
                                <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNSFPUpdate'.$data->id.'" aria-controls="offcanvasNSFPUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                            </div>

                            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNSFPUpdate'.$data->id.'" aria-labelledby="offcanvasNSFPUpdate'.$data->id.'Label">
                                <div class="offcanvas-header">
                                    <h5 class="offcanvas-title" id="offcanvasNSFPUpdate'.$data->id.'Label">Form NSFP</h5>
                                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                </div>
                                <div class="offcanvas-body">
                                    <form action="'.route('nsfp.update',$data).'" method="post">
                                    <input type="hidden" name="_token" value="'.csrf_token().'" />
                                        <input type="hidden" name="_method" value="PUT" />
                                        '.$view.'
                                    </form>
                                </div>
                            </div>';
                    return $html;
                }
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function FCL(Order $order)
    {
        $orders = Order::where('job',$order->job)->get();
        $cas = Tagihan::whereIn('order_id',$orders->pluck('id')->toArray())->get();
        $asuransi = 0;
        $admin = 0;
        $doc = 0;
        $doc_count = 0;
        $doc_total = 0;
        $sub_total = 0;
        $validate = array();
        $items = array();
        $asuransi_name = '';
        foreach ($orders->groupBy('tarif_id') as $idx => $tar ) {
            $koli = 0;
            if ($tar->first()->tarif->kondisi==1||$tar->first()->tarif->kondisi==6) {
                $doc = 500000;
                $doc_total += $tar->count() * 500000;
                $doc_count += $tar->count();
            }
            foreach ($tar as $or ) {
                $koli += $or->bttb->sum('qty');
                if (!is_null($or->asuransi_id)) {
                    $asuransi += ($or->asuransiInfo->rate/100) * $or->pertanggungan;
                    $asuransi_name = $or->asuransiInfo->nama;
                    $admin += $or->asuransiInfo->admin;
                }
                if($or->asuransi=='ADA EXC'){
                    if(is_null($or->asuransi_id)){
                        array_push($validate,'Asuransi Job '.$or->job.'-'.sprintf('%02d',$or->no_job).' belum diinput!');
                    }
                }
                if(is_null($or->tarif->customer->nik)){
                    array_push($validate,'Customer '.$or->tarif->customer->nama.' NIK Belum diinput!');
                }
                if(is_null($or->tarif->customer->npwp)){
                    array_push($validate,'Customer '.$or->tarif->customer->nama.' NPWP Belum diinput!');
                }
            }
            $items[$idx]['keterangan'] = $tar->first()->tarif->kondisiInfo->nama.', '.$tar->first()->tarif->dari_lokasi->nama.' - '.$tar->first()->tarif->tujuan_lokasi->nama;
            $items[$idx]['koli'] = $koli;
            $items[$idx]['jumlah'] = $tar->count();
            $items[$idx]['jumlah_cont'] = $tar->count();
            $items[$idx]['si'] = 'Cont '.$tar->first()->tarif->shipmentInfo->nama;
            $items[$idx]['tarif'] = $tar->first()->tarif->tarif - $doc;
            $items[$idx]['sub_total'] = ($tar->first()->tarif->tarif - $doc) * $tar->count();
            $sub_total += ($tar->first()->tarif->tarif - $doc) * $tar->count();
        }
        $sub_total += $doc_total;
        $asuransi += $admin;
        if ($asuransi>0&&$order->tipe_asuransi=='job') {
            $asuransi = (($order->asuransiInfo->rate/100) * $order->pertanggungan + $order->asuransiInfo->admin);
        }
        if($doc_total>0){
            $pph = $doc_total * 0.02;
        }else{
            $pph = $sub_total * 0.02;
        }
        $ppn = $sub_total * 0.011;
        $total = $sub_total + $asuransi + $ppn + $cas->sum('jumlah');
        return [
            'items' => $items,
            'sub_total' => $sub_total,
            'doc_count' => $doc_count,
            'doc_total' => $doc_total,
            'ppn' => $ppn,
            'pph' => $pph,
            'admin' => $admin,
            'total' => $total,
            'asuransi' => $asuransi_name,
            'asuransi_total' => $asuransi,
            'validate' => $validate,
        ];
    }

    public function LCL(Order $order)
    {
        $orders = Order::where('job',$order->job)->get();
        $cas = Tagihan::whereIn('order_id',$orders->pluck('id')->toArray())->get();
        $asuransi = 0;
        $admin = 0;
        $doc = 0;
        $doc_count = 0;
        $doc_total = 0;
        $sub_total = 0;
        $validate = array();
        $items = array();
        $asuransi_name = '';
        foreach ($orders->groupBy('tarif_id') as $idx => $tar ) {
            $koli = 0;
            $jumlah = 0;
            if ($tar->first()->tarif->kondisi==1||$tar->first()->tarif->kondisi==6) {
                $doc = $tar->count() * 500000;
                $doc_total += $tar->count() * 500000;
                $doc_count += $tar->count();
            }
            foreach ($tar as $or ) {
                $koli += $or->bttb->sum('qty');
                $jumlah += $or->bttb->sum('vol');
                if (!is_null($or->asuransi_id)) {
                    $asuransi += ($or->asuransiInfo->rate/100) * $or->pertanggungan;
                    $asuransi_name = $or->asuransiInfo->nama;
                    $admin += $or->asuransiInfo->admin;
                }
                if($or->asuransi=='ADA EXC'){
                    if(is_null($or->asuransi_id)){
                        array_push($validate,'Asuransi Job '.$or->job.'-'.sprintf('%02d',$or->no_job).' belum diinput!');
                    }
                }
                if(is_null($or->tarif->customer->nik)){
                    array_push($validate,'Customer '.$or->tarif->customer->nama.' NIK Belum diinput!');
                }
                if(is_null($or->tarif->customer->npwp)){
                    array_push($validate,'Customer '.$or->tarif->customer->nama.' NPWP Belum diinput!');
                }
            }
            $items[$idx]['keterangan'] = $tar->first()->tarif->kondisiInfo->nama.', '.$tar->first()->tarif->dari_lokasi->nama.' - '.$tar->first()->tarif->tujuan_lokasi->nama;
            $items[$idx]['koli'] = $koli;
            $items[$idx]['jumlah'] = round($jumlah,2);
            $items[$idx]['jumlah_cont'] = $tar->count();
            $items[$idx]['si'] = 'M3 '.$tar->first()->tarif->shipmentInfo->nama;
            $items[$idx]['tarif'] = $tar->first()->tarif->tarif;
            $items[$idx]['sub_total'] = $tar->first()->tarif->tarif * round($jumlah,2);
            $sub_total += $tar->first()->tarif->tarif * round($jumlah,2)    ;
        }
        $sub_total += $doc_total;
        $asuransi += $admin;
        if ($asuransi>0&&$order->tipe_asuransi=='job') {
            $asuransi = (($order->asuransiInfo->rate/100) * $order->pertanggungan + $order->asuransiInfo->admin);
        }
        if($doc_total>0){
            $pph = $doc_total * 0.02;
        }else{
            $pph = $sub_total * 0.02;
        }
        $ppn = $sub_total * 0.011;
        $total = $sub_total + $asuransi + $ppn + $cas->sum('jumlah');
        return [
            'items' => $items,
            'sub_total' => $sub_total,
            'doc_count' => $doc_count,
            'doc_total' => $doc_total,
            'ppn' => $ppn,
            'pph' => $pph,
            'admin' => $admin,
            'total' => $total,
            'asuransi' => $asuransi_name,
            'asuransi_total' => $asuransi,
            'validate' => $validate,
        ];
    }
}
