<?php

namespace App\Http\Controllers;

use App\Models\HutangAgen;
use App\Models\Jurnal;
use App\Models\Order;
use App\Models\TagihanAgen;
use App\Models\TarifAgen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class HutangAgenController extends Controller
{
    public function index()
    {
        $data = Order::whereHas('agent')->whereNull('invoice_agen')->whereYear('created_at',2024)->get()->groupBy('agen_id');
        return view('admin.hutangagen.index', compact('data'));
    }

    public function list()
    {
        $data = HutangAgen::all()->groupBy('jurnal');
        // dd($data);
        return view('admin.hutangagen.list', compact('data'));
    }

    public function draf(Request $request)
    {
        $ids = $request->order_id;
        $orders = Order::whereIn('id',$ids)->get()->groupBy('agen_id');
        if(count($ids)==0){
            return back()->with('danger','Harus centang salah satu!');
        }
        if($orders->count()>1){
            return back()->with('danger','Harus centang pada agen yang sama!');
        }

        $orders = Order::whereIn('id',$ids)->get();
        $jobs = $orders->groupBy('job');
        $tarif = TarifAgen::where('agen_id', $orders->first()->agen_id)->where('is_active',1)->orderBy('created_at')->get();
        $count = Order::whereIn('id',$ids)->count();
        return view('admin.hutangagen.draf', compact('orders','tarif','ids','count','jobs'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $no = Jurnal::where('tipe','TEST')->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->max('no') + 1;
        $nomor = 'HUTAGEN/'.sprintf('%02d',date('m')).'-'.sprintf('%03d',$no).'/'.date('y');
        for ($i=0; $i < count($request->order_id); $i++) {
            $order = Order::find($request->order_id[$i]);
            $cek = Jurnal::where('order_id',$request->order_id[$i])->where('coa_id',93)->where('debit','>',0)->count();
            $jurnal = array();
            $jurnal['order_id'] = $request->order_id[$i];
            $jurnal['nomor'] = $nomor;
            $jurnal['no'] = $no;
            $jurnal['nama'] = 'Biaya Dooring '.$order->tarif->customer->nama.' '.$order->tarif->shipmentInfo->nama;
            $jurnal['container'] = $order->container;
            $jurnal['invoice'] = $request->invoice;
            $jurnal['tipe'] = 'TEST';
            if($cek>0){
                $jurnal['coa_id'] = 134;
                $jurnal['debit'] = $request->tarif[$i];
                $jurnal['credit'] = 0;
                Jurnal::create($jurnal);
                $jurnal['coa_id'] = 63;
                $jurnal['credit'] = $request->tarif[$i];
                $jurnal['debit'] = 0;
                Jurnal::create($jurnal);
            }else{
                $jurnal['coa_id'] = 31;
                $jurnal['debit'] = $request->tarif[$i];
                $jurnal['credit'] = 0;
                Jurnal::create($jurnal);
                $jurnal['coa_id'] = 63;
                $jurnal['credit'] = $request->tarif[$i];
                $jurnal['debit'] = 0;
                Jurnal::create($jurnal);
            }
            Order::find($request->order_id[$i])->update(['invoice_agen' => $request->invoice]);
            HutangAgen::create([
                'order_id' => $request->order_id[$i],
                'tarif' => $request->tarif[$i],
                'ppn' => $request->ppn[$i],
                'pph' => $request->pph[$i],
                'invoice' => $request->invoice,
                'jurnal' => $nomor,
                'tanggal' => $request->tanggal
            ]);
        }
        for ($i=0; $i < count($data['tagihan_order_id']); $i++) {
            if($data['nama'][$i]!=null && $data['jumlah'][$i]!=null && $data['tagihan_order_id'][$i]!=null){
                $tipe = $data['tagihan_order_id'][$i];
                if(substr($tipe,0,3)=='job'){
                    $job = str_replace('job-','',$tipe);
                    $jobs = Order::where('job',$job)->get();
                    $amount = (int)$data['jumlah'][$i] / $jobs->count();
                    $price = (int)((int)$data['jumlah'][$i] / $jobs->count());
                    $selisih = (int)$data['jumlah'][$i] - ($price * $jobs->count());
                    foreach ($jobs as $key => $order) {
                        if ($key==0) {
                            $amount = (int)((int)$data['jumlah'][$i] / $jobs->count()) + $selisih;
                        }else{
                            $amount = $price;
                        }
                        if($data['beban'][$i]=='ras'){
                            $cek = Jurnal::where('order_id',$order->id)->where('coa_id',93)->where('debit','>',0)->count();
                            if($cek > 0){
                                Jurnal::create([
                                    'order_id' => $order->id,
                                    'nomor' => $nomor,
                                    'no' => $no,
                                    'nama' => $data['nama'][$i],
                                    'container' => $order->container,
                                    'invoice' => $request->invoice,
                                    'tipe' => 'TEST',
                                    'coa_id' => 134,
                                    'debit' => $amount,
                                    'credit' => 0
                                ]);
                            }else{
                                Jurnal::create([
                                    'order_id' => $order->id,
                                    'nomor' => $nomor,
                                    'no' => $no,
                                    'nama' => $data['nama'][$i],
                                    'container' => $order->container,
                                    'invoice' => $request->invoice,
                                    'tipe' => 'TEST',
                                    'coa_id' => 31,
                                    'debit' => $amount,
                                    'credit' => 0
                                ]);
                            }
                            Jurnal::create([
                                'order_id' => $order->id,
                                'nomor' => $nomor,
                                'no' => $no,
                                'nama' => $data['nama'][$i],
                                'container' => $order->container,
                                'invoice' => $request->invoice,
                                'tipe' => 'TEST',
                                'coa_id' => 63,
                                'credit' => $amount,
                                'debit' => 0
                            ]);
                        }else{
                            Jurnal::create([
                                'order_id' => $order->id,
                                'nomor' => $nomor,
                                'no' => $no,
                                'nama' => $data['nama'][$i],
                                'container' => $order->container,
                                'invoice' => $request->invoice,
                                'tipe' => 'TEST',
                                'coa_id' => 63,
                                'debit' => $amount,
                                'credit' => 0
                            ]);
                            Jurnal::create([
                                'order_id' => $order->id,
                                'nomor' => $nomor,
                                'no' => $no,
                                'nama' => $data['nama'][$i],
                                'container' => $order->container,
                                'invoice' => $request->invoice,
                                'tipe' => 'TEST',
                                'coa_id' => 28,
                                'credit' => $amount,
                                'debit' => 0
                            ]);
                        }
                    }
                    TagihanAgen::create([
                        'invoice' => $request->invoice,
                        'tipe' => 'group',
                        'order_id' => $order->id,
                        'nama' => $data['nama'][$i],
                        'jumlah' => $data['jumlah'][$i],
                        'beban' => $data['beban'][$i]
                    ]);
                }else{
                    $order = Order::find($data['tagihan_order_id'][$i]);
                    if($data['beban'][$i]=='ras'){
                        $cek = Jurnal::where('order_id',$data['tagihan_order_id'][$i])->where('coa_id',93)->where('debit','>',0)->count();
                        if($cek > 0){
                            Jurnal::create([
                                'order_id' => $data['tagihan_order_id'][$i],
                                'nomor' => $nomor,
                                'no' => $no,
                                'nama' => $data['nama'][$i],
                                'container' => $order->container,
                                'invoice' => $request->invoice,
                                'tipe' => 'TEST',
                                'coa_id' => 134,
                                'debit' => $data['jumlah'][$i],
                                'credit' => 0
                            ]);
                        }else{
                            Jurnal::create([
                                'order_id' => $data['tagihan_order_id'][$i],
                                'nomor' => $nomor,
                                'no' => $no,
                                'nama' => $data['nama'][$i],
                                'container' => $order->container,
                                'invoice' => $request->invoice,
                                'tipe' => 'TEST',
                                'coa_id' => 31,
                                'debit' => $data['jumlah'][$i],
                                'credit' => 0
                            ]);
                        }
                        Jurnal::create([
                            'order_id' => $data['tagihan_order_id'][$i],
                            'nomor' => $nomor,
                            'no' => $no,
                            'nama' => $data['nama'][$i],
                            'container' => $order->container,
                            'invoice' => $request->invoice,
                            'tipe' => 'TEST',
                            'coa_id' => 63,
                            'credit' => $data['jumlah'][$i],
                            'debit' => 0
                        ]);
                    }else{
                        Jurnal::create([
                            'order_id' => $data['tagihan_order_id'][$i],
                            'nomor' => $nomor,
                            'no' => $no,
                            'nama' => $data['nama'][$i],
                            'container' => $order->container,
                            'invoice' => $request->invoice,
                            'tipe' => 'TEST',
                            'coa_id' => 63,
                            'debit' => $data['jumlah'][$i],
                            'credit' => 0
                        ]);
                        Jurnal::create([
                            'order_id' => $data['tagihan_order_id'][$i],
                            'nomor' => $nomor,
                            'no' => $no,
                            'nama' => $data['nama'][$i],
                            'container' => $order->container,
                            'invoice' => $request->invoice,
                            'tipe' => 'TEST',
                            'coa_id' => 28,
                            'credit' => $data['jumlah'][$i],
                            'debit' => 0
                        ]);
                    }

                    TagihanAgen::create([
                        'invoice' => $request->invoice,
                        'order_id' => $data['tagihan_order_id'][$i],
                        'nama' => $data['nama'][$i],
                        'jumlah' => $data['jumlah'][$i],
                        'beban' => $data['beban'][$i]
                    ]);
                }
            }
        }
        return redirect()->route('hutang-agen.index')->with('success', 'Data berhasil disimpan');
    }

    public function update(HutangAgen $hutangagen, Request $request)
    {
        $data = $request->all();
        $hutangagen->update($data);

        return back()->with('success', 'Data berhasil diupdate');
    }

    public function destroy(HutangAgen $hutangagen)
    {
        $hutangagen->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = HutangAgen::all()->sortByDesc('created_at');

        return Datatables::of($data)
            ->addColumn('agen_id', function ($data) {
                return $data->tarif_agen->agen->nama;
            })
            ->addColumn('order_id', function ($data) {
                return $data->order->job . '-' . sprintf('%02d', $data->no_job);
            })
            ->rawColumns([])
            ->make(true);
    }

    public function print()
    {
        $invoice = request('invoice');
        $hutang_agen = HutangAgen::where('invoice', $invoice)->get();
        if($hutang_agen->count() == 0){
            return back()->with('danger', 'Data tidak ditemukan');
        }
        $order = $hutang_agen->first()->order;
        $tagihan = TagihanAgen::where('invoice', $invoice)->get();
        $total = HutangAgen::where('invoice', $invoice)->sum('tarif') + HutangAgen::where('invoice', $invoice)->sum('ppn') - HutangAgen::where('invoice', $invoice)->sum('pph') + TagihanAgen::where('invoice', $invoice)->sum('jumlah');
        $terbilang = $this->terbilang($total);
        $rows = 0;
        foreach ($hutang_agen->groupBy('tarif') as $tarif => $tarif_group) {
            foreach ($tarif_group->groupBy('order.job') as $job => $job_grouo) {
                $rows++;
            }
        }
        return view('admin.hutangagen.print', compact('hutang_agen', 'tagihan', 'total', 'order','terbilang','rows'));
    }

    private function terbilang($angka) {
        $angka = (float)$angka;
        $bilangan = array(
                '',
                'satu',
                'dua',
                'tiga',
                'empat',
                'lima',
                'enam',
                'tujuh',
                'delapan',
                'sembilan',
                'sepuluh',
                'sebelas'
            );
            if ($angka < 12) {
                return $bilangan[$angka];
            } else if ($angka < 20) {
                return $bilangan[$angka - 10] . ' belas';
            } else if ($angka < 100) {
                $hasil_bagi = (int)($angka / 10);
                $hasil_mod = $angka % 10;
                return trim(sprintf('%s puluh %s', $bilangan[$hasil_bagi], $bilangan[$hasil_mod]));
            } else if ($angka < 200) {
                return 'seratus ' . $this->terbilang($angka - 100);
            } else if ($angka < 1000) {
                $hasil_bagi = (int)($angka / 100);
                $hasil_mod = $angka % 100;
                return trim(sprintf('%s ratus %s', $bilangan[$hasil_bagi], $this->terbilang($hasil_mod)));
            } else if ($angka < 2000) {
                return 'seribu ' . $this->terbilang($angka - 1000);
            } else if ($angka < 1000000) {
                $hasil_bagi = (int)($angka / 1000);
                $hasil_mod = $angka % 1000;
                return trim(sprintf('%s ribu %s', $this->terbilang($hasil_bagi), $this->terbilang($hasil_mod)));
            } else if ($angka < 1000000000) {
                $hasil_bagi = (int)($angka / 1000000);
                $hasil_mod = $angka % 1000000;
                return trim(sprintf('%s juta %s', $this->terbilang($hasil_bagi), $this->terbilang($hasil_mod)));
            } else if ($angka < 1000000000000) {
                $hasil_bagi = (int)($angka / 1000000000);
                $hasil_mod = fmod($angka, 1000000000);
                return trim(sprintf('%s miliar %s', $this->terbilang($hasil_bagi), $this->terbilang($hasil_mod)));
            } else {
                return 'Angka terlalu besar';
            }
        }
}
