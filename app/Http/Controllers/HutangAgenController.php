<?php

namespace App\Http\Controllers;

use App\Models\HutangAgen;
use App\Models\Jurnal;
use App\Models\Order;
use App\Models\TagihanAgen;
use App\Models\TarifAgen;
use App\Models\Setting;
use App\Models\COA;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class HutangAgenController extends Controller
{
    protected $sno;
    public function __construct()
    {
        $setting = Setting::find(1);
        $this->sno = $setting->short_name;
    }
    public function index()
    {
       $data = Order::whereHas('agent', function ($q) {
        $q->whereNotNull('top')
          ->where('top', '>', 0);
    })
    ->whereHas('tarif.kondisiInfo', function ($q) {
        $q->whereIn('id', [5, 7, 8,9,10]); // filter kondisi lewat tarif
    })
    ->whereNull('invoice_agen')
    ->whereYear('created_at', '>=', 2025)
    ->with(['agent', 'tarif.kondisiInfo']) // eager load relasi biar efisien
    ->get()
    ->groupBy('agen_id');

        return view('admin.hutangagen.index', compact('data'));
    }

public function list(Request $request)
{
    $year = $request->year ?? now()->year;

    $query = HutangAgen::with([
        'order.tarif.customer',
        'order.penerima',
        'order.tarif.shipmentInfo',
        'order.tarif.dari_lokasi',
        'order.tarif.tujuan_lokasi',
    ])
    ->whereNotNull('jurnal')
    ->whereNull('deleted_at')
    ->whereYear('created_at', $year);

    $hutang = $query->get();

    $data = $hutang->groupBy('draf');

    // ambil semua order_id
    $orderIds = $hutang->pluck('order_id');

    // ambil semua tagihan SEKALI SAJA
    $tagihanAll = TagihanAgen::with('order')
                    ->whereIn('order_id', $orderIds)
                    ->get()
                    ->groupBy('order_id');

    return view('admin.hutangagen.list', compact(
        'data',
        'year',
        'tagihanAll'
    ));
}
    public function draf(Request $request)
    {
        $ids = $request->order_id;
        $orders = Order::whereIn('id', $ids)->get()->groupBy('agen_id');
        if (count($ids) == 0) {
            return back()->with('danger', 'Harus centang salah satu!');
        }
        if ($orders->count() > 1) {
            return back()->with('danger', 'Harus centang pada agen yang sama!');
        }

        $orders = Order::whereIn('id', $ids)->get();
        $jobs = $orders->groupBy('job');
        $tarif = TarifAgen::where('agen_id', $orders->first()->agen_id)->where('is_active', 1)->orderBy('created_at')->get();
        $count = Order::whereIn('id', $ids)->count();
        return view('admin.hutangagen.draf', compact('orders', 'tarif', 'ids', 'count', 'jobs'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $draf = 'HA' . date('ymdhis');
        for ($i = 0; $i < count($request->order_id); $i++) {
            $order = Order::find($request->order_id[$i]);
            HutangAgen::upsert([
                'order_id' => $request->order_id[$i],
                'tarif' => $request->tarif[$i],
                'ppn' => $request->ppn[$i],
                'pph' => $request->pph[$i],
                'invoice' => $request->invoice[$i],
                'draf' => $draf,
                'tanggal' => $request->tanggal[$i]
            ], ['order_id']);
        }
        for ($i = 0; $i < count($data['tagihan_order_id']); $i++) {
            if ($data['nama'][$i] != null && $data['jumlah'][$i] != null && $data['tagihan_order_id'][$i] != null) {
                $tipe = $data['tagihan_order_id'][$i];
                if (substr($tipe, 0, 3) == 'job') {
                    $order = Order::where('job', str_replace('job-', '', $tipe))->first();
                    TagihanAgen::where('order_id', $order->id)->delete();
                } else {
                    TagihanAgen::where('order_id', $data['tagihan_order_id'][$i])->delete();
                }
            }
        }
        for ($i = 0; $i < count($data['tagihan_order_id']); $i++) {
            if ($data['nama'][$i] != null && $data['jumlah'][$i] != null && $data['tagihan_order_id'][$i] != null) {
                $tipe = $data['tagihan_order_id'][$i];
                if (substr($tipe, 0, 3) == 'job') {
                    $order = Order::where('job', str_replace('job-', '', $tipe))->first();
                    TagihanAgen::create([
                        // 'invoice' => $request->invoice[$i],
                        'draf' => $draf,
                        'tipe' => 'group',
                        'order_id' => $order->id,
                        'nama' => $data['nama'][$i],
                        'jumlah' => $data['jumlah'][$i],
                        'beban' => $data['beban'][$i]
                    ]);
                } else {
                    $order = Order::find($data['tagihan_order_id'][$i]);
                    TagihanAgen::create([
                        // 'invoice' => $request->invoice[$i],
                        'draf' => $draf,
                        'order_id' => $data['tagihan_order_id'][$i],
                        'nama' => $data['nama'][$i],
                        'jumlah' => $data['jumlah'][$i],
                        'beban' => $data['beban'][$i]
                    ]);
                }
            }
        }
        return redirect()->route('hutang-agen.print', ['draf' => $draf])->with('success', 'Data berhasil disimpan');
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
        $draf = request('draf');
        $hutang_agen = HutangAgen::where('draf', $draf)->get();
        if ($hutang_agen->count() == 0) {
            return back()->with('danger', 'Data tidak ditemukan');
        }
        $order = $hutang_agen->first()->order;
        $tagihan = TagihanAgen::where('draf', $draf)->get();
        $total = HutangAgen::where('draf', $draf)->sum('tarif') + HutangAgen::where('draf', $draf)->sum('ppn') - HutangAgen::where('draf', $draf)->sum('pph') + TagihanAgen::where('draf', $draf)->sum('jumlah');
        $terbilang = $this->terbilang($total);
        $rows = 0;
        foreach ($hutang_agen->groupBy('invoice') as $tarif => $tarif_group) {
            // foreach ($tarif_group->groupBy('tarif') as $job => $job_group) {
            //     foreach($job_group->groupBy('order.no_job') as $inv) {
            //     }
            // }

            $rows++;
        }
        return view('admin.hutangagen.print', compact('hutang_agen', 'tagihan', 'total', 'order', 'terbilang', 'rows'));
    }

    public function generate_jurnal()
    {
        $order_id = HutangAgen::where('draf', request('draf'))->pluck('order_id')->toArray();
        $generate_jurnal = $this->check_omset($order_id);

        $this->jurnal(request('draf'));
        // if($generate_jurnal){
        // }else{
        //     $this->jurnal(request('draf'),134);
        // }

        return redirect()->route('hutang-agen.print', ['draf' => request('draf'), 'print' => 1]);
    }
    
    private function jurnal($draf, $coa_id = 31)
    {
        $hutang_agen = HutangAgen::where('draf', $draf)->get();
        $tagihan_agen = TagihanAgen::where('draf', $draf)->get();
        $no = Jurnal::where('tipe', 'JNL')->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->max('no') + 1;
        $nomor = sprintf('%02d', date('m')) . '-' . sprintf('%03d', $no) . '/' . ($this->sno == 'ALB' ? 'ALB/' : '') . date('y');
        $pph = 0;
        $total = 0;
        $total_tagihan_agen = 0;
        $c93 = COA::where('coa_ras', 93)->first()->id ?? 93;
        $c134 = COA::where('coa_ras', 134)->first()->id ?? 134;
        $c31 = COA::where('coa_ras', 31)->first()->id ?? 31;
        $c28 = COA::where('coa_ras', 28)->first()->id ?? 28;
        $c73 = COA::where('coa_ras', 73)->first()->id ?? 73;
        $c63 = COA::where('coa_ras', 63)->first()->id ?? 63;
        foreach ($hutang_agen as $hutang) {
            $pph += round($hutang->pph);
            $order = $hutang->order;
            $cek = Jurnal::where('order_id', $order->id)->where('coa_id', $c93)->where('debit', '>', 0)->count();
            $jurnal = array();
            $jurnal['order_id'] = $order->id;
            $jurnal['nomor'] = $nomor;
            $jurnal['relasi'] = $nomor;
            $jurnal['no'] = $no;
            $jurnal['invoice_agen'] = $hutang->invoice ?? null;
            $jurnal['nama'] = 'Biaya Dooring ' . ($order->tarif->customer->nama ?? '') . ' ' . ($order->tarif->shipmentInfo->nama ?? '') . ' ' . ($order->agent->nama ?? '');
            $jurnal['container'] = $order->container;
            $jurnal['tipe'] = 'JNL';
            if ($cek > 0) {
                $jurnal['coa_id'] = $c134;
                $jurnal['debit'] = $hutang->tarif + round($hutang->ppn);
                $jurnal['credit'] = 0;
                Jurnal::create($jurnal);
            } else {
                $jurnal['coa_id'] = ($order->checkOmset() ? $c134 : $c31);
                $jurnal['debit'] = $hutang->tarif + round($hutang->ppn);
                $jurnal['credit'] = 0;
                Jurnal::create($jurnal);
            }

            $hutang->update([
                'status' => 1,
                'jurnal' => $nomor
            ]);
            $order->update(['invoice_agen' => $hutang->invoice]);
            $total += $hutang->tarif + round($hutang->ppn);
        }
        foreach ($tagihan_agen as $tagihan) {
            if ($tagihan->tipe == 'group') {
                $job = $tagihan->order->job;
                $jobs = Order::where('job', $job)->get();
                $amount = (int)$tagihan->jumlah / $jobs->count();
                $price = (int)((int)$tagihan->jumlah / $jobs->count());
                $selisih = (int)$tagihan->jumlah - ($price * $jobs->count());
                foreach ($jobs as $key => $order) {
                   
                    if ($key == 0) {
                        $amount = (int)((int)$tagihan->jumlah / $jobs->count()) + $selisih;
                    } else {
                        $amount = $price;
                    }
                    if ($tagihan->beban == 'ras') {
                        $cek = Jurnal::where('order_id', $order->id)->where('coa_id', $c93)->where('debit', '>', 0)->count();
                        
                        if ($cek > 0) {
                            Jurnal::create([
                                'order_id' => $order->id,
                                'nomor' => $nomor,
                                'relasi' => $nomor,
                                'no' => $no,
                                'nama' => $tagihan->nama,
                                'container' => $order->container,
                                'invoice_agen' => $order->invoice_agen,
                                'tipe' => 'JNL',
                                'coa_id' => $c134,
                                'debit' => $amount,
                                'credit' => 0
                            ]);
                        } else {
                            Jurnal::create([
                                'order_id' => $order->id,
                                'nomor' => $nomor,
                                'relasi' => $nomor,
                                'no' => $no,
                                'nama' => $tagihan->nama,
                                'container' => $order->container,
                                'invoice_agen' => $order->invoice_agen,
                                'tipe' => 'JNL',
                                'coa_id' => ($order->checkOmset() ? $c134 : $c31),
                                'debit' => $amount,
                                'credit' => 0
                            ]);
                        }
                    } else {
                        Jurnal::create([
                            'order_id' => $order->id,
                            'nomor' => $nomor,
                            'relasi' => $nomor,
                            'no' => $no,
                            'nama' => $tagihan->nama,
                            'container' => $order->container,
                            'invoice_agen' => $order->invoice_agen,
                            'tipe' => 'JNL',
                            'coa_id' => $c28,
                            'debit' => $amount,
                            'credit' => 0
                        ]);
                    }
                }
            } else {
                $order = $tagihan->order;
                if ($tagihan->beban == 'ras') {
                    $cek = Jurnal::where('order_id', $tagihan->order_id)->where('coa_id', $c93)->where('debit', '>', 0)->count();
                    if ($cek > 0) {
                        Jurnal::create([
                            'order_id' => $tagihan->order_id,
                            'nomor' => $nomor,
                            'relasi' => $nomor,
                            'no' => $no,
                            'nama' => $tagihan->nama,
                            'container' => $order->container,
                            'invoice_agen' => $order->invoice_agen,
                            'tipe' => 'JNL',
                            'coa_id' => $c134,
                            'debit' => $tagihan->jumlah,
                            'credit' => 0
                        ]);
                    } else {
                        Jurnal::create([
                            'order_id' => $tagihan->order_id,
                            'nomor' => $nomor,
                            'relasi' => $nomor,
                            'no' => $no,
                            'nama' => $tagihan->nama,
                            'container' => $order->container,
                            'invoice_agen' => $order->invoice_agen,
                            'tipe' => 'JNL',
                            'coa_id' => ($order->checkOmset() ? $c134 : $c31),
                            'debit' => $tagihan->jumlah,
                            'credit' => 0
                        ]);
                    }
                } else {
                    Jurnal::create([
                        'order_id' => $tagihan->order_id,
                        'nomor' => $nomor,
                        'relasi' => $nomor,
                        'no' => $no,
                        'nama' => $tagihan->nama,
                        'container' => $order->container,
                        'invoice_agen' => $order->invoice_agen,
                        'tipe' => 'JNL',
                        'coa_id' => $c28,
                        'debit' => $tagihan->jumlah,
                        'credit' => 0
                    ]);
                }
            }

            $tagihan->update([
                'status' => 1,
                'jurnal' => $nomor
            ]);

            $total += $tagihan->jumlah;
            $total_tagihan_agen += $tagihan->jumlah;
        }
        foreach ($hutang_agen->groupBy('invoice') as $invoice => $invoice_group) {
    $firstRecord = $invoice_group->first();

    // Hitung ulang total_tagihan_agen hanya untuk invoice ini
$order_ids = $invoice_group->pluck('order_id'); // semua order_id dalam invoice ini
$total_tagihan_agen = $tagihan_agen
    ->whereIn('order_id', $order_ids)
    ->sum('jumlah') ?? 0;

    // PPH 23
    Jurnal::create([
        'order_id' => null,
        'nomor' => $nomor,
        'relasi' => $nomor,
        'no' => $no,
        'nama' => 'Potongan PPH 23 Agen ' . ($firstRecord->order->agent->nama ?? '') . ' ' . $invoice,
        'tipe' => 'JNL',
        'coa_id' => $c73,
        'debit' => 0,
        'credit' => $invoice_group->sum('pph'),
        'invoice_agen' => $invoice
    ]);


    // Hutang Agen
    Jurnal::create([
        'order_id' => null,
        'nomor' => $nomor,
        'no' => $no,
        'nama' => 'Hutang Agen ' . $invoice . ' ' . ($firstRecord->order->agent->nama ?? ''),
        'relasi' => $nomor,
        'tipe' => 'JNL',
        'coa_id' => $c63,
        'credit' => ($invoice_group->sum('tarif') + round($invoice_group->sum('ppn'))) - $invoice_group->sum('pph') + $total_tagihan_agen,
        'debit' => 0,
        'invoice_agen' => $invoice
    ]);
}

        return true;
    }

    private function check_omset($order_id)
    {
        $c93 = COA::where('coa_ras', 93)->first()->id ?? 93;
        foreach ($order_id as $id) {
            $jurnals = Jurnal::where('order_id', $id)->where('coa_id', $c93)->where('debit', '>', 0)->get();
            $order = Order::find($id);
            if ($jurnals->count() > 0 && $order) {
                return false;
            }
        }
        return true;
    }

    private function terbilang($angka)
    {
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
