<?php

namespace App\Http\Controllers;

use App\Exports\JurnalBatchExport;
use App\Exports\JurnalCoaExport;
use App\Exports\JurnalMonth;
use App\Http\Resources\OrderResource;
use App\Services\SyncService;
use App\Imports\JurnalImport;
use App\Models\COA;
   use App\Exports\JurnalCodeExport;
use App\Models\Agen;
use App\Models\HutangPelayaran;
use App\Models\JasaKirim;
use App\Models\Jurnal;
use App\Models\Customer;
use Illuminate\Support\Collection;
use App\Models\CustomerTrucking;
use App\Models\JurnalSample;
use App\Models\JurnalTampungan;
use App\Models\Order;
use App\Models\OrderTrucking;
use App\Models\Tarif;
use App\Models\Transaksi;
use App\Models\Pelayaran;
use App\Models\Setting;
use App\Models\TransaksiSopir;
use App\Models\TransaksiTrucking;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Storage;

class JurnalController extends Controller
{
    protected $sno;
    public function __construct()
    {
        $setting = Setting::find(1);
        $this->sno = $setting->short_name;
    }


    public function index()
    {
        $now = Carbon::now()->addMonths(1)->format('Y-m-d');
        $last = Carbon::now()->subMonths(3)->format('Y-m-d');

        $cacheKey = 'jurnal_unbalance_' . $last . '_' . $now;

        $unbalance = Cache::remember($cacheKey, 300, function () use ($last, $now) {
            return Jurnal::select([
                    'nomor',
                    DB::raw("SUM(debit) as debit"),
                    DB::raw("SUM(credit) as credit")
                ])
                ->whereBetween('created_at', [$last, $now])
                ->groupBy('nomor')
                ->havingRaw('ABS(SUM(debit) - SUM(credit)) > 0.01')
                ->get();
        });

        $month = request('month') ?? date('m');
        $year = request('year') ?? date('Y');
        $is_sample = request('is_sample') ?? 'real';
        return view('admin.jurnal.index', compact('month', 'unbalance', 'year', 'is_sample'));
    }


    public function jNoJob(){
          $month = request('month') ?? date('m');
        $year = request('year') ?? date('Y');
        $is_sample = request('is_sample') ?? 'real';
        return view('admin.jurnal.jurnal-no-job', compact('month', 'year', 'is_sample'));
    }
    public function j_cekcoa(){
        return view('admin.jurnal.jurnal-cek-coa', compact('month', 'unbalance', 'year', 'is_sample'));
    }

        public function exportExcel(Request $request)
{
    $data = json_decode($request->input('data'), true);
    $jurnals = Jurnal::whereIn('id', $data)->get();

    $tanggal = Carbon::now()->format('Ymd'); // Format: 20250708

    return Excel::download(new JurnalCodeExport($jurnals), "{$tanggal}-jurnal-code.xlsx");
}

    public function code(){
        $now = Carbon::now()->addMonths(1)->format('Y-m-d');
        $last = Carbon::now()->subMonths(3)->format('Y-m-d');

        $cacheKey = 'jurnal_unbalance_' . $last . '_' . $now;
        $coa = COA::where('is_active', 1)->orderBy('kode')->get();

        $month = request('month') ?? date('m');
        $year = request('year') ?? date('Y');
        $is_sample = request('is_sample') ?? 'real';
        return view('admin.jurnal.kode-balik', compact('month', 'year', 'is_sample', 'coa'));
    }

    public function show($id) {
      $month = request('month') ?? date('m');
      $year = request('year') ?? date('Y');
      $is_sample = request('is_sample') ?? 'real';
      $tipe = request('tipe') ?? 'BB';
    // Atau kembalikan response kosong jika tidak diperlukan
      return view('admin.jurnal.jurnal-cek-coa', compact('month', 'year', 'is_sample','tipe'));
    }



    public function totalan_sopir()
    {
        $data = TransaksiSopir::whereNull('jurnal')->where('jurnal_status', 0)->orderBy('tgl_invoice')->get();
        $data1 = TransaksiSopir::whereNotNull('jurnal')->where('jurnal_status', 1)->orderBy('jurnal_submit', 'desc')->get();
        return view('admin.jurnal.totalan_sopir', compact('data', 'data1'));
    }

    public function slip_totalan_sopir(Request $request)
    {
        $ids = explode(',', $request->ids);
        $data = TransaksiSopir::whereIn('id', $ids)->pluck('order_id');
        $id = '';
        foreach ($data as $order_id) {
            $id .= str_replace(['[', ']'], '', $order_id) . ',';
        }
        $id = explode(',', $id);
        $orders = OrderTrucking::with('sopir')->whereIn('id', $id)->get();
        $created_at = $request->created_at;
        return view('admin.jurnal.slip_totalan_sopir', compact('orders', 'created_at'));
    }

    public function submit_slip_totalan_sopir(Request $request)
    {
        if (!$request->nomor) {
            return back()->with('danger', 'Harap pilih nomor jurnal terlebih dahulu!');
        }

        if ($request->jurnal_simpanan_sopir) {
            foreach ($request->jurnal_simpanan_sopir as $js) {
                $debit = $js;
                $credit = $js;
                $debit['created_at'] = $request->created_at;
                $credit['coa_id'] = ($credit['tipe'] == 'BBK' ? 45 : ($credit['tipe'] == 'BKK' ? 16 : ($credit['tipe'] == 'BBKT' ? 175 : null)));
                $credit['credit'] = $credit['debit'];
                $credit['debit'] = 0;
                $credit['created_at'] = $request->created_at;
                if (in_array($debit['order_trucking_id'], $request->active)) {
                    Jurnal::create($debit);
                    Jurnal::create($credit);
                    TransaksiSopir::where('order_id', 'LIKE', '%' . $debit['order_trucking_id'] . '%')->update([
                        'jurnal' => $debit['nomor'],
                        'jurnal_status' => 1,
                        'jurnal_tgl' => $request->created_at,
                        'jurnal_submit' => date('Y-m-d H:i:s')
                    ]);
                } else {
                    JurnalSample::create($debit);
                    JurnalSample::create($credit);
                }
            }
        }
        if ($request->jurnal_simpanan_kuli) {
            foreach ($request->jurnal_simpanan_kuli as $js) {
                $debit = $js;
                $credit = $js;
                $debit['created_at'] = $request->created_at;
                $credit['coa_id'] = ($credit['tipe'] == 'BBK' ? 45 : ($credit['tipe'] == 'BKK' ? 16 : ($credit['tipe'] == 'BBKT' ? 175 : null)));
                $credit['credit'] = $credit['debit'];
                $credit['debit'] = 0;
                $credit['created_at'] = $request->created_at;
                if (in_array($debit['order_trucking_id'], $request->active)) {
                    Jurnal::create($debit);
                    Jurnal::create($credit);
                    TransaksiSopir::where('order_id', 'LIKE', '%' . $debit['order_trucking_id'] . '%')->update([
                        'jurnal' => $debit['nomor'],
                        'jurnal_status' => 1,
                        'jurnal_tgl' => $request->created_at,
                        'jurnal_submit' => date('Y-m-d H:i:s')
                    ]);
                } else {
                    JurnalSample::create($debit);
                    JurnalSample::create($credit);
                }
            }
        }
        if ($request->jurnal_tbtl) {
            foreach ($request->jurnal_tbtl as $js) {
                $debit = $js;
                $credit = $js;
                $debit['created_at'] = $request->created_at;
                $credit['coa_id'] = ($credit['tipe'] == 'BBK' ? 45 : ($credit['tipe'] == 'BKK' ? 16 : ($credit['tipe'] == 'BBKT' ? 175 : null)));
                $credit['credit'] = $credit['debit'];
                $credit['debit'] = 0;
                $credit['created_at'] = $request->created_at;
                if (in_array($debit['order_trucking_id'], $request->active)) {
                    Jurnal::create($debit);
                    Jurnal::create($credit);
                    TransaksiSopir::where('order_id', 'LIKE', '%' . $debit['order_trucking_id'] . '%')->update([
                        'jurnal' => $debit['nomor'],
                        'jurnal_status' => 1,
                        'jurnal_tgl' => $request->created_at,
                        'jurnal_submit' => date('Y-m-d H:i:s')
                    ]);
                } else {
                    JurnalSample::create($debit);
                    JurnalSample::create($credit);
                }
            }
        }
        if ($request->jurnal_stappel) {
            foreach ($request->jurnal_stappel as $js) {
                $debit = $js;
                $credit = $js;
                $debit['created_at'] = $request->created_at;
                $credit['coa_id'] = ($credit['tipe'] == 'BBK' ? 45 : ($credit['tipe'] == 'BKK' ? 16 : ($credit['tipe'] == 'BBKT' ? 175 : null)));
                $credit['credit'] = $credit['debit'];
                $credit['debit'] = 0;
                $credit['created_at'] = $request->created_at;
                if (in_array($debit['order_trucking_id'], $request->active)) {
                    Jurnal::create($debit);
                    Jurnal::create($credit);
                    TransaksiSopir::where('order_id', 'LIKE', '%' . $debit['order_trucking_id'] . '%')->update([
                        'jurnal' => $debit['nomor'],
                        'jurnal_status' => 1,
                        'jurnal_tgl' => $request->created_at,
                        'jurnal_submit' => date('Y-m-d H:i:s')
                    ]);
                } else {
                    JurnalSample::create($debit);
                    JurnalSample::create($credit);
                }
            }
        }

        return redirect()->route('jurnal.totalan_sopir')->with('success', 'Jurnal berhasil dibuat!');
    }

    public function order()
    {
        return view('admin.jurnal.order');
    }

    public function order_trucking()
    {
        return view('admin.jurnal.order_trucking');
    }

    public function kolektif()
    {
        $job = Order::pluck('job')->toArray();
        $job = array_unique($job);
        $coa = COA::where('is_active', 1)->orderBy('kode')->get();
        return view('admin.jurnal.kolektif', compact('job', 'coa'));
    }

    public function manual()
    {
        return view('admin.jurnal.manual');
    }
    
    public function merge()
    {
        // Mengambil tipe jurnal yang unik dan menyimpannya dalam cache selama 600 detik
        $tipe = Cache::remember('jurnal_tipe', 600, function () {
            return Jurnal::pluck('tipe')->unique()->toArray();
        });
        // Inisialisasi array untuk data
        $data1 = [];
        $data = [];
    
        // Menampilkan data berdasarkan tipe awal jika ada yang dipilih
        if ($tipeAwal = request('tipe_awal')) {
            $data = Cache::remember("jurnal_data_{$tipeAwal}", 600, function () use ($tipeAwal) {
                // return Jurnal::where('tipe', $tipeAwal)->where('kunci', 0)->pluck('nomor')->unique()->toArray();
                  return Jurnal::where('tipe', $tipeAwal)->pluck('nomor')->unique()->toArray();
            });
        }
    
        // Menampilkan data berdasarkan tipe tujuan jika ada yang dipilih
        if ($tipeTujuan = request('tipe_tujuan')) {
            $data1 = Cache::remember("jurnal_data_{$tipeTujuan}", 600, function () use ($tipeTujuan) {
                return Jurnal::where('tipe', $tipeTujuan)->pluck('nomor')->unique()->toArray();
            });
        }
    
        // Mengirim data ke view
        return view('admin.jurnal.merge', compact('data', 'data1', 'tipe'));
    }
    
    public function tampungan()
    {
        $data = JurnalTampungan::get();
        if (request()->ajax()) {
            $view = view('data.jurnal', compact('data'))->render();
            return response()->json(['html' => $view]);
        }
        $no_1 = Jurnal::where('tipe', 'JNL')->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->max('no') + 1;
        $no_2 = Jurnal::where('tipe', 'BBK')->whereYear('created_at', date('Y'))->max('no') + 1;
        $no_3 = Jurnal::where('tipe', 'BBM')->whereYear('created_at', date('Y'))->max('no') + 1;
        $no_4 = Jurnal::where('tipe', 'BKK')->whereYear('created_at', date('Y'))->max('no') + 1;
        $no_5 = Jurnal::where('tipe', 'BKM')->whereYear('created_at', date('Y'))->max('no') + 1;
        $jno_1 = sprintf('%02d', date('m')) . '-' . sprintf('%03d', $no_1) . '/' . ($this->sno == 'ALB' ? 'ALB/' : '') . date('y');
        $jno_2 = sprintf('%03d', $no_2) . '/BBK-' . $this->sno . '/' . date('y');
        $jno_3 = sprintf('%03d', $no_3) . '/BBM-' . $this->sno . '/' . date('y');
        $jno_4 = sprintf('%03d', $no_4) . '/BKK-' . $this->sno . '/' . date('y');
        $jno_5 = sprintf('%03d', $no_5) . '/BKM-' . $this->sno . '/' . date('y');
        $data = [];
        return view('admin.jurnal.tampungan', compact('no_1', 'no_2', 'no_3', 'no_4', 'no_5', 'jno_1', 'jno_2', 'jno_3', 'jno_4', 'jno_5', 'data'));
    }

    public function tampungan_store(Request $request)
    {
        $debit = JurnalTampungan::sum('debit');
        $credit = JurnalTampungan::sum('credit');
        $status = true;
        $message = 'Jurnal tampungan berhasil diterbitkan!';
        if ($debit != $credit) {
            $status = false;
            $message = 'Debit dan Credit tidak balance!';
        } elseif (!$request->nomor) {
            $status = false;
            $message = 'Harap pilih tipe jurnal!';
        } else {
            if ($request->tipe == 'JNL') {
                $no = Jurnal::where('tipe', 'JNL')->whereMonth('created_at', date('m', strtotime($request->created_at)))->whereYear('created_at', date('Y', strtotime($request->created_at)))->max('no') + 1;
                $nomor = sprintf('%02d', date('m', strtotime($request->created_at))) . '-' . sprintf('%03d', $no) . '/' . ($this->sno == 'ALB' ? 'ALB/' : '') . date('y', strtotime($request->created_at));
            } else {
                $no = Jurnal::where('tipe', $request->tipe)->whereYear('created_at', date('Y'))->max('no') + 1;
                $nomor = sprintf('%03d', $no) . '/' . $request->tipe . '-' . $this->sno . '/' . date('y', strtotime($request->created_at));
            }
            $data = JurnalTampungan::all()->toArray();
            foreach ($data as $item) {
                $jurnal = $item;
                $jurnal['nomor'] = $nomor;
                $jurnal['tipe'] = $request->tipe;
                $jurnal['no'] = $no;
                $jurnal['created_at'] = $request->created_at;
                Jurnal::create($jurnal);
            }
            JurnalTampungan::truncate();
        }
        return response([
            'status' => $status,
            'message' => $message,
        ]);
    }

    public function tampungan_destroy()
    {
        JurnalTampungan::find(request('id'))->delete();
        return response('success');
    }

    public function balik()
    {
        $coa = COA::where('is_active', 1)->orderBy('kode')->get();

$code = Jurnal::whereNotNull('kode')
    ->whereNull('jurnal_balik')
    ->select('kode', 'jurnal_balik')
    ->distinct()
    ->get()
    ->toArray();

$uncode = Jurnal::whereNotNull('kode')
    ->whereNotNull('jurnal_balik')
    ->select('kode', 'jurnal_balik')
    ->distinct()
    ->get()
    ->toArray();

// Gabungkan
$combined = array_merge($code, $uncode);

// Konversi ke koleksi
$collection = collect($combined);

// Ambil semua kode yang punya jurnal_balik tidak null
$kodeDenganBalik = $collection
    ->whereNotNull('jurnal_balik')
    ->pluck('kode')
    ->unique();

// Filter hanya yang jurnal_balik null dan tidak ada di daftar kode dengan jurnal_balik
$kode = $collection
    ->filter(function ($item) use ($kodeDenganBalik) {
        return is_null($item['jurnal_balik']) && !$kodeDenganBalik->contains($item['kode']);
    })
    ->values()
    ->toArray();




        $data = [];
        $new = [];
        $coa_debit = null;
        $coa_credit = null;
        $orders = Order::get(['id', 'job', 'no_job', 'seal']);
        if (request('draf')) {
            $query = Jurnal::query();
            $query->whereNull('jurnal_balik');
            if (request('order_id')) {
                if (request('tipe') == 'job') {
                    $order = Order::find(request('order_id'));
                    $job = $order->job;
                    $query->whereHas('order', function ($q) use ($job) {
                        $q->where('job', $job);
                    });
                } else if (request('tipe') == 'id_job') {
                    $query->where('order_id', request('order_id'));
                }
            }
            if (request('name')) {
                $query->where('kode', request('name'));
            }
            if (request('debit_coa_id_tujuan')) {
                $query->where('coa_id', request('debit_coa_id_tujuan'));
                $query->where('debit', '>', 0);
                $query->orderBy('debit','asc');
            }
            if (request('credit_coa_id_tujuan')) {
                $query->where('coa_id', request('credit_coa_id_tujuan'));
                $query->where('credit', '>', 0);
                $query->whereNull('jurnal_balik');
                $query->orderBy('credit','asc');
                if (request('order_id')) {
                    $query->where('order_id', request('order_id'));
                }
            }
            $query->whereBetween('created_at', [request('start'), request('end')]);
            $data = $query->get();
            $new = array();
            foreach ($data as $idx => $item) {
                if ($item['debit'] > 0) {
                    $new[$idx]['debit'] = $item;
                    $new[$idx]['credit'] = [];
                } else {
                    $new[$idx]['credit'] = $item;
                    $new[$idx]['debit'] = [];
                }
            }
            $coa_debit = COA::find(request('debit_coa_id'));
            $coa_credit = COA::find(request('credit_coa_id'));
        }
        $no_1 = Jurnal::where('tipe', 'JNL')->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->max('no') + 1;
        $no_2 = Jurnal::where('tipe', 'BBK')->whereYear('created_at', date('Y'))->max('no') + 1;
        $no_3 = Jurnal::where('tipe', 'BBM')->whereYear('created_at', date('Y'))->max('no') + 1;
        $no_4 = Jurnal::where('tipe', 'BKK')->whereYear('created_at', date('Y'))->max('no') + 1;
        $no_5 = Jurnal::where('tipe', 'BKM')->whereYear('created_at', date('Y'))->max('no') + 1;
        $nomor_1 = sprintf('%02d', date('m')) . '-' . sprintf('%03d', $no_1) . '/' . ($this->sno == 'ALB' ? 'ALB/' : '') . date('y');
        $nomor_2 = sprintf('%03d', $no_2) . '/BBK-' . $this->sno . '/' . date('y');
        $nomor_3 = sprintf('%03d', $no_3) . '/BBM-' . $this->sno . '/' . date('y');
        $nomor_4 = sprintf('%03d', $no_4) . '/BKK-' . $this->sno . '/' . date('y');
        $nomor_5 = sprintf('%03d', $no_5) . '/BKM-' . $this->sno . '/' . date('y');
        return view('admin.jurnal.balik', compact('kode','coa', 'new', 'coa_debit', 'coa_credit', 'orders', 'data', 'no_1', 'no_2', 'no_3', 'no_4', 'no_5', 'nomor_1', 'nomor_2', 'nomor_3', 'nomor_4', 'nomor_5'));
    }

    public function store_manual(Request $request)
    {
        $data = $request->all();
        $no = Jurnal::where('tipe', $data['tipe'])->whereYear('created_at', date('Y', strtotime($data['created_at'])))->max('no') + 1;
        if ($data['tipe'] == 'JNL') {
            $no = Jurnal::where('tipe', 'JNL')->whereMonth('created_at', date('m', strtotime($data['created_at'])))->whereYear('created_at', date('Y', strtotime($data['created_at'])))->max('no') + 1;
        }

        $jurnal_model = new Jurnal();
        if ($data['simpan'] == 'tampungan') {
            $jurnal_model = new JurnalTampungan();
        }

        for ($i = 0; $i < count($data['debit_coa_id']); $i++) {
            if ($data['name'][$i] && $data['amount'][$i]) {
                $name = $data['name'][$i];
                $no_bg = $data['no_bg'][$i] ?? null;
                $order_trucking = $data['invoice_trucking'][$i] ?? null;
                $order_vendor = $data['invoice_vendor'][$i] ?? null;
                $order_expdc = $data['invoice'][$i] ?? null;
                $order_agen = $data['invoice_agen'][$i] ?? null;
                $agen =  Order::find($order_agen);
                $jurnal_external = $data['invoice_external'][$i] ?? null;
                $expdc = Order::find($order_expdc);
                $trucking = OrderTrucking::find($order_trucking);
                $vendor = OrderTrucking::find($order_vendor);
                $invoice_vendor = $vendor->invoice ?? null;
                $invoice_trucking = $trucking->invoice ?? null;
                $invoice_expdc = $expdc->invoice ?? null;
                $invoice_agen = $agen->invoice_agen ?? null;
                if ($data['tipe'] == 'JNL') {
                    $nomor = sprintf('%02d', date('m', strtotime($data['created_at']))) . '-' . sprintf('%03d', $no) . '/' . ($this->sno == 'ALB' ? 'ALB/' : '') . date('y', strtotime($data['created_at']));
                } else {
                    $nomor = sprintf('%03d', $no) . '/' . $data['tipe'] . '-' . $this->sno . '/' . date('y', strtotime($data['created_at']));
                }
                if ($data['debit_coa_id'][$i] && $data['credit_coa_id'][$i]) {
                    // Tentukan nilai relasi untuk debit
                    $relasiDebit = $data['relasi'][$i] ?? ($invoice_vendor === null && $invoice_trucking === null && $invoice_expdc === null && $invoice_agen === null ? $nomor : $nomor);
                    $relasiCredit = $data['relasi'][$i] ?? ($invoice_vendor === null && $invoice_trucking === null && $invoice_expdc === null && $invoice_agen === null  ? $nomor : $nomor);
                    // Buat entri untuk debit
                    $jurnal_model->create([
                        'tipe' => $data['tipe'],
                        'invoice_external' => $jurnal_external?? null,
                        'invoice_agen' => $invoice_agen ?? null,
                        'invoice_vendor' => $invoice_vendor ?? null,
                        // 'order_trucking_id' => $order_trucking ?? $order_vendor ?? null,
                        'invoice' => $invoice_expdc ?? null,
                        // 'order_id' => $order_expdc ?? $order_agen ?? null,
                        'invoice_trucking' => $invoice_trucking ?? null,
                        'nopol' => $data['nopol'][$i],
                        'coa_id' => $data['debit_coa_id'][$i],
                        'nomor' => $nomor,
                        'no_bg' => $no_bg,
                        'nama' => $name,
                        'debit' => $data['amount'][$i],
                        'created_at' => $data['created_at'],
                        'relasi' => $relasiDebit,
                        'no' => $no,
                    ]);

                    // Tentukan nilai relasi untuk kredit
                    $relasiCredit = $data['relasi'][$i] ?? ($invoice_vendor === null && $invoice_trucking === null && $invoice_expdc === null && $invoice_agen === null  ? $nomor : $nomor);
                    // Buat entri untuk kredit
                    $jurnal_model->create([
                        'tipe' => $data['tipe'],
                        'invoice' => $invoice_expdc ?? null,
                        'invoice_external' => $jurnal_external?? null,
                        'invoice_agen' => $invoice_agen ?? null,
                        // 'order_id' => $order_expdc ?? $order_agen ?? null,
                        'invoice_vendor' => $invoice_vendor ?? null,
                        // 'order_trucking_id' => $order_trucking ?? $order_vendor ?? null,
                        'invoice_trucking' => $invoice_trucking ?? null,
                        'nopol' => $data['nopol'][$i],
                        'coa_id' => $data['credit_coa_id'][$i],
                        'nomor' => $nomor,
                        'nama' => $name,
                        'no_bg' => $no_bg,
                        'credit' => $data['amount'][$i],
                        'created_at' => $data['created_at'],
                        'relasi' => $relasiCredit,
                        'no' => $no,
                    ]);
                } else {
                    if ($data['debit_coa_id'][$i]) {
                        // Tentukan nilai relasi untuk debit
                        $relasiDebit = $data['relasi'][$i] ?? ($invoice_vendor === null && $invoice_trucking === null && $invoice_expdc === null && $invoice_agen === null ? $nomor : $nomor);

                        // Buat entri untuk debit
                        $jurnal_model->create([
                            'tipe' => $data['tipe'],
                            'invoice' => $invoice_expdc ?? null,
                            'invoice_external' => $jurnal_external?? null,
                            'invoice_agen' => $invoice_agen ?? null,
                            // 'order_id' => $order_expdc ?? $order_agen ?? null,
                            'invoice_vendor' => $invoice_vendor ?? null,
                            // 'order_trucking_id' => $order_trucking ?? $order_vendor ?? null,
                            'invoice_trucking' => $invoice_trucking ?? null,
                            'nopol' => $data['nopol'][$i],
                            'coa_id' => $data['debit_coa_id'][$i],
                            'nomor' => $nomor,
                            'no_bg' => $no_bg,
                            'nama' => $name,
                            'debit' => $data['amount'][$i],
                            'created_at' => $data['created_at'],
                            'relasi' => $relasiDebit,
                            'no' => $no,
                        ]);
                    }

                    if ($data['credit_coa_id'][$i]) {
                        // Tentukan nilai relasi untuk kredit
                        $relasiCredit = $data['relasi'][$i] ?? ($invoice_vendor === null && $invoice_trucking === null && $invoice_expdc === null && $invoice_agen === null  ? $nomor : $nomor);
                        // Buat entri untuk kredit
                        $jurnal_model->create([
                            'tipe' => $data['tipe'],
                            'invoice' => $invoice_expdc ?? null,
                            'invoice_agen' => $invoice_agen ?? null,
                            'invoice_external' => $jurnal_external?? null,
                            // 'order_id' => $order_expdc ?? $order_agen ?? null,
                            'invoice_vendor' => $invoice_vendor ?? null,
                            // 'order_trucking_id' => $order_trucking ?? $order_vendor ?? null,
                            'invoice_trucking' => $invoice_trucking ?? null,
                            'nopol' => $data['nopol'][$i],
                            'coa_id' => $data['credit_coa_id'][$i],
                            'nomor' => $nomor,
                            'no_bg' => $no_bg,
                            'nama' => $name,
                            'credit' => $data['amount'][$i],
                            'created_at' => $data['created_at'],
                            'relasi' => $relasiCredit,
                            'no' => $no,
                        ]);
                    }
                }
            }
        }

        return back()->with('success', 'Data berhasil disimpan');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $no = Jurnal::where('tipe', $data['tipe'])->whereYear('created_at', date('Y', strtotime($data['created_at'])))->max('no') + 1;
        if ($data['tipe'] == 'JNL') {
            $no = Jurnal::where('tipe', 'JNL')->whereMonth('created_at', date('m', strtotime($data['created_at'])))->whereYear('created_at', date('Y', strtotime($data['created_at'])))->max('no') + 1;
        }
        $arr_order = array();
        $jurnal_model = new Jurnal();
        if ($data['simpan'] == 'tampungan') {
            $jurnal_model = new JurnalTampungan();
        }

        for ($i = 0; $i < count($data['debit_coa_id']); $i++) {
            if ($data['name'][$i] && $data['amount'][$i]) {
                $name = $data['name'][$i];
                $no_bg = $data['no_bg'][$i] ?? null;
                $jurnal_external = $data['invoice_external'][$i] ?? null;
                $order_id = $data['invoice'][$i] ?? null;
                $order_id1 = $data['invoice_agen'][$i] ?? null;
                $orders = Order::find($order_id);
                $orders1 = Order::find($order_id1);
                $invoice = $orders->invoice ?? null;
                $invoice_agen = $orders1->invoice_agen ?? null;
                // dd($invoice_agen, $invoice,$order_id,$order_id1);
                $nopol = null;
                $container = null;
                if ($data['order_id'][$i]) {
                    array_push($arr_order, $data['order_id'][$i]);
                    $order_id = $data['order_id'][$i];
                    $order = Order::find($order_id);
                    $id_job = $order->job . '-' . sprintf('%02d', $order->no_job);
                    $cont = $order->container;
                    $seal = $order->seal;
                    $shipment = $order->tarif->shipmentInfo->nama;
                    $pembayar = $order->tarif->customer->nama ?? '-';
                    $kapal = $order->jadwal_kapal->kapal->nama ?? '-';
                    $voyage = $order->jadwal_kapal->voyage ?? '-';
                    $customer = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->customer->nama;
                    $shipment_trucking = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->tipe;
                    $tujuan_trucking = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->tarif->tujuan->tujuanInfo->nama;
                    $name = str_replace('[1]', $id_job, $name);
                    $name = str_replace('[2]', $cont, $name);
                    $name = str_replace('[3]', $seal, $name);
                    $name = str_replace('[4]', $kapal, $name);
                    $name = str_replace('[5]', $voyage, $name);
                    $name = str_replace('[6]', $shipment, $name);
                    $name = str_replace('[7]', $pembayar, $name);
                    $name = str_replace('[8]', $customer, $name);
                    $name = str_replace('[9]', $shipment_trucking, $name);
                    $name = str_replace('[10]', $tujuan_trucking, $name);
                    $nopol = $order->nopol;
                    $container = $order->container;
                }
                if ($data['tipe'] == 'JNL') {
                    $nomor = sprintf('%02d', date('m', strtotime($data['created_at']))) . '-' . sprintf('%03d', $no) . '/' . ($this->sno == 'ALB' ? 'ALB/' : '') . date('y', strtotime($data['created_at']));
                } else {
                    $nomor = sprintf('%03d', $no) . '/' . $data['tipe'] . '-' . $this->sno . '/' . date('y', strtotime($data['created_at']));
                }
                if ($data['debit_coa_id'][$i] && $data['credit_coa_id'][$i]) {
                    $relasiDebit = $data['relasi'][$i] ?? ($invoice_agen === null && $invoice === null ? $nomor : $nomor);
                    $relasiCredit = $data['relasi'][$i] ?? ($invoice_agen === null && $invoice === null  ? $nomor : $nomor);
                    $jurnal_model->create([
                        'tipe' => $data['tipe'],
                        'invoice' => $invoice ?? null,
                        'invoice_agen' => $invoice_agen ?? null,
                        'nopol' => $nopol,
                        'container' => $container,
                        'coa_id' => $data['debit_coa_id'][$i],
                        'order_id' => $order_id ?? ($order_id1 ?? null),
                        'nomor' => $nomor,
                        'nama' => $name,
                        'debit' => $data['amount'][$i],
                        'created_at' => $data['created_at'],
                        'relasi' => $relasiDebit ?? $nomor,
                        'no_bg' => $no_bg,
                        'invoice_external' => $jurnal_external,
                        'no' => $no
                    ]);
                    $relasiCredit = $data['relasi'][$i] ?? ($invoice_agen === null && $invoice === null  ? $nomor : $nomor);
                    $jurnal_model->create([
                        'tipe' => $data['tipe'],
                        'invoice' => $invoice ?? null,
                        'invoice_agen' => $invoice_agen ?? null,
                        'nopol' => $nopol,
                        'container' => $container,
                        'coa_id' => $data['credit_coa_id'][$i],
                        'order_id' => $order_id ?? ($order_id1 ?? null),
                        'nomor' => $nomor,
                        'nama' => $name,
                        'credit' => $data['amount'][$i],
                        'created_at' => $data['created_at'],
                        'relasi' => $relasiCredit ?? $nomor,
                        'no_bg' => $no_bg,
                        'invoice_external' => $jurnal_external,
                        'no' => $no
                    ]);
                } else {
                    if ($data['debit_coa_id'][$i]) {
                        $relasiDebit = $data['relasi'][$i] ?? ($invoice_agen === null && $invoice === null ? $nomor : $nomor);
                        $jurnal_model->create([
                            'tipe' => $data['tipe'],
                            'invoice' => $invoice ?? null,
                            'invoice_agen' => $invoice_agen ?? null,
                            'nopol' => $nopol,
                            'container' => $container,
                            'coa_id' => $data['debit_coa_id'][$i],
                            'order_id' => $order_id ?? ($order_id1 ?? null),
                            'nomor' => $nomor,
                            'nama' => $name,
                            'debit' => $data['amount'][$i],
                            'created_at' => $data['created_at'],
                            'relasi' => $relasiDebit ?? $nomor,
                            'no_bg' => $no_bg,
                            'invoice_external' => $jurnal_external,
                            'no' => $no
                        ]);
                    }
                    if ($data['credit_coa_id'][$i]) {
                        $relasiCredit = $data['relasi'][$i] ?? ($invoice_agen === null && $invoice === null  ? $nomor : $nomor);
                        $jurnal_model->create([
                            'tipe' => $data['tipe'],
                            'invoice' => $invoice ?? null,
                            'invoice_agen' => $invoice_agen ?? null,
                            'nopol' => $nopol,
                            'container' => $container,
                            'coa_id' => $data['credit_coa_id'][$i],
                            'order_id' => $order_id ?? ($order_id1 ?? null),
                            'nomor' => $nomor,
                            'nama' => $name,
                            'credit' => $data['amount'][$i],
                            'created_at' => $data['created_at'],
                            'relasi' => $relasiCredit ?? $nomor,
                            'no_bg' => $no_bg,
                            'invoice_external' => $jurnal_external,
                            'no' => $no
                        ]);
                    }
                }
            }
        }

        $service = new SyncService();
        foreach ($arr_order as $id) {
            $sangu_sopir = Jurnal::where('order_id', $id)->where('nama', 'LIKE', 'SANGU SOPIR%')->where('debit', '>', 0)->sum('debit') ?? 0;
            $sangu_kuli = Jurnal::where('order_id', $id)->where('nama', 'LIKE', 'SANGU KULI%')->where('debit', '>', 0)->sum('debit') ?? 0;
            $uang_makan = Jurnal::where('order_id', $id)->where('nama', 'LIKE', 'UANG MAKAN%')->where('debit', '>', 0)->sum('debit') ?? 0;
            $solar = Jurnal::where('order_id', $id)->where('nama', 'LIKE', 'BIAYA TAMBAH SOLAR%')->where('debit', '>', 0)->sum('debit') ?? 0;
            $op = Jurnal::where('order_id', $id)->where('nama', 'LIKE', 'BIAYA OPERASIONAL TRUCKING%')->where('debit', '>', 0)->sum('debit') ?? 0;
            $cleaning = Jurnal::where('order_id', $id)->where('nama', 'LIKE', 'BIAYA CLEANING%')->where('debit', '>', 0)->sum('debit') ?? 0;
            $tally = Jurnal::where('order_id', $id)->where('nama', 'LIKE', 'BIAYA CHECKER%')->where('debit', '>', 0)->sum('debit') ?? 0;

            if ($sangu_sopir > 0) {
                OrderTrucking::where('order_id', $id)->update([
                    'sangu' => $sangu_sopir,
                ]);
            }
            if ($sangu_kuli > 0) {
                OrderTrucking::where('order_id', $id)->update([
                    'kuli' => $sangu_kuli,
                ]);
            }
            if ($solar > 0) {
                OrderTrucking::where('order_id', $id)->update([
                    'tambah_solar' => $solar,
                ]);
            }
            if ($tally > 0) {
                OrderTrucking::where('order_id', $id)->update([
                    'tally' => $tally,
                ]);
            }
            if ($uang_makan > 0) {
                OrderTrucking::where('order_id', $id)->update([
                    'uang_makan' => $uang_makan,
                ]);
            }
            if ($op > 0) {
                OrderTrucking::where('order_id', $id)->update([
                    'op' => $op,
                ]);
            }
            if ($cleaning > 0) {
                OrderTrucking::where('order_id', $id)->update([
                    'cleaning' => $cleaning,
                ]);
            }

            if ($sangu_sopir > 0 || $sangu_kuli > 0 || $solar > 0 || $tally > 0 || $uang_makan > 0 || $op > 0 || $cleaning > 0) {
                $order_trucking = OrderTrucking::where('order_id', $id)->first();
                if ($order_trucking) {
                    $service->trucking($order_trucking->id);
                }
            }
        }

        return back()->with('success', 'Data berhasil disimpan');
    }

    public function store_merge(Request $request)
    {

        $tujuan = Jurnal::where('nomor', $request->tujuan)->first();
        $jasakirim = JasaKirim::where('jurnal',$request->awal)->get();
        if ($jasakirim->isNotEmpty()) {
            // Update data JasaKirim terkait
            JasaKirim::where('jurnal', $request->awal)->update([
                'jurnal' => $tujuan->nomor,
            ]);
        }
        Jurnal::where('nomor', $request->awal)->update([
            'relasi' => $tujuan->nomor ?? $tujuan->relasi,
            'nomor' => $tujuan->nomor,
            'no' => $tujuan->no,
            'tipe' => $tujuan->tipe,
            'created_at' => $tujuan->created_at
        ]);

        return back()->with('success', 'Merge No. Jurnal berhasil');
    }

    public function store_trucking(Request $request)
    {
        $data = $request->all();
        $no = Jurnal::where('tipe', $data['tipe'])->whereYear('created_at', date('Y', strtotime($data['created_at'])))->max('no') + 1;
        if ($data['tipe'] == 'JNL') {
            $no = Jurnal::where('tipe', 'JNL')->whereMonth('created_at', date('m', strtotime($data['created_at'])))->whereYear('created_at', date('Y', strtotime($data['created_at'])))->max('no') + 1;
        }
        $jurnal_model = new Jurnal();
        if ($data['simpan'] == 'tampungan') {
            $jurnal_model = new JurnalTampungan();
        }

        $arr_order = array();
        for ($i = 0; $i < count($data['debit_coa_id']); $i++) {
            if ($data['name'][$i] && $data['amount'][$i]) {
                $name = $data['name'][$i];
                $order_id = null;
                $order_trucking = $data['invoice_trucking'][$i] ?? null;
                $order_vendor = $data['invoice_vendor'][$i] ?? null;
                $trucking = OrderTrucking::find($order_trucking);
                $vendor = OrderTrucking::find($order_vendor);
                $invoice = $trucking->invoice ?? null;
                $invoice_vendor = $vendor->invoice?? null;
                 // Mengambil ID saja
                $nopol = null;
                $container = null;
                $jurnal_external = $data['invoice_external'][$i] ?? null;
                if ($data['order_id'][$i]) {
                    $order_trucking = $data['order_id'][$i];
                    $order = OrderTrucking::find($data['order_id'][$i]);
                    $order_ids = $order->order_id;
                    $id_job = $order->order ? $order->order->job . '-' . sprintf('%02d', $order->order->no_job) : '-';
                    $cont = $order->container;
                    $seal = $order->seal;
                    $order_id = $order->id ?? null;
                    $invoice = $order->invoice ?? null;
                    $invoice_vendor = $order->invoice ?? null;
                    $shipment = $order->order ? $order->order->tarif->shipmentInfo->nama : '-';
                    $pembayar = $order->order ? $order->order->tarif->customer->nama : '-';
                    $kapal = $order->order ? $order->order->jadwal_kapal->kapal->nama : '-';
                    $voyage = $order->order ? $order->order->jadwal_kapal->voyage : '-';
                    $customer = $order->customer->nama;
                    $shipment_trucking = $order->tipe;
                    $tujuan_trucking = $order->tarif->tujuan->tujuanInfo->nama;
                    $name = str_replace('[1]', $id_job, $name);
                    $name = str_replace('[2]', $cont, $name);
                    $name = str_replace('[3]', $seal, $name);
                    $name = str_replace('[4]', $kapal, $name);
                    $name = str_replace('[5]', $voyage, $name);
                    $name = str_replace('[6]', $shipment, $name);
                    $name = str_replace('[7]', $pembayar, $name);
                    $name = str_replace('[8]', $customer, $name);
                    $name = str_replace('[9]', $shipment_trucking, $name);
                    $name = str_replace('[10]', $tujuan_trucking, $name);
                    $nopol = $order->kendaraan->nopol;
                    $container = $order->container;
                    array_push($arr_order, $order->id);
                }
                if ($data['tipe'] == 'JNL') {
                    $nomor = sprintf('%02d', date('m', strtotime($data['created_at']))) . '-' . sprintf('%03d', $no) . '/' . ($this->sno == 'ALB' ? 'ALB/' : '') . date('y', strtotime($data['created_at']));
                } else {
                    $nomor = sprintf('%03d', $no) . '/' . $data['tipe'] . '-' . $this->sno . '/' . date('y', strtotime($data['created_at']));
                }
                if ($data['debit_coa_id'][$i] && $data['credit_coa_id'][$i]) {
                    // Tentukan nilai relasi untuk debit dan kredit
                    $relasiDebit = $data['relasi'][$i] ?? ($invoice === null && $invoice_vendor === null ? $nomor : $nomor);
                    $relasiCredit = $data['relasi'][$i] ?? ($invoice === null && $invoice_vendor === null  ? $nomor : $nomor);
                    // Buat entri untuk debit terlebih dahulu
                    $jurnal_model->create([
                        'tipe' => $data['tipe'],
                        'coa_id' => $data['debit_coa_id'][$i],
                        'invoice_vendor' => !str_contains($invoice, 'RAS-LT') ? $invoice_vendor : null,
                        'invoice_trucking' => str_contains($invoice, 'RAS-LT') ? $invoice : null,
                        'nopol' => $nopol,
                        'container' => $container,
                        'order_trucking_id' => $order_trucking ?? $order_vendor,
                        'nomor' => $nomor,
                        'nama' => $name,
                        'order_id' => (($data['debit_coa_id'][$i] == 31 && $order_trucking) || $order_vendor) ? $order_ids : null,
                        'debit' => $data['amount'][$i],
                        'created_at' => $data['created_at'],
                        'relasi' => $relasiDebit,
                        'no' => $no,
                        'invoice_external' => $jurnal_external,
                    ]);

                    // Buat entri untuk kredit setelah debit
                    $jurnal_model->create([
                        'tipe' => $data['tipe'],
                        'coa_id' => $data['credit_coa_id'][$i],
                        'invoice_vendor' => !str_contains($invoice, 'RAS-LT') ? $invoice_vendor  : null,
                        'invoice_trucking' => str_contains($invoice, 'RAS-LT') ? $invoice  : null,
                        'nopol' => $nopol,
                        'order_id' => (($data['debit_coa_id'][$i] == 31 && $order_trucking) || $order_vendor) ? $order_ids : null,
                        'container' => $container,
                        'order_trucking_id' => $order_trucking ?? $order_vendor,
                        'nomor' => $nomor,
                        'nama' => $name,
                        'credit' => $data['amount'][$i],
                        'created_at' => $data['created_at'],
                        'relasi' => $relasiCredit,
                        'no' => $no,
                        'invoice_external' => $jurnal_external,
                    ]);
                } else {
                    if ($data['debit_coa_id'][$i]) {
                        // Tentukan nilai relasi untuk debit
                        $relasiDebit = $data['relasi'][$i] ?? ($invoice === null && $invoice_vendor === null ? $nomor : $nomor);

                        // Buat entri untuk debit
                        $jurnal_model->create([
                            'tipe' => $data['tipe'],
                            'coa_id' => $data['debit_coa_id'][$i],
                            'invoice_vendor' => !str_contains($invoice, 'RAS-LT') ? $invoice_vendor  : null,
                            'invoice_trucking' => str_contains($invoice, 'RAS-LT') ? $invoice : null,
                            'nopol' => $nopol,
                            'container' => $container,
                            'order_trucking_id' => $order_trucking ?? $order_vendor,
                            'nomor' => $nomor,
                            'nama' => $name,
                            'debit' => $data['amount'][$i],
                            'created_at' => $data['created_at'],
                            'relasi' => $relasiDebit,
                            'no' => $no,
                            'invoice_external' => $jurnal_external,
                        ]);
                    }

                    if ($data['credit_coa_id'][$i]) {
                        // Tentukan nilai relasi untuk kredit
                        $relasiCredit = $data['relasi'][$i] ?? ($invoice === null && $invoice_vendor === null ? $nomor : $nomor);

                        // Buat entri untuk kredit
                        $jurnal_model->create([
                            'tipe' => $data['tipe'],
                            'coa_id' => $data['credit_coa_id'][$i],
                            'invoice_vendor' => !str_contains($invoice, 'RAS-LT') ? $invoice_vendor  : null,
                            'invoice_trucking' => str_contains($invoice, 'RAS-LT') ? $invoice  : null,
                            'nopol' => $nopol,
                            'container' => $container,
                            'order_id' => $order_id,
                            'order_trucking_id' => $order_trucking ?? $order_vendor,
                            'nomor' => $nomor,
                            'nama' => $name,
                            'credit' => $data['amount'][$i],
                            'created_at' => $data['created_at'],
                            'relasi' => $relasiCredit,
                            'no' => $no,
                            'invoice_external' => $jurnal_external,
                        ]);
                    }
                }
            }
        }

        if ($data['simpan'] == 'tampungan') {
        } else {
            $service = new SyncService();
            foreach ($arr_order as $id) {
                $order = OrderTrucking::find($id);
                $sangu_sopir = Jurnal::where('order_trucking_id', $id)->where('nama', 'LIKE', 'SANGU SOPIR%')->where('debit', '>', 0)->sum('debit') ?? 0;
                $sangu_kuli = Jurnal::where('order_trucking_id', $id)->where('nama', 'LIKE', 'SANGU KULI%')->where('debit', '>', 0)->sum('debit') ?? 0;
                $uang_makan = Jurnal::where('order_trucking_id', $id)->where('nama', 'LIKE', 'UANG MAKAN%')->where('debit', '>', 0)->sum('debit') ?? 0;
                $solar = Jurnal::where('order_trucking_id', $id)->where('nama', 'LIKE', 'BIAYA TAMBAH SOLAR%')->where('debit', '>', 0)->sum('debit') ?? 0;
                $op = Jurnal::where('order_trucking_id', $id)->where('nama', 'LIKE', 'BIAYA OPERASIONAL TRUCKING%')->where('debit', '>', 0)->sum('debit') ?? 0;
                $cleaning = Jurnal::where('order_trucking_id', $id)->where('nama', 'LIKE', 'BIAYA CLEANING%')->where('debit', '>', 0)->sum('debit') ?? 0;
                $tally = Jurnal::where('order_trucking_id', $id)->where('nama', 'LIKE', 'BIAYA CHECKER%')->where('debit', '>', 0)->sum('debit') ?? 0;

                if ($sangu_sopir > 0) {
                    OrderTrucking::find($id)->update([
                        'sangu' => $sangu_sopir,
                    ]);
                }
                if ($sangu_kuli > 0) {
                    OrderTrucking::find($id)->update([
                        'kuli' => $sangu_kuli,
                    ]);
                }
                if ($solar > 0) {
                    OrderTrucking::find($id)->update([
                        'tambah_solar' => $solar,
                    ]);
                }
                if ($tally > 0) {
                    OrderTrucking::find($id)->update([
                        'tally' => $tally,
                    ]);
                }
                if ($uang_makan > 0) {
                    OrderTrucking::find($id)->update([
                        'uang_makan' => $uang_makan,
                    ]);
                }
                if ($op > 0) {
                    OrderTrucking::find($id)->update([
                        'op' => $op,
                    ]);
                }
                if ($cleaning > 0) {
                    OrderTrucking::find($id)->update([
                        'cleaning' => $cleaning,
                    ]);
                }

                if ($sangu_sopir > 0 || $sangu_kuli > 0 || $solar > 0 || $tally > 0 || $uang_makan > 0 || $op > 0 || $cleaning > 0) {
                    $service->trucking($id);
                }
            }
        }

        return back()->with('success', 'Data berhasil disimpan');
    }

    public function store_kolektif(Request $request)
    {
        $data = $request->all();
        $no = Jurnal::where('tipe', $data['tipe'])->whereYear('created_at', date('Y', strtotime($data['created_at'])))->max('no') + 1;
        if ($data['tipe'] == 'JNL') {
            $no = Jurnal::where('tipe', 'JNL')->whereMonth('created_at', date('m', strtotime($data['created_at'])))->whereYear('created_at', date('Y', strtotime($data['created_at'])))->max('no') + 1;
        }

        $jurnal_model = new Jurnal();
        if ($data['simpan'] == 'tampungan') {
            $jurnal_model = new JurnalTampungan();
        }

        for ($i = 0; $i < count($data['debit_coa_id']); $i++) {
            if ($data['name'][$i] && $data['amount'][$i] && $data['job'][$i] && $data['debit_coa_id'][$i] && $data['credit_coa_id'][$i]) {
                $name = $data['name'][$i];
                $jobs = Order::where('job', $data['job'][$i])->get();
                $amount = (int)$data['amount'][$i] / $jobs->count();
                $price = (int)((int)$data['amount'][$i] / $jobs->count());
                $selisih = (int)$data['amount'][$i] - ($price * $jobs->count());
                $invoice = null;
                $nopol = null;
                $container = null;
                foreach ($jobs as $idx => $order) {
                    $id_job = $order->job . '-' . sprintf('%02d', $order->no_job);
                    $cont = $order->container;
                    $seal = $order->seal;
                    $shipment = $order->tarif->shipmentInfo->nama;
                    $pembayar = $order->tarif->customer->nama ?? '-';
                    $kapal = $order->jadwal_kapal->kapal->nama ?? '-';
                    $voyage = $order->jadwal_kapal->voyage ?? '-';
                    $customer = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->customer->nama;
                    $shipment_trucking = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->tipe;
                    $tujuan_trucking = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->tarif->tujuan->tujuanInfo->nama;
                    $name = str_replace('[1]', $id_job, $name);
                    $name = str_replace('[2]', $cont, $name);
                    $name = str_replace('[3]', $seal, $name);
                    $name = str_replace('[4]', $kapal, $name);
                    $name = str_replace('[5]', $voyage, $name);
                    $name = str_replace('[6]', $shipment, $name);
                    $name = str_replace('[7]', $pembayar, $name);
                    $name = str_replace('[8]', $customer, $name);
                    $name = str_replace('[9]', $shipment_trucking, $name);
                    $name = str_replace('[10]', $tujuan_trucking, $name);
                    $nopol = $order->nopol;
                    $container = $order->container;

                    if ($data['tipe'] == 'JNL') {
                        $nomor = sprintf('%02d', date('m', strtotime($data['created_at']))) . '-' . sprintf('%03d', $no) . '/' . ($this->sno == 'ALB' ? 'ALB/' : '') . date('y', strtotime($data['created_at']));
                    } else {
                        $nomor = sprintf('%03d', $no) . '/' . $data['tipe'] . '-' . $this->sno . '/' . date('y', strtotime($data['created_at']));
                    }

                    if ($idx == 0) {
                        $amount = (int)((int)$data['amount'][$i] / $jobs->count()) + $selisih;
                    } else {
                        $amount = $price;
                    }

                    $jurnal_model->create([
                        'tipe' => $data['tipe'],
                        'coa_id' => $data['debit_coa_id'][$i],
                        'order_id' => $order->id,
                        'nomor' => $nomor,
                        'nama' => $name,
                        'debit' => $amount,
                        'invoice' => $invoice,
                        'relasi' => $nomor,
                        'nopol' => $nopol,
                        'container' => $container,
                        'created_at' => $data['created_at'],
                        'no' => $no
                    ]);
                    $jurnal_model->create([
                        'tipe' => $data['tipe'],
                        'coa_id' => $data['credit_coa_id'][$i],
                        'order_id' => $order->id,
                        'nomor' => $nomor,
                        'nama' => $name,
                        'relasi' => $nomor,
                        'credit' => $amount,
                        'invoice' => $invoice,
                        'nopol' => $nopol,
                        'container' => $container,
                        'created_at' => $data['created_at'],
                        'no' => $no
                    ]);
                }
            }
        }

        return back()->with('success', 'Data berhasil disimpan');
    }

    public function store_balik(Request $request)
    {
    
        $dataToInsert = []; // Kumpulkan data sebelum transaksi
        $r = 0;
        $jurnal = $request->jurnal;

        // Pastikan $jurnal adalah array sebelum mengurutkan
        if (is_array($jurnal)) {
            ksort($jurnal); // Mengurutkan array berdasarkan key (indeks)
        }
        // Loop pertama: Menyiapkan data
        foreach ($jurnal as $item) {
            if (!empty($item['nama'])) {
                $item['created_at'] = now();
                $item['jurnal_balik'] = empty($item['jurnal_balik']) ? null : $item['jurnal_balik'];
                $item['is_balik'] = 1;
                $item['relasi'] = $request->nomor;
                $item['nomor'] = $request->nomor;
                $item['no'] = $request->no;
                $item['tipe'] = $request->tipe;
    
                $dataToInsert[] = $item;
                $r++;
            }
        }
    
        if ($r == 0) {
            return back()->with('danger', 'Data gagal disimpan');
        }
    
      
    DB::beginTransaction();

    try {
        /**
         * Step 1: Insert semua data sekaligus
         */
        Jurnal::insert($dataToInsert);

        /**
         * Step 2: Update field jurnal_balik dari jurnal lama
         * Kita ambil ulang data yang baru dimasukkan berdasarkan nomor dan is_balik
         */
        $insertedJurnal = Jurnal::where('nomor', $request->nomor)
                                ->where('is_balik', 1)
                                ->orderBy('id', 'asc')
                                ->get();

        // Pastikan jumlah data sama agar tidak error saat mapping
        if ($insertedJurnal->count() !== count($dataToInsert)) {
            throw new \Exception('Jumlah data yang dimasukkan tidak sesuai.');
        }

        /**
         * Step 3: Mapping antara data lama dan data baru
         * Update jurnal lama berdasarkan jurnal_balik yang diinput
         */
        foreach ($insertedJurnal as $index => $j) {
            $original = $dataToInsert[$index];
            if (!empty($original['jurnal_balik'])) {
                Jurnal::where('id', $original['jurnal_balik'])
                      ->update(['jurnal_balik' => $j->id]);
            }
        }

        DB::commit();
        return redirect()
            ->route('jurnal.balik.create')
            ->with('success', 'Data berhasil disimpan');

    } catch (\Exception $e) {
        DB::rollBack();

        // Log error untuk debugging
        \Log::error('Gagal menyimpan jurnal balik', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return back()->with('danger', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
    }
}
    

    public function create()
    {
        return view('admin.jurnal.create');
    }

      public function kunci_jurnal()
    {
       $periodeJurnal = Jurnal::select(
        DB::raw("DATE_FORMAT(created_at, '%M %Y') as periode"),
        DB::raw("DATE_FORMAT(created_at, '%Y-%m') as periode_key"),
        DB::raw("MAX(kunci) as kunci")
    )
    ->whereNull('deleted_at')
    ->groupBy('periode','periode_key')
    ->orderByRaw("MIN(created_at) ASC")
    ->get();

        return view('admin.jurnal.kunci_jurnal', compact('periodeJurnal'));
    }

  public function toggle(Request $request)
{
    $periode = $request->periode; 

    // Hitung awal & akhir bulan
    $start = $periode . "-01 00:00:00";
    $end   = date("Y-m-t 23:59:59", strtotime($start));

    // Ambil satu jurnal untuk cek status awal
    $jurnalPertama = Jurnal::whereBetween('created_at', [$start, $end])->first();

    if (!$jurnalPertama) {
        return response()->json([
            'status' => 'error',
            'message' => 'Tidak ada jurnal pada periode ' . $periode
        ], 404);
    }

    // Toggle status baru
    $newStatus = $jurnalPertama->kunci == 1 ? 0 : 1;

    // Update semua di periode tsb
    $updated = Jurnal::whereBetween('created_at', [$start, $end])
        ->update(['kunci' => $newStatus]);

    return response()->json([
        'status' => 'success',
        'updatedRows' => $updated,
        'newStatus' => $newStatus,
        'message' => "Sebanyak $updated jurnal di periode $periode berhasil diubah menjadi " . ($newStatus ? 'Terkunci' : 'Belum Terkunci')
    ]);
}
    public function trucking()
    {
        return view('admin.jurnal.trucking');
    }

   public function edit()
{
    $jurnal = request('jurnal');
    $now = Carbon::now()->addMonths(1)->format('Y-m-d');
    $last = Carbon::now()->subMonths(9)->format('Y-m-d');

    $coa = Cache::remember('coa_active', 60, function () {
        return COA::where('is_active', 1)->orderBy('kode')->get();
    });

    $jurnalQuery = Jurnal::where('nomor', $jurnal);
    $count = $jurnalQuery->count();
    $data = $jurnalQuery->first();
    $deb = $jurnalQuery->sum('debit');
    $cre = $jurnalQuery->sum('credit');

    $orderSelect = ['id', 'no_job', 'job', 'seal', 'invoice'];

    $orders = Cache::remember("orders_all_$last", 60, function () use ($last, $now, $orderSelect) {
        return Order::whereBetween('created_at', [$last, $now])
            ->orderBy('job')->orderBy('no_job')
            ->select($orderSelect)
            ->get();
    });

    $orders_expdc = Cache::remember("orders_expdc_$last", 60, function () use ($last, $now, $orderSelect) {
        return Order::whereBetween('created_at', [$last, $now])
            ->whereNotNull('invoice')
            ->orderBy('job')->orderBy('no_job')
            ->select($orderSelect)
            ->get();
    });

    $orders_agen = Cache::remember("orders_agen_$last", 60, function () use ($last, $now) {
        return Order::whereBetween('created_at', [$last, $now])
            ->whereNotNull('invoice_agen')
            ->orderBy('job')->orderBy('no_job')
            ->select(['id', 'no_job', 'job', 'seal', 'invoice_agen'])
            ->get();
    });

    $orders_trucking1 = Cache::remember("orders_trucking1_$last", 60, function () use ($last, $now) {
        return OrderTrucking::whereBetween('created_at', [$last, $now])
            ->select('container', 'seal', 'id', 'invoice')
            ->orderBy('container')
            ->get();
    });

    $tipe = 'xpdc';
    $orders_trucking = collect();
    $orders_vendor = collect();

    $invx = Cache::remember('invx_unique', 60, function () {
        return Jurnal::whereNotNull('invoice_external')->orderBy('invoice_external')->pluck('invoice_external')->unique()->toArray();
    });

    if ($data->order_trucking_id) {
        $tipe = 'trucking';
        $orders_trucking = $orders_trucking1->filter(fn($q) => str_contains($q->invoice, 'RAS-LT'))->values();
        $orders_vendor = $orders_trucking1->filter(fn($q) => !str_contains($q->invoice, 'RAS-LT'))->values();
    } elseif (is_null($data->order_trucking_id) && is_null($data->order_id)) {
        $tipe = 'lain-lain';
        $orders_trucking = $orders_trucking1->filter(fn($q) => str_contains($q->invoice, 'RAS-LT'))->values();
        $orders_vendor = $orders_trucking1->filter(fn($q) => !str_contains($q->invoice, 'RAS-LT'))->values();
    }

    $jur = $data;

    $bgs = Cache::remember('bgs_unique', 60, function () {
        return Jurnal::whereNotNull('no_bg')->orderBy('no_bg')->pluck('no_bg')->unique()->toArray();
    });

    $last_relasi = Carbon::now()->subMonths(5)->format('Y-m-d');
    $relasi = Cache::remember("relasi_$last_relasi", 60, function () use ($last_relasi) {
        return Jurnal::where('created_at', '>=', $last_relasi)->orderBy('nomor')->pluck('nomor')->unique()->toArray();
    });

$voucherDeb = $jurnalQuery
    ->whereIn('coa_id', [16, 45, 175])
    ->sum('debit');

$voucherCre = $jurnalQuery
    ->whereIn('coa_id', [16, 45, 175])
    ->sum('credit');

$voucher = abs($voucherDeb - $voucherCre);


    return view('admin.jurnal.new_edit', compact(
        'orders', 'orders_expdc', 'orders_agen',
        'orders_trucking', 'orders_trucking1', 'orders_vendor',
        'invx', 'bgs', 'data', 'relasi', 'coa', 'tipe',
        'jur', 'voucher', 'deb', 'cre', 'count'
    ));
}



    public function editCoa(Request $request) {
    $nomor = $request->query('jurnal');
    $data = Jurnal::with('order')->where('nomor',$nomor)->get();

    $coa = COA::where('is_active', 1)->orderBy('kode')->get();
    $now = Carbon::now()->addMonths(1)->format('Y-m-d');
    $last = Carbon::now()->subMonths(9)->format('Y-m-d');

    return view('admin.jurnal.edit-coa', compact('coa','data'));
}

public function simpanKode(Request $request)
{
    $data = $request->input('data');
    foreach ($data as $row) {
        DB::table('jurnal')
            ->where('id', $row['id'])
            ->update(['kode' => $row['kode']]); // pastikan kolom 'kode' ada
    }

    return response()->json(['status' => 'success']);
}


// public function buatCode(Request $request) {
//     $nomor = $request->query('jurnal');
//     $data = Jurnal::with('order')->where('nomor',$nomor)->get();

//     $coa = COA::where('is_active', 1)->orderBy('kode')->get();
//     $now = Carbon::now()->addMonths(1)->format('Y-m-d');
//     $last = Carbon::now()->subMonths(9)->format('Y-m-d');

//     return view('admin.jurnal.buat-code-balik', compact('coa','data'));
// }

public function updateCoa(Request $request, $jurnal_id)
{
    $data = $request->input('jurnal'); // Format: [id => ['coa_id' => ...], ...]

    if (!$data || !is_array($data)) {
        return back()->with('error', 'Data COA tidak valid.');
    }

    $logChanges = [];

    foreach ($data as $id => $value) {
        $jurnal = Jurnal::find($id);
        if ($jurnal) {
            $oldCoaId = $jurnal->coa_id;
            $newCoaId = $value['coa_id'] ?? null;

            if ($oldCoaId != $newCoaId) {
                $oldCoa = Coa::find($oldCoaId);
                $newCoa = Coa::find($newCoaId);

                $logChanges[] = [
                    'id' => $jurnal->id,
                    'nomor' => $jurnal->nomor,
                    'coa_sebelumnya' => $oldCoa ? "{$oldCoa->kode} - {$oldCoa->nama}" : 'null',
                    'coa_baru' => $newCoa ? "{$newCoa->kode} - {$newCoa->nama}" : 'null',
                ];

                $jurnal->coa_id = $newCoaId;
                $jurnal->save();
            }
        }
    }

    if (empty($logChanges)) {
        return back()->with('info', 'Tidak ada perubahan COA yang dilakukan.');
    }

    $logText = collect($logChanges)->map(function ($item) {
        return "ID: {$item['id']}, Nomor: {$item['nomor']}, COA Lama: {$item['coa_sebelumnya']}, COA Baru: {$item['coa_baru']}";
    })->implode('<br>');

    return back()->with('success', "Berhasil update COA berikut:<br>{$logText}");
}



public function editOne(Jurnal $jurnal)
{
    $now = Carbon::now()->addMonths(1)->format('Y-m-d');
    $last = Carbon::now()->subMonths(12)->format('Y-m-d');

    $coa = Cache::remember('coa_active', 60, function () {
        return COA::where('is_active', 1)->orderBy('kode')->get();
    });

    $orderBaseKey = 'order_base_' . $last;
    $orders = Cache::remember($orderBaseKey . '_all', 60, function () use ($last, $now) {
        return Order::whereBetween('created_at', [$last, $now])
            ->orderBy('job')->orderBy('no_job')
            ->select('id', 'no_job', 'job', 'seal', 'invoice')
            ->get();
    });

    $orders_expdc = Cache::remember($orderBaseKey . '_expdc', 60, function () use ($last, $now) {
        return Order::whereBetween('created_at', [$last, $now])
            ->whereNotNull('invoice')
            ->orderBy('job')->orderBy('no_job')
            ->select('id', 'no_job', 'job', 'seal', 'invoice')
            ->get();
    });

    $orders_agen = Cache::remember($orderBaseKey . '_agen', 60, function () use ($last, $now) {
        return Order::whereBetween('created_at', [$last, $now])
            ->whereNotNull('invoice_agen')
            ->orderBy('job')->orderBy('no_job')
            ->select('id', 'no_job', 'job', 'seal', 'invoice_agen')
            ->get();
    });

    $orders_trucking1 = Cache::remember("orders_trucking1_$last", 60, function () use ($last, $now) {
        return OrderTrucking::whereBetween('created_at', [$last, $now])
            ->select('container', 'seal', 'id', 'invoice')
            ->orderBy('container')
            ->get();
    });

    $invx = Cache::remember('invx_unique', 60, function () {
        return Jurnal::whereNotNull('invoice_external')
    ->distinct()
    ->orderBy('invoice_external')
    ->pluck('invoice_external')
    ->toArray();

    });

    // Filter trucking/vendor
    $orders_trucking = collect();
    $orders_vendor = collect();
    $tipe = 'xpdc';

    if ($jurnal->order_trucking_id) {
        $tipe = 'trucking';
    } elseif (is_null($jurnal->order_trucking_id) && is_null($jurnal->order_id)) {
        $tipe = 'lain-lain';
    }

    if ($tipe !== 'xpdc') {
        $orders_trucking = $orders_trucking1->filter(fn($row) => str_contains($row->invoice, 'RAS-LT'))->values();
        $orders_vendor = $orders_trucking1->filter(fn($row) => !str_contains($row->invoice, 'RAS-LT'))->values();
    }

    $bgs = Cache::remember('jurnal_bgs', 60, function () {
        return Jurnal::whereNotNull('no_bg')->orderBy('no_bg')->pluck('no_bg')->unique()->toArray();
    });

    $last_relasi = Carbon::now()->subMonths(5)->format('Y-m-d');
    $relasi = Cache::remember("jurnal_relasi_$last_relasi", 60, function () use ($last_relasi) {
        return Jurnal::where('created_at', '>=', $last_relasi)
            ->orderBy('nomor')
            ->pluck('nomor')
            ->unique()
            ->toArray();
    });

    return view('admin.jurnal.form_edit', compact(
        'orders', 'invx', 'jurnal', 'orders_trucking1',
        'orders_expdc', 'orders_agen', 'orders_trucking', 'orders_vendor',
        'coa', 'tipe', 'bgs', 'relasi'
    ));
}




    public function updateOne(Request $request, Jurnal $jurnal)
    {
        $data = $request->all();
        $data['relasi'] = $data['relasi'] ?? $request->relasi1;
        $data['invoice'] = $jurnal->invoice;
        $data['invoice_agen'] = $jurnal->invoice_agen;
        $data['invoice_trucking'] = $jurnal->invoice_trucking;
        $data['invoice_vendor'] = $jurnal->invoice_vendor;
        $data['order_trucking_id'] = $jurnal->order_trucking_id;
        $data['order_id'] = $jurnal->order_id;
        $data['nopol'] = $jurnal->nopol;
        $data['container'] = $jurnal->container;
        if (!empty($data['invoice_external']) || !empty($data['no_bg'])) {
            $data['no_bg'] = $data['no_bg'] ?? null;
            $data['invoice_external'] = $data['invoice_external'] ?? null;
        }
        else if (!empty($data['inv_expdc'])) {
            $name = $data['nama'];
            $order_expdc = $data['inv_expdc'] ?? null;
            $order = Order::find($order_expdc);
            $id_job = $order->job . '-' . sprintf('%02d', $order->no_job);
            $cont = $order->container;
            $seal = $order->seal;
            $shipment = $order->tarif->shipmentInfo->nama;
            $pembayar = $order->tarif->customer->nama ?? '-';
            $kapal = $order->jadwal_kapal->kapal->nama ?? '-';
            $voyage = $order->jadwal_kapal->voyage ?? '-';
            $customer = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->customer->nama;
            $shipment_trucking = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->tipe;
            $tujuan_trucking = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->tarif->tujuan->tujuanInfo->nama;
            $name = str_replace('[1]', $id_job, $name);
            $name = str_replace('[2]', $cont, $name);
            $name = str_replace('[3]', $seal, $name);
            $name = str_replace('[4]', $kapal, $name);
            $name = str_replace('[5]', $voyage, $name);
            $name = str_replace('[6]', $shipment, $name);
            $name = str_replace('[7]', $pembayar, $name);
            $name = str_replace('[8]', $customer, $name);
            $name = str_replace('[9]', $shipment_trucking, $name);
            $name = str_replace('[10]', $tujuan_trucking, $name);
            $data['invoice'] = $order->invoice;
            $data['invoice_agen'] = null;
            $data['invoice_trucking'] = null;
            $data['invoice_vendor'] = null;
            $data['order_trucking_id'] = null;
            $data['no_bg'] = null;
            $data['order_id'] =$order->id;
            $data['nopol'] = $order->nopol ?? null;
            $data['container'] = $order->container ?? null;
            $data['nama'] = $name;
        }
        else if (!empty($data['inv_agen'])) {
            $name = $data['nama'];
            $order_agen = $data['inv_agen'] ?? null;
            $order1 = Order::find($order_agen);
            $id_job = $order1->job . '-' . sprintf('%02d', $order1->no_job);
            $cont = $order1->container;
            $seal = $order1->seal;
            $shipment = $order1->tarif->shipmentInfo->nama;
            $pembayar = $order1->tarif->customer->nama ?? '-';
            $kapal = $order1->jadwal_kapal->kapal->nama ?? '-';
            $voyage = $order1->jadwal_kapal->voyage ?? '-';
            $customer = is_null($order1->truckingInfo) ? '-' : $order1->truckingInfo->customer->nama;
            $shipment_trucking = is_null($order1->truckingInfo) ? '-' : $order1->truckingInfo->tipe;
            $tujuan_trucking = is_null($order1->truckingInfo) ? '-' : $order1->truckingInfo->tarif->tujuan->tujuanInfo->nama;
            $name = str_replace('[1]', $id_job, $name);
            $name = str_replace('[2]', $cont, $name);
            $name = str_replace('[3]', $seal, $name);
            $name = str_replace('[4]', $kapal, $name);
            $name = str_replace('[5]', $voyage, $name);
            $name = str_replace('[6]', $shipment, $name);
            $name = str_replace('[7]', $pembayar, $name);
            $name = str_replace('[8]', $customer, $name);
            $name = str_replace('[9]', $shipment_trucking, $name);
            $name = str_replace('[10]', $tujuan_trucking, $name);
            $data['invoice'] = null;
            $data['invoice_agen'] = $order1->invoice_agen ?? null;
            $data['invoice_trucking'] = null;
            $data['no_bg'] = null;
            $data['invoice_vendor'] = null;
            $data['order_trucking_id'] = null;
            $data['order_id'] =$order_agen;
            $data['nopol'] = $order1->nopol ?? null;
            $data['container'] = $order1->container ?? null;
            $data['nama'] = $name;
        }
        else if (!empty($data['inv_trucking'])) {
            $name = $data['nama'];
            $order_expdc = $data['inv_trucking'] ?? null;
            $order = OrderTrucking::find($order_expdc);
            $id_job = $order->order ? $order->order->job . '-' . sprintf('%02d', $order->order->no_job) : '-';
            $cont = $order->container;
            $seal = $order->seal;
            $order_id = $order->order ? $order->order->id : null;
            $shipment = $order->order ? $order->order->tarif->shipmentInfo->nama : '-';
            $pembayar = $order->order ? $order->order->tarif->customer->nama : '-';
            $kapal = $order->order ? $order->order->jadwal_kapal->kapal->nama : '-';
            $voyage = $order->order ? $order->order->jadwal_kapal->voyage : '-';
            $customer = $order->customer->nama;
            $shipment_trucking = $order->tipe;
            $tujuan_trucking = $order->tarif->tujuan->tujuanInfo->nama;
            $name = str_replace('[1]', $id_job, $name);
            $name = str_replace('[2]', $cont, $name);
            $name = str_replace('[3]', $seal, $name);
            $name = str_replace('[4]', $kapal, $name);
            $name = str_replace('[5]', $voyage, $name);
            $name = str_replace('[6]', $shipment, $name);
            $name = str_replace('[7]', $pembayar, $name);
            $name = str_replace('[8]', $customer, $name);
            $name = str_replace('[9]', $shipment_trucking, $name);
            $name = str_replace('[10]', $tujuan_trucking, $name);
            $data['invoice_trucking'] = $order->invoice ?? null;
            $data['order_id'] = null;
            $data['nopol'] = $order->kendaraan->nopol;
            $data['invoice'] = null;
            $data['no_bg'] = null;
            $data['invoice_vendor'] = null;
            $data['invoice_agen'] = null;
            $data['order_trucking_id'] = $order_expdc;
            $data['container'] = $order->container;
            $data['nama'] = $name;
        }
        else if(!empty($data['inv_vendor'])) {
            $name = $data['nama'];
            $order_vendor = $data['inv_vendor'] ?? null;
            $order = OrderTrucking::find($order_vendor);
            $id_job = $order->order ? $order->order->job . '-' . sprintf('%02d', $order->order->no_job) : '-';
            $cont = $order->container;
            $seal = $order->seal;
            $order_id = $order->order ? $order->order->id : null;
            $shipment = $order->order ? $order->order->tarif->shipmentInfo->nama : '-';
            $pembayar = $order->order ? $order->order->tarif->customer->nama : '-';
            $kapal = $order->order ? $order->order->jadwal_kapal->kapal->nama : '-';
            $voyage = $order->order ? $order->order->jadwal_kapal->voyage : '-';
            $customer = $order->customer->nama;
            $shipment_trucking = $order->tipe;
            $tujuan_trucking = $order->tarif->tujuan->tujuanInfo->nama;
            $name = str_replace('[1]', $id_job, $name);
            $name = str_replace('[2]', $cont, $name);
            $name = str_replace('[3]', $seal, $name);
            $name = str_replace('[4]', $kapal, $name);
            $name = str_replace('[5]', $voyage, $name);
            $name = str_replace('[6]', $shipment, $name);
            $name = str_replace('[7]', $pembayar, $name);
            $name = str_replace('[8]', $customer, $name);
            $name = str_replace('[9]', $shipment_trucking, $name);
            $name = str_replace('[10]', $tujuan_trucking, $name);
            $data['invoice_vendor'] = $order->invoice ?? null;
            $data['order_trucking_id'] = $order_vendor;
            $data['order_id'] = null;
            $data['no_bg'] = null;
            $data['invoice'] = null;
            $data['invoice_trucking'] = null;
            $data['invoice_agen'] = null;
            $data['nopol'] = $order->kendaraan->nopol ?? null;
            $data['container'] = $order->container ?? null;
            $data['nama'] = $name;
        }
        else if (!empty($data['trucking'])) {
            $name = $data['nama'];
            $order_trucking = $data['trucking'] ?? null;
            $order = OrderTrucking::find($order_trucking);
            $id_job = $order->order ? $order->order->job . '-' . sprintf('%02d', $order->order->no_job) : '-';
            $cont = $order->container;
            $seal = $order->seal;
            $order_id = $order->order ? $order->order->id : null;
            $shipment = $order->order ? $order->order->tarif->shipmentInfo->nama : '-';
            $pembayar = $order->order ? $order->order->tarif->customer->nama : '-';
            $kapal = $order->order ? $order->order->jadwal_kapal->kapal->nama : '-';
            $voyage = $order->order ? $order->order->jadwal_kapal->voyage : '-';
            $customer = $order->customer->nama;
            $shipment_trucking = $order->tipe;
            $invoice = $order->invoice;
            $tujuan_trucking = $order->tarif->tujuan->tujuanInfo->nama;
            $name = str_replace('[1]', $id_job, $name);
            $name = str_replace('[2]', $cont, $name);
            $name = str_replace('[3]', $seal, $name);
            $name = str_replace('[4]', $kapal, $name);
            $name = str_replace('[5]', $voyage, $name);
            $name = str_replace('[6]', $shipment, $name);
            $name = str_replace('[7]', $pembayar, $name);
            $name = str_replace('[8]', $customer, $name);
            $name = str_replace('[9]', $shipment_trucking, $name);
            $name = str_replace('[10]', $tujuan_trucking, $name);
            // $data['invoice_vendor'] = !str_contains($invoice, 'RAS-LT') ? $invoice : null;
            // $data['invoice_trucking'] = str_contains($invoice, 'RAS-LT') ? $invoice : null;
            $data['invoice_vendor'] = $jurnal->invoice_vendor;
            if (!empty($data['inv_vendor'])) {
                    if (!str_contains($order->invoice, 'RAS-LT')) {
                        $data['invoice_vendor'] = $order->invoice;
                    } else {
                        $data['invoice_vendor'] = null;
                    }
            }
            $data['invoice_trucking'] = $jurnal->invoice_trucking;
            if (!empty($data['inv_trucking'])) {
                    if (str_contains($order->invoice, 'RAS-LT')) {
                        $data['invoice_trucking'] = $order->invoice;
                    } else {
                        $data['invoice_trucking'] = null;
                    }
            }
            $data['order_trucking_id'] = $order_trucking;
            $data['order_id'] = $order->order_id;
            $data['no_bg'] = null;
            $data['invoice'] = null;
            $data['invoice_agen'] = null;
            $data['nopol'] = $order->kendaraan->nopol ?? null;
            $data['container'] = $order->container ?? null;
            $data['nama'] = $name;
        }

         else if (!empty($data['job'])) {
            $name = $data['nama'];
            $order_job = $data['job'] ?? null;
            $order = Order::find($order_job);
            $id_job = $order->job . '-' . sprintf('%02d', $order->no_job);
            $cont = $order->container;
            $seal = $order->seal;
            $shipment = $order->tarif->shipmentInfo->nama;
            $pembayar = $order->tarif->customer->nama ?? '-';
            $kapal = $order->jadwal_kapal->kapal->nama ?? '-';
            $voyage = $order->jadwal_kapal->voyage ?? '-';
            $customer = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->customer->nama;
            $shipment_trucking = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->tipe;
            $tujuan_trucking = is_null($order->truckingInfo) ? '-' : $order->truckingInfo->tarif->tujuan->tujuanInfo->nama;
            $name = str_replace('[1]', $id_job, $name);
            $name = str_replace('[2]', $cont, $name);
            $name = str_replace('[3]', $seal, $name);
            $name = str_replace('[4]', $kapal, $name);
            $name = str_replace('[5]', $voyage, $name);
            $name = str_replace('[6]', $shipment, $name);
            $name = str_replace('[7]', $pembayar, $name);
            $name = str_replace('[8]', $customer, $name);
            $name = str_replace('[9]', $shipment_trucking, $name);
            $name = str_replace('[10]', $tujuan_trucking, $name);
            $data['invoice'] = $jurnal->invoice;
            if (!empty($data['inv_expdc'])){
                $data['invoice'] = $order->invoice;
            }
            $data['no_bg'] = $jurnal->no_bg;
            $data['invoice_agen'] = $jurnal->invoice_agen;
            if (!empty($data['inv_agen'])){
                $data['invoice_agen'] = $order->invoice_agen;
            }
            $data['invoice_trucking'] = $jurnal->invoice_trucking;
            $data['invoice_vendor'] = $jurnal->invoice_vendor;
            $data['order_trucking_id'] = $jurnal->order_trucking_id;
            $data['order_id'] =$order_job;
            $data['nopol'] = $jurnal->nopol;
            $data['container'] = $jurnal->container;
            $data['nama'] = $name;
        }
        
        $jurnal->update($data);
        return back()->with('success', 'Data berhasil disimpan!');
    }

    public function update(Jurnal $jurnal, Request $request)
    {
        $tipe = Jurnal::where('nomor', $jurnal->nomor)->first()->tipe;
        $no = Jurnal::where('nomor', $jurnal->nomor)->first()->no;
        if ($tipe == 'JNL') {
            $nomor = sprintf('%02d', date('m', strtotime($request->created_at))) . '-' . sprintf('%03d', $no) . '/' . ($this->sno == 'ALB' ? 'ALB/' : '') . date('y', strtotime($request->created_at));
        } else {
            $nomor = sprintf('%03d', $no) . '/' . $tipe . '-' . $this->sno . '/' . date('y', strtotime($request->created_at));
        }
        Jurnal::where('nomor', $jurnal->nomor)->update([
            'created_at' => $request->created_at,
            'nomor' => $nomor
        ]);

        return redirect()->route('jurnal.edit', ['jurnal' => $nomor])->with('success', 'Data berhasil diupdate');
    }

    public function destroy(Jurnal $jurnal)
    {
        $jurnal->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }

    public function import(Request $request)
    {
        Excel::import(new JurnalImport, $request->file);

        return back()->with('success', 'All good!');
    }

    public function neraca()
    {
        $month = request('month') ?? date('m');
        $year = request('year') ?? date('Y');
        return view('admin.jurnal.neraca', compact('month', 'year'));
    }

    public function laba_rugi()
    {
        return view('admin.jurnal.laba_rugi');
    }

    public function buku_besar()
    {
        $coas = COA::orderBy('kode')->get(['id', 'nama', 'kode']);
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $coa_id = request('coa_id') ?? 45;
        $coa = COA::find($coa_id);
        $year = request('year') ?? date('Y');
        $month = request('month') ?? date('m');
        $tipe = 'D';
        if (substr($coa->kode, 0, 1) == '2' || substr($coa->kode, 0, 1) == '3' || substr($coa->kode, 0, 1) == '5') {
            $tipe = 'C';
        }
        $saldo = array();
        foreach ($months as $idx => $item) {
            $bln = $idx + 1;
            $c = new Carbon($year . '-' . sprintf('%02d', $bln) . '-01');
            $now = $c->startOfMonth()->format('Y-m-d');
            $last = Carbon::parse($year . '-' . sprintf('%02d', $bln) . '-01')->endOfMonth()->format('Y-m-d 23:59:59');
            $start = $c->subMonth()->startOfMonth()->format('Y-m-d');
            // $start = '2022-12-01';
            $des = $c->endOfMonth()->format('Y-m-d');
            // dd($start,$des,$last);
            if ($idx == 0) {
                if ($tipe == 'D') {
                    $saldo_awal = Jurnal::where('coa_id', $coa_id)->whereBetween('created_at', ['2022-12-01', $des])->sum('debit') - Jurnal::where('coa_id', $coa_id)->whereBetween('created_at', ['2022-12-01', $des])->sum('credit');
                } else {
                    $saldo_awal = Jurnal::where('coa_id', $coa_id)->whereBetween('created_at', ['2022-12-01', $des])->sum('credit') - Jurnal::where('coa_id', $coa_id)->whereBetween('created_at', ['2022-12-01', $des])->sum('debit');
                }
            } else {
                // if ($tipe=='D') {
                //     $saldo_awal = Jurnal::where('coa_id',$coa_id)->whereBetween('created_at',[$start,$last])->sum('debit') - Jurnal::where('coa_id',$coa_id)->whereBetween('created_at',[$start,$last])->sum('credit');
                // } else {
                //     $saldo_awal = Jurnal::where('coa_id',$coa_id)->whereBetween('created_at',[$start,$last])->sum('credit') - Jurnal::where('coa_id',$coa_id)->whereBetween('created_at',[$start,$last])->sum('debit');
                // }
                // if($saldo_awal>0){
                // }
                $start = $now;
                $saldo_awal =  $saldo['saldo_akhir'][$idx - 1];
                // dd($start,$last,$saldo_awal);
            }
            $debit = Jurnal::where('coa_id', $coa_id)->whereBetween('created_at', [$now, $last])->sum('debit');
            $credit = Jurnal::where('coa_id', $coa_id)->whereBetween('created_at', [$now, $last])->sum('credit');
            $saldo['saldo_awal'][$idx] = $saldo_awal;
            if ($tipe == 'D') {
                $saldo['saldo_akhir'][$idx] = ($debit + $saldo_awal) - $credit;
            } else {
                $saldo['saldo_akhir'][$idx] = ($credit + $saldo_awal) - $debit;
            }
            $saldo['debit'][$idx] = $debit;
            $saldo['credit'][$idx] = $credit;
        }
        $m = (int)$month;
        $saldo_awal = $saldo['saldo_awal'][$m - 1];
        $search = null;
        $data = Jurnal::join('coa', 'coa.id', '=', 'jurnal.coa_id')
            ->leftJoin('order', 'order.id', '=', 'jurnal.order_id')
            ->whereMonth('jurnal.created_at', $month)
            ->whereYear('jurnal.created_at', $year)
            ->where('jurnal.coa_id', $coa_id)
            ->select('jurnal.*')
            ->orderBy('jurnal.created_at')
            ->orderBy('jurnal.tipe')
            ->orderBy('jurnal.nomor', 'asc')
            ->get();
        $dateExport = null;
        $job_sync = DB::table('jobs')->count();
        if (Storage::disk('public')->exists('buku-besar.xlsx')) {
            $lastModif = Storage::disk('public')->lastModified('buku-besar.xlsx');
            $dateExport = date('d/m/Y H:i:s', $lastModif);
        }
        return view('admin.jurnal.buku_besar', compact('coas', 'months', 'month', 'saldo', 'saldo_awal', 'coa', 'coa_id', 'data', 'tipe', 'year', 'dateExport', 'job_sync'));
    }

    public function buku_besar_pembantu()
    {
        $year = request('year') ?? date('Y');
        $month = request('month') ?? date('m');
        $coa_id = request('coa_id') ?? 46;
        $subjek = request('subjek') ?? 'customer_xpdc';
        $coa = COA::find($coa_id);
        $coas = COA::orderBy('kode')->get(['id', 'nama', 'kode']);
        $tipe = 'D';
        if (substr($coa->kode, 0, 1) == '2' || substr($coa->kode, 0, 1) == '3' || substr($coa->kode, 0, 1) == '5') {
            $tipe = 'C';
        }
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        return view('admin.jurnal.buku_besar_pembantu', compact('months', 'coas', 'year', 'month', 'coa_id', 'tipe', 'subjek'));
    }

    public function bb_pembantu()
    {
        // Ambil parameter dari request atau set default jika tidak ada
        $year = request('year') ?? date('Y');
        $month = request('month') ?? date('m');
        $coa_id = request('coa_id') ?? 46;
        $subjek = request('subjek') ?? 'customer_xpdc';
        $startDate = '2022-01-01';
        $endDate = Carbon::create($year, $month)->endOfMonth()->endOfDay();
        // Cek apakah COA ditemukan
        $coa = COA::find($coa_id);
        if (!$coa) {
            return back()->with('error', 'COA tidak ditemukan');
        }

        // Ambil semua COA yang tersedia
        $coas = COA::orderBy('kode')->get(['id', 'nama', 'kode']);

        // Tentukan tipe berdasarkan kode COA
        $tipe = in_array(substr($coa->kode, 0, 1), ['2', '3', '5']) ? 'C' : 'D';
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        if ($subjek == 'customer_xpdc') {
            // Cache daftar customer
            $customer = Cache::remember('customer_list', 60, function () {
                return Customer::pluck('nama', 'id');
            });
            // Cache daftar tarif
            $tarif = Cache::remember("tarif_list_{$customer->keys()->implode('_')}", 60, function () use ($customer) {
                return Tarif::whereIn('customer_id', $customer->keys())->pluck('id');
            });
            // Cache daftar order
            $order = Cache::remember("order_list_{$tarif->implode('_')}", 60, function () use ($tarif) {
                return Order::whereIn('tarif_id', $tarif)->pluck('id');
            });
            // Pastikan $order tidak kosong sebelum query transaksi
            $transaksi = $order->isNotEmpty()
                ? Transaksi::whereIn('order_id', $order)->pluck('pph', 'order_id')
                : collect();
            
            // Cache jurnal berdasarkan kriteria, dengan pengecekan agar cache tetap efisien
            $jurnal = Cache::remember("jurnal_{$coa_id}_{$startDate}_{$endDate}_" . implode('_', $order->toArray()), 60, function () use ($coa_id, $order, $startDate, $endDate) {
                return $order->isNotEmpty()
                    ? Jurnal::where('coa_id', $coa_id)
                        ->whereIn('order_id', $order)
                        ->whereNull('order_trucking_id')
                        ->whereNull('invoice_trucking')
                        ->whereNull('invoice_vendor')
                        ->whereNull('invoice_agen')
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->whereNotNull('invoice')
                        ->get(['order_id', 'debit', 'credit'])
                    : collect();
            });
            
            // Proses data untuk hasil akhir
            $finalData = $jurnal->map(function ($item) use ($customer) {
                return [
                    'customer_name' => $customer[$item->order->tarif->customer_id] ?? 'Unknown',
                    'debit' => $item->debit,
                    'credit' => $item->credit,
                ];
            });
            
            // Cek kondisi untuk perhitungan PPH
            if ($subjek == 'customer_xpdc' && $coa_id == 46) {
                $orderIds = $jurnal->pluck('order_id')->unique();
            
                $data['pph'] = $orderIds
                    ->map(fn($id) => isset($transaksi[$id]) ? round($transaksi[$id]) : null)
                    ->filter()
                    ->implode('<br>');
            
                // Kurangi debit dengan PPH jika ada
                $data['debit'] = ((int) ($data['debit'] ?? 0)) - ((int) ($data['pph'] ?? 0));
            }
            
            // Kelompokkan dan hitung total per customer
            $groupedData = $finalData->groupBy('customer_name')->map(function ($group) use ($tipe) {
                $customerName = $group->first()['customer_name'];
                $totalPPH = $group->sum('pph');
                $totalDebit = $group->sum('debit');
                $totalCredit = $group->sum('credit');
                $saldo = $tipe == 'D' ? $totalDebit - $totalCredit : $totalCredit - $totalDebit;
            
                return [
                    'customer_name' => $customerName,
                    'total_debit' => $totalDebit - $totalPPH,
                    'total_credit' => $totalCredit,
                    'saldo' => $saldo,
                ];
            })->sortByDesc('saldo');            
        }
                if ($subjek == 'customer_xpdc') {
            // Cache daftar customer
            $customer = Cache::remember('customer_list', 60, function () {
                return Customer::pluck('nama', 'id');
            });
            // Cache daftar tarif
            $tarif = Cache::remember("tarif_list_{$customer->keys()->implode('_')}", 60, function () use ($customer) {
                return Tarif::whereIn('customer_id', $customer->keys())->pluck('id');
            });
            // Cache daftar order
            $order = Cache::remember("order_list_{$tarif->implode('_')}", 60, function () use ($tarif) {
                return Order::whereIn('tarif_id', $tarif)->pluck('id');
            });
            // Pastikan $order tidak kosong sebelum query transaksi
            $transaksi = $order->isNotEmpty()
                ? Transaksi::whereIn('order_id', $order)->pluck('pph', 'order_id')
                : collect();
            
            // Cache jurnal berdasarkan kriteria, dengan pengecekan agar cache tetap efisien
            $jurnal = Cache::remember("jurnal_{$coa_id}_{$startDate}_{$endDate}_" . implode('_', $order->toArray()), 60, function () use ($coa_id, $order, $startDate, $endDate) {
                return $order->isNotEmpty()
                    ? Jurnal::where('coa_id', $coa_id)
                        ->whereIn('order_id', $order)
                        ->whereNull('order_trucking_id')
                        ->whereNull('invoice_trucking')
                        ->whereNull('invoice_vendor')
                        ->whereNull('invoice_agen')
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->whereNotNull('invoice')
                        ->get(['order_id', 'debit', 'credit'])
                    : collect();
            });
            
            // Proses data untuk hasil akhir
            $finalData = $jurnal->map(function ($item) use ($customer) {
                return [
                    'customer_name' => $customer[$item->order->tarif->customer_id] ?? 'Unknown',
                    'debit' => $item->debit,
                    'credit' => $item->credit,
                ];
            });
            
            // Cek kondisi untuk perhitungan PPH
            if ($subjek == 'customer_xpdc' && $coa_id == 46) {
                $orderIds = $jurnal->pluck('order_id')->unique();
            
                $data['pph'] = $orderIds
                    ->map(fn($id) => isset($transaksi[$id]) ? round($transaksi[$id]) : null)
                    ->filter()
                    ->implode('<br>');
            
                // Kurangi debit dengan PPH jika ada
                $data['debit'] = ((int) ($data['debit'] ?? 0)) - ((int) ($data['pph'] ?? 0));
            }
            
            // Kelompokkan dan hitung total per customer
            $groupedData = $finalData->groupBy('customer_name')->map(function ($group) use ($tipe) {
                $customerName = $group->first()['customer_name'];
                $totalPPH = $group->sum('pph');
                $totalDebit = $group->sum('debit');
                $totalCredit = $group->sum('credit');
                $saldo = $tipe == 'D' ? $totalDebit - $totalCredit : $totalCredit - $totalDebit;
            
                return [
                    'customer_name' => $customerName,
                    'total_debit' => $totalDebit - $totalPPH,
                    'total_credit' => $totalCredit,
                    'saldo' => $saldo,
                ];
            })->sortByDesc('saldo');            
        }
        if ($subjek == 'pelayaran') {
            // Gunakan cache untuk jurnal berdasarkan filter
            $customer = Cache::remember('pelayaran_list', 60, function () {
                return Pelayaran::pluck('nama', 'id');
            });
            $tarif = Cache::remember("hutang_pelayaran_list_{$customer->keys()->implode('_')}", 60, function () use ($customer){
                return HutangPelayaran::whereNotNull('no_bg_opt')
                    ->whereIn('pelayaran_id', $customer->keys())
                    ->orWhereNotNull('no_bg_opp')
                    ->orWhereNotNull('no_bg_ut')
                    ->pluck('id');
            });
            $order = Cache::remember("order_pelayaran_list_{$tarif->implode('_')}", 60, function () use ($tarif) {
                return Order::whereIn('tarif_id', $tarif)->pluck('id');
            });
            $jurnal = Cache::remember("jurnal_{$coa_id}_{$startDate}_{$endDate}_{$order->implode('_')}", 60, function () use ($coa_id, $order, $endDate, $startDate) {
                return Jurnal::with(['order.hutang_pelayaran.pelayaran' => function($query) {
                        $query->select('id', 'nama'); // Pilih kolom 'id' dan 'nama' dari tabel 'pelayaran'
                    }])
                    ->where('coa_id', $coa_id)
                    ->whereNotNull('no_bg')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get();
            });


            // Gunakan cache untuk proses mapping dan pengelompokan
            $groupedData = Cache::remember("grouped_pelayaran_{$coa_id}_{$year}_{$month}", 60, function () use ($jurnal, $tipe) {
                // Mapping data jurnal untuk menyesuaikan format yang diinginkan
                $finalData = $jurnal->map(function ($item) {
                    // Mengambil nama pelayaran dari relasi yang sudah dimuat
                    $pelayaranName = $item->order->hutang_pelayaran->pelayaran->nama ?? $item->bg_pelayaran();
            
                    return [
                        'pelayaran' => $pelayaranName,  // Ambil nama pelayaran
                        'debit' => $item->debit,
                        'credit' => $item->credit,
                        'no_bg' => $item->no_bg,
                    ];
                });
            
                // Kelompokkan data berdasarkan nama pelayaran dan hitung total debit, kredit, dan saldo
                return $finalData->groupBy('pelayaran')->map(function ($group) use ($tipe) {
                    $totalDebit = $group->sum('debit');
                    $totalCredit = $group->sum('credit');
                    $saldo = $tipe == 'D'
                        ? $totalDebit - $totalCredit  // Jika tipe adalah 'D'
                        : $totalCredit - $totalDebit; // Jika tipe bukan 'D'
            
                    return [
                        'pelayaran' => $group->first()['pelayaran'], // Nama pelayaran (satu saja karena sudah dikelompokkan)
                        'total_debit' => $totalDebit,
                        'total_credit' => $totalCredit,
                        'no_bg_list' => $group->pluck('no_bg'), // Nilai no_bg yang unik sebagai koleksi
                        'saldo' => $saldo, // Hitung saldo
                    ];
                })->sortByDesc('saldo');
            });            
        }
        if ($subjek== 'agen'){
            $customer = Cache::remember('agen_list', 60, function () {
                return Agen::pluck('id'); // Mengambil semua agen (ID sebagai key, Nama sebagai value)
            });
            
            $order = Cache::remember("order_agen_list_" . $customer, 60, function () use ($customer) {
                return Order::whereIn('agen_id', $customer)->pluck('invoice_agen'); // Mengambil invoice_agen dengan ID order sebagai key
            });
            $jurnal = Cache::remember("jurnal_{$coa_id}_{$startDate}_{$endDate}_" . implode('_', $order->values()->toArray()), 60, function () use ($coa_id, $order, $endDate, $startDate) {
                return Jurnal::where('coa_id', $coa_id)
                ->whereNull('order_trucking_id')
                ->whereNull('invoice_trucking')
                ->whereNull('invoice_vendor')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereNull('invoice')
                ->whereIn('invoice_agen', $order)
                ->get();
            });
            
            // Ambil semua order yang sesuai dengan invoice_agen dari jurnal
            $orderIds = Order::whereIn('invoice_agen', $order->values())->pluck('agen_id', 'invoice_agen');
            
            // Ambil semua agen berdasarkan ID order yang ditemukan
            $agenList = Agen::whereIn('id', $orderIds->values())->pluck('nama', 'id');
            
            $finalData = $jurnal->map(function ($item) use ($orderIds, $agenList) {
                // Ambil agen_id dari order berdasarkan invoice_agen
                $agenId = $orderIds[$item->invoice_agen] ?? null;
            
                // Ambil nama agen dari daftar yang sudah di-cache
                $customerName = $agenList[$agenId] ?? 'Unknown';
            
                return [
                    'customer_name' => $customerName,
                    'debit' => $item->debit,
                    'credit' => $item->credit,
                ];
            });            
            // Kelompokkan berdasarkan nama customer dan hitung sum debit dan kredit
            $groupedData = $finalData->groupBy('customer_name')->map(function ($group) use ($tipe) {
                // Ambil nama customer (satu karena sudah dikelompokkan)
                $customerName = $group->first()['customer_name'];
                $totalDebit = $group->sum('debit');
                $totalCredit = $group->sum('credit');
                $saldo = $tipe == 'D'
                    ? $totalDebit - $totalCredit  // Jika tipe adalah 'D'
                    : $totalCredit - $totalDebit; // Jika tipe bukan 'D'

                return [
                    'customer_name' => $customerName,
                    'total_debit' => $group->sum('debit'),
                    'total_credit' => $group->sum('credit'),
                    'saldo' => $saldo,
                ];
            })->sortByDesc('saldo');
        }
         // Urutkan berdasarkan saldo secara descending

    // Debugging untuk memastikan hasil data


    if($subjek=='customer_trucking'){
            // Ambil data customer trucking
            $customer = CustomerTrucking::pluck('nama', 'id'); // Pastikan key adalah ID, value adalah nama

            // Ambil order trucking berdasarkan customer_id
            $order = OrderTrucking::whereIn('customer_id', $customer->keys()) // Menggunakan keys() untuk ID
                ->whereNotNull('invoice')
                ->pluck('id');

            // Ambil jurnal berdasarkan order_trucking_id dan coa_id
            $jurnal = Jurnal::where('coa_id', $coa_id)
                ->whereNotNull('invoice_trucking')
                ->whereNull('invoice_vendor') // Pastikan order_trucking_id tidak null
                ->whereIn('order_trucking_id', $order)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get(['order_trucking_id', 'debit', 'credit']);

            // Gabungkan hasil customer trucking dan jurnal
            $finalData = $jurnal->map(function ($item) use ($customer) {
                // Ambil nama customer berdasarkan ID dari relasi order_trucking
                $customerName = optional($item->order_trucking)->customer->nama ?? 'Unknown'; // Cegah error dengan optional()

                return [
                    'customer_name' => $customerName,
                    'debit' => $item->debit,
                    'credit' => $item->credit,
                ];
            });
            // Kelompokkan berdasarkan nama customer dan hitung sum debit dan kredit
            $groupedData = $finalData->groupBy('customer_name')->map(function ($group) use ($tipe){
                // Ambil nama customer (satu karena sudah dikelompokkan)
                $customerName = $group->first()['customer_name'];
                $totalDebit = $group->sum('debit');
                $totalCredit = $group->sum('credit');
                $saldo = $tipe == 'D'
                    ? $totalDebit - $totalCredit  // Jika tipe adalah 'D'
                    : $totalCredit - $totalDebit; // Jika tipe bukan 'D'
                return [
                    'customer_name' => $customerName,
                    'total_debit' => $group->sum('debit'),
                    'total_credit' => $group->sum('credit'),
                    'saldo' => $saldo,
                ];
            })->sortByDesc('saldo'); // Mengurutkan berdasarkan saldo, terbesar dulu
    }

    if($subjek=='vendor'){
                // Ambil data customer trucking
            // Ambil semua customer trucking
        $customer = CustomerTrucking::pluck('nama', 'id'); // Key = ID, Value = Nama

        // Ambil invoice dari OrderTrucking yang ada customer_id-nya di daftar customer, dan tidak null
        $order = OrderTrucking::whereIn('customer_id', $customer->keys())
            ->whereNotNull('invoice')
            ->pluck('invoice');

        // Ambil jurnal yang sesuai coa_id, belum terkait invoice_trucking, dan berdasarkan invoice_vendor
        $jurnal = Jurnal::where('coa_id', $coa_id)
            ->whereNull('invoice_trucking')
            ->whereIn('invoice_vendor', $order)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get(['order_trucking_id', 'debit', 'credit', 'invoice_vendor']); // tambahkan invoice_vendor untuk referensi

        // Ambil mapping invoice → customer_id, tapi hanya yang bukan RAS-LT
        $orderIds = OrderTrucking::whereIn('invoice', $order->values())
            ->where('invoice', 'not like', '%RAS-LT%')
            ->pluck('customer_id', 'invoice'); // ['invoice' => customer_id]

        // Ambil nama customer berdasarkan customer_id yang ditemukan
        $vendorList = CustomerTrucking::whereIn('id', $orderIds->values())
            ->pluck('nama', 'id'); // ['id' => nama]

        // Map data jurnal ke format final dengan customer_name
        $finalData = $jurnal->map(function ($item) use ($orderIds, $vendorList) {
            $vendorId = $orderIds[$item->invoice_vendor] ?? null;
            $customerName = $vendorList[$vendorId] ?? 'Unknown';

            // Ganti nama jika cocok dengan yang ditentukan
            if ($customerName === 'PT. RAHMAT ALAM SAMUDERA') {
                $customerName = 'R1';
            }            

            return [
                'customer_name' => $customerName,
                'debit' => $item->debit,
                'credit' => $item->credit,
            ];
        });

        // Kelompokkan dan hitung total per customer
        $groupedData = $finalData->groupBy('customer_name')->map(function ($group) use ($tipe) {
            $totalDebit = $group->sum('debit');
            $totalCredit = $group->sum('credit');

            return [
                'customer_name' => $group->first()['customer_name'],
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'saldo' => $tipe === 'D'
                    ? $totalDebit - $totalCredit
                    : $totalCredit - $totalDebit,
            ];
        })->sortByDesc('saldo');

}

        if($subjek=='lain-lain'){
            // Ambil data customer trucking
        // Ambil semua customer trucking
        $customer = Customer::pluck('nama', 'id'); // Key = ID, Value = Nama
        $tarif = Cache::remember("tarif_list_{$customer->keys()->implode('_')}", 60, function () use ($customer) {
            return Tarif::whereIn('customer_id', $customer->keys())->pluck('id');
        });
        // Cache daftar order
        $order = Cache::remember("order_list_{$tarif->implode('_')}", 60, function () use ($tarif) {
            return Order::whereIn('tarif_id', $tarif)->pluck('id');
        });

        // Ambil jurnal yang sesuai coa_id, belum terkait invoice_trucking, dan berdasarkan invoice_vendor
        $jurnal = Jurnal::where('coa_id', $coa_id)
        ->where(function ($query) use ($order) {
            $query->whereIn('order_id', $order)
                  ->orWhereNull('order_id');
        })
        ->whereNotNull('invoice_external')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->get(['order_id', 'debit', 'credit', 'invoice_external']);    

        $finalData = $jurnal->map(function ($item) use ($customer) {
            $customerName = 'Lain-lain';
        
            if ($item->order && $item->order->tarif) {
                $customerId = $item->order->tarif->customer_id;
                $customerName = $customer[$customerId] ?? 'Lain-lain';
            }
        
            return [
                'customer_name' => $customerName,
                'debit' => $item->debit,
                'credit' => $item->credit,
            ];
        });        
        
        // Cek kondisi untuk perhitungan PPH
        if ($subjek == 'customer_xpdc' && $coa_id == 46) {
            $orderIds = $jurnal->pluck('order_id')->unique();
        
            $data['pph'] = $orderIds
                ->map(fn($id) => isset($transaksi[$id]) ? round($transaksi[$id]) : null)
                ->filter()
                ->implode('<br>');
        
            // Kurangi debit dengan PPH jika ada
            $data['debit'] = ((int) ($data['debit'] ?? 0)) - ((int) ($data['pph'] ?? 0));
        }
        
        // Kelompokkan dan hitung total per customer
        $groupedData = $finalData->groupBy('customer_name')->map(function ($group) use ($tipe) {
            $customerName = $group->first()['customer_name'];
            $totalPPH = $group->sum('pph');
            $totalDebit = $group->sum('debit');
            $totalCredit = $group->sum('credit');
            $saldo = $tipe == 'D' ? $totalDebit - $totalCredit : $totalCredit - $totalDebit;
        
            return [
                'customer_name' => $customerName,
                'total_debit' => $totalDebit - $totalPPH,
                'total_credit' => $totalCredit,
                'saldo' => $saldo,
            ];
        })->sortByDesc('saldo'); 

        }

    if ($subjek == 'relasi') {
    $jurnal = Jurnal::where('coa_id', $coa_id)
        ->whereNotNull('relasi')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->orderBy('created_at', 'asc') // Pastikan urut tanggal
        ->get(['debit', 'credit', 'nama', 'nomor', 'created_at', 'relasi', 'invoice', 'invoice_trucking', 'invoice_external', 'invoice_vendor']);

    $runningBalance = 0;
    $groupedData = $jurnal->groupBy('relasi')->map(function ($group) use ($tipe) {
                $customerName = $group->first()->relasi;
                $invoice = $group->first()->invoice ?? $group->first()->invoice_external ?? $group->first()->invoice_vendor ?? $group->first()->invoice_trucking;            
                // Ambil nama & tanggal untuk debit
                $ket_d = $group->where('debit', '>', 0)->pluck('nama')->values();
                $date_d = $group->where('debit', '>', 0)
                    ->pluck('created_at')
                    ->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))
                    ->values();
            
                // Ambil nama & tanggal untuk kredit
                $ket_c = $group->where('credit', '>', 0)->pluck('nama')->values();
                $date_c = $group->where('credit', '>', 0)
                    ->pluck('created_at')
                    ->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))
                    ->values();
            
                // Total
                $totalDebit = $group->sum('debit');
                $totalCredit = $group->sum('credit');
            
                // Hitung saldo tergantung tipe
                $saldo = $tipe === 'D'
                    ? $totalDebit - $totalCredit
                    : $totalCredit - $totalDebit;
            
                return [
                    'invoice' => $invoice,
                    'customer_name' => $customerName,
                    'ket_d' => $ket_d,
                    'tgl_d' => $date_d,
                    'ket_c' => $ket_c,
                    'tgl_c' => $date_c,
                    'total_debit' => $totalDebit,
                    'total_credit' => $totalCredit,
                    'saldo' => $saldo,
                ];
            })->sortByDesc('tgl_d')->values(); // Urutkan dari saldo tertinggi dan reset index
            
         // Mengurutkan dan reset index
    
        // Return atau gunakan $groupedData sesuai kebutuhan
 $groupedData = $jurnal->groupBy('relasi')->map(function ($group) use ($tipe) {
                $customerName = $group->first()->relasi;
                $invoice = $group->first()->invoice ?? $group->first()->invoice_external ?? $group->first()->invoice_vendor ?? $group->first()->invoice_trucking;            
                // Ambil nama & tanggal untuk debit
                $ket_d = $group->where('debit', '>', 0)->pluck('nama')->values();
                $no_d = $group->where('debit', '>', 0)->pluck('nomor')->values();
                $date_d = $group->where('debit', '>', 0)
                    ->pluck('created_at')
                    ->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))
                    ->values();
            
                // Ambil nama & tanggal untuk kredit
                $ket_c = $group->where('credit', '>', 0)->pluck('nama')->values();
                $no_c = $group->where('credit', '>', 0)->pluck('nomor')->values();
                $date_c = $group->where('credit', '>', 0)
                    ->pluck('created_at')
                    ->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))
                    ->values();
            
                // Total
                $totalDebit = $group->sum('debit');
                $totalCredit = $group->sum('credit');
            
                // Hitung saldo tergantung tipe
                $saldo = $tipe === 'D'
                    ? $totalDebit - $totalCredit
                    : $totalCredit - $totalDebit;
            
                return [
                    'invoice' => $invoice,
                    'customer_name' => $customerName,
                    'no_d' => $no_d,
                    'ket_d' => $ket_d,
                    'tgl_d' => $date_d,
                    'no_c' => $no_c,
                    'ket_c' => $ket_c,
                    'tgl_c' => $date_c,
                    'total_debit' => $totalDebit,
                    'total_credit' => $totalCredit,
                    'saldo' => $saldo,
                ];
            })->sortByDesc('tgl_d')->values();

    if($coa_id == 65 || $coa_id == 66){
        $groupedData = $jurnal->map(function ($row) use (&$runningBalance, $tipe) {
            $customerName = $row->relasi;
            $invoice = $row->invoice ?? $row->invoice_external ?? $row->invoice_vendor ?? $row->invoice_trucking;
    
            // Tanggal dan keterangan tunggal karena ini per baris
            $ket_d = [$row->nama];
            $tgl_d = $row->debit > 0
                ? [Carbon::parse($row->created_at)->format('Y-m-d')]
                : ($row->credit > 0 ? [Carbon::parse($row->created_at)->format('Y-m-d')] : []);
    
            $ket_c = $row->nama;
            $tgl_c = $row->credit > 0
                ? [Carbon::parse($row->created_at)->format('Y-m-d')]
                : [];
    
            $totalDebit = $row->debit;
            $totalCredit = $row->credit;
    
            // Tambahkan ke saldo berjalan
            $runningBalance += $tipe === 'D'
                ? ($totalDebit - $totalCredit)
                : ($totalCredit - $totalDebit);
    
            return [
                'invoice' => $invoice,
                'customer_name' => $customerName,
                'ket_d' => collect($ket_d),
                'tgl_d' => collect($tgl_d),
                'ket_c' => collect($ket_c),
                'tgl_c' => collect($tgl_c),
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'saldo' => $runningBalance, // saldo berjalan
            ];
        });
    }


    // Gunakan $groupedData sesuai kebutuhan (misal kirim ke view)
}


    
        // Daftar bulan

        // Mengembalikan tampilan dengan data yang sudah dihitung dan diproses
        return view('admin.jurnal.bb_pembantu', compact(
            'groupedData', 'months', 'coas', 'year', 'month', 'coa_id', 'tipe', 'subjek'
        ));
    }
    public function buku_besar_pembantu_rincian($year, $month, $coa_id, $customer, $subjek)
{
    $details = [];
    $totalDebit = 0;
    $totalCredit = 0;
    $totalSaldo =0;
    $groupedJurnal=[]; 
    $customerPelayaran = null;
    $tipe = in_array(substr($coa_id, 0, 1), ['2', '3', '5']) ? 'C' : 'D';
    $startDate = '2022-01-01';
    $endDate = Carbon::create($year, $month)->endOfMonth()->endOfDay();

    if ($subjek == 'customer_xpdc') {
        // Ambil data terkait customer
        $customers = Customer::where('nama', $customer)->pluck('nama', 'id');
        $tarif = Tarif::whereIn('customer_id', $customers->keys())->pluck('id');
        $order = Order::whereIn('tarif_id', $tarif)->pluck('id');
        $transaksi = Transaksi::whereIn('order_id', $order)->pluck('pph', 'order_id');

        // Query jurnal
        $jurnal = Jurnal::where('coa_id', $coa_id)
            ->whereIn('order_id', $order)
            ->whereNull('order_trucking_id')
            ->whereNull('invoice_trucking')
            ->whereNull('invoice_vendor')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('invoice_agen')
            ->whereNotNull('invoice')
            ->orderBy('invoice')
            ->get(['order_id', 'debit', 'credit', 'nama', 'nomor', 'created_at', 'invoice']);
        $nomor = $jurnal->pluck('nomor');
        $pph =  Jurnal::where('coa_id',52)
        ->whereIn('nomor',$nomor)
        ->pluck('nomor')->unique();
        // Kelompokkan jurnal berdasarkan invoice
        $groupedJurnal = $jurnal->groupBy('invoice')->map(function ($items) use ($transaksi, $subjek, $coa_id,$pph,$nomor) {
            $data = [
                'no_pph' => $pph,
                'nomor_d' => $items->where('debit', '>', 0)->pluck('nomor')
                    ->map(fn($nomor) => '<a href="' . url('admin/jurnal-edit?jurnal=' . $nomor) . '" target="_blank">' . $nomor . '</a>')
                    ->implode('<br>'),
                'tgl_d' => implode('<br>', $items->where('debit', '>', 0)->pluck('created_at')->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))->toArray()),
                'nomor_k' => $items->where('credit', '>', 0)
                    ->pluck('nomor')
                    ->map(fn($nomor) => '<a href="' . url('admin/jurnal-edit?jurnal=' . $nomor) . '" target="_blank">' . $nomor . '</a>')
                    ->implode('<br>'),
                'tgl_k' => implode('<br>', $items->where('credit', '>', 0)->pluck('created_at')->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))->toArray()),
                'invoice' => $items->first()->invoice,
                'debit' => $items->sum('debit'),
                'credit' => $items->sum('credit'),
                'keterangan' => $items->pluck('nama')->unique()->implode('<br>'), // Gabungkan semua keterangan
            ];
            // Tambahkan pph jika kondisi terpenuhi
            if ($subjek == 'customer_xpdc' && $coa_id == 46) {
                $orderIds = $items->pluck('order_id')->unique();
                $data['pph'] = $orderIds
                    ->map(fn($id) => isset($transaksi[$id]) ? round($transaksi[$id]) : null)
                    ->filter()
                    ->implode('<br>');
                    $data['debit'] =  $data['debit'];
            }            
            return $data;
        });
        // Hitung total debit dan credit
        foreach ($groupedJurnal as $detail) {
            $totalDebit += $detail['debit']; 
            $totalCredit += $detail['credit'];
        }

        // Saldo total
        $totalSaldo = $totalDebit - $totalCredit;
    }
    if ($subjek == 'lain-lain') {
        // Ambil data terkait customer
        $customers = Customer::where('nama', $customer)->pluck('nama', 'id');
        $tarif = Tarif::whereIn('customer_id', $customers->keys())->pluck('id');
        $order = Order::whereIn('tarif_id', $tarif)->pluck('id');
        $transaksi = Transaksi::whereIn('order_id', $order)->pluck('pph', 'order_id');

        // Query jurnal
        // Ambil jurnal yang sesuai coa_id, belum terkait invoice_trucking, dan berdasarkan invoice_vendor
        if($customer == 'Lain-lain'){
            $jurnal = Jurnal::where('coa_id', $coa_id)
            ->where(function ($query) use ($order) {
                $query->whereIn('order_id', $order)
                      ->orWhereNull('order_id');
            })
            ->whereNotNull('invoice_external')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get(['order_id', 'debit', 'credit', 'nama', 'nomor', 'created_at', 'invoice_external']);
        } else {
            $jurnal = Jurnal::where('coa_id', $coa_id)
            ->whereIn('order_id',$order)
            ->whereNotNull('invoice_external')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get(['order_id', 'debit', 'credit', 'nama', 'nomor', 'created_at', 'invoice_external']);   
        }
        $nomor = $jurnal->pluck('nomor');
        // Kelompokkan jurnal berdasarkan invoice
        $groupedJurnal = $jurnal->groupBy('invoice_external')->map(function ($items) use ($transaksi, $subjek, $coa_id,$nomor) {
            $data = [
                'nomor_d' => $items->where('debit', '>', 0)->pluck('nomor')->first(),
                            'tgl_d' => implode(
                    '<br>',
                    $items->where('debit', '>', 0)
                        ->pluck('created_at')
                        ->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))
                        ->unique()
                        ->toArray()
                ),

                'nomor_k' => $items->where('credit', '>', 0)
                    ->pluck('nomor')
                    ->map(fn($nomor) => '<a href="' . url('admin/jurnal-edit?jurnal=' . $nomor) . '" target="_blank">' . $nomor . '</a>')
                    ->implode('<br>'),
                'tgl_k' => implode('<br>', $items->where('credit', '>', 0)->pluck('created_at')->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))->toArray()),
                'invoice_external' => $items->first()->invoice_external,
                'debit' => $items->sum('debit'),
                'credit' => $items->sum('credit'),
                'keterangan' => $items->pluck('nama')->unique()->implode('<br>'), // Gabungkan semua keterangan
            ];
            // Tambahkan pph jika kondisi terpenuhi
            if ($subjek == 'customer_xpdc' && $coa_id == 46) {
                $orderIds = $items->pluck('order_id')->unique();
                $data['pph'] = $orderIds
                    ->map(fn($id) => isset($transaksi[$id]) ? round($transaksi[$id]) : null)
                    ->filter()
                    ->implode('<br>');
                    $data['debit'] =  $data['debit'];
            }            
            return $data;
        });
        // Hitung total debit dan credit
        foreach ($groupedJurnal as $detail) {
            $totalDebit += $detail['debit']; 
            $totalCredit += $detail['credit'];
        }

        // Saldo total
        $totalSaldo = $tipe == 'C'
        ? $totalDebit - $totalCredit  // Jika tipe adalah 'D'
        : $totalCredit - $totalDebit;
    }

    if ($subjek == 'agen') {
        // Ambil data terkait customer
        $customers = Agen::where('nama', $customer)->pluck('nama', 'id');
        $order = Order::whereIn('agen_id', $customers->keys())->pluck('invoice_agen','id');
        // Query jurnal
        $jurnal = Jurnal::where('coa_id', $coa_id)
        ->whereNull('order_trucking_id')
        ->whereNull('invoice_trucking')
        ->whereNull('invoice_vendor')
        ->whereNull('invoice')
        ->whereIn('invoice_agen', $order->values())
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get(['order_id', 'debit', 'credit', 'nama', 'nomor', 'input', 'invoice_agen']);
        // Kelompokkan jurnal berdasarkan invoice
        $groupedJurnal = $jurnal->groupBy('invoice_agen')->map(function ($items) {
            return [
                'nomor_d' => $items->where('debit', '>', 0)->pluck('nomor')->first(),
                'tgl_d' => $items->where('debit', '>', 0)->pluck('input')->first(),
                'nomor_k' => $items->where('credit', '>', 0)
                    ->pluck('nomor')
                    ->map(fn($nomor) => '<a href="' . url('admin/jurnal-edit?jurnal=' . $nomor) . '" target="_blank">' . $nomor . '</a>')
                    ->implode('<br>'),
                'tgl_k' => $items->where('credit', '>', 0)->pluck('input')->implode('<br>'),
                'invoice_agen' => $items->first()->invoice_agen,
                'debit' => $items->sum('debit'),
                'credit' => $items->sum('credit'),
                'keterangan' => $items->pluck('nama')->unique()->implode('<br>'), // Gabungkan semua keterangan
            ];
        });
        

        // Hitung total debit dan credit
        foreach ($groupedJurnal as $detail) {
            $totalDebit += $detail['debit'];
            $totalCredit += $detail['credit'];
        }

        // Saldo total
        $totalSaldo = $tipe == 'C'
        ? $totalDebit - $totalCredit  // Jika tipe adalah 'D'
        : $totalCredit - $totalDebit;
    }
    if ($subjek == 'vendor') {
        // Ambil data terkait customer
        if ($customer == 'R1') {
            // Karena 'PT. RAHMAT ALAM SAMUDERA' ada di id 2,
            // maka buat koleksi dengan index 2 dan value-nya
            $customers = collect([2 => 'R1']);
        } else {
            $customers = CustomerTrucking::where('nama', $customer)
                ->pluck('nama', 'id');
        }        
        $order = OrderTrucking::whereIn('customer_id', $customers->keys())->whereNotNull('invoice')->pluck('id');
        $invoice = OrderTrucking::whereIn('customer_id', $customers->keys())->whereNotNull('invoice')->pluck('invoice');
        $transaksi = TransaksiTrucking::whereIn('customer_id',$customers->keys())->pluck('pph', 'order_trucking_id');

        // Query jurnal
        $jurnal = Jurnal::where('coa_id', $coa_id)
            ->whereIn('invoice_vendor', $invoice)
            ->whereNull('invoice_trucking')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get(['order_trucking_id', 'debit', 'credit', 'nama', 'nomor', 'created_at','invoice_vendor']);
            $nomor = $jurnal->pluck('nomor');
            $pph =  Jurnal::where('coa_id',52)
                ->whereIn('nomor',$nomor)
                ->pluck('nomor')->unique();
        // Kelompokkan jurnal berdasarkan invoice
        $groupedJurnal = $jurnal->groupBy('invoice_vendor')->map(function ($items) use ($transaksi, $subjek, $coa_id,$pph,$nomor){
            $data = [
                'nomor_d' => $items->where('debit', '>', 0)->pluck('nomor')->first(),
                'tgl_d' => implode('<br>', $items->where('debit', '>', 0)->pluck('created_at')->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))->toArray()),
                'nomor_k' =>  $items->where('credit', '>', 0)
                        ->pluck('nomor')
                        ->map(fn($nomor) => '<a href="' . url('admin/jurnal-edit?jurnal=' . $nomor) . '" target="_blank">' . $nomor . '</a>')
                        ->implode('<br>'),
                'tgl_k' => implode('<br>', $items->where('credit', '>', 0)->pluck('created_at')->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))->toArray()),
                'invoice_vendor' => $items->first()->invoice_vendor,
                'debit' => $items->sum('debit'),
                'credit' => $items->sum('credit'),
                'keterangan' => $items->pluck('nama')->unique()->implode('<br>') , // Gabungkan semua keterangan
            ];
            if ($subjek == 'customer_trucking' && $coa_id == 47) {
                $orderIds = $items->pluck('order_trucking_id')->unique();
                $data['pph'] = $orderIds
                    ->map(fn($id) => isset($transaksi[$id]) ? round($transaksi[$id]) : null)
                    ->filter()
                    ->implode('<br>');
                    $data['debit'] =  $data['debit'];
            }            
            return $data;
        });
        

        // Hitung total debit dan credit
        foreach ($groupedJurnal as $detail) {
            $totalDebit += $detail['debit'];
            $totalCredit += $detail['credit'];
        }

        // Saldo total
        $totalSaldo = $tipe == 'C'
        ? $totalDebit - $totalCredit  // Jika tipe adalah 'D'
        : $totalCredit - $totalDebit;
    }

    if ($subjek == 'customer_trucking') {
        // Ambil data terkait customer
        $customers = CustomerTrucking::where('nama', $customer)->pluck('nama', 'id');
        $order = OrderTrucking::whereIn('customer_id', $customers->keys())->whereNotNull('invoice')->pluck('id');
        $invoice = OrderTrucking::whereIn('customer_id', $customers->keys())->whereNotNull('invoice')->pluck('invoice');
        $transaksi = TransaksiTrucking::whereIn('customer_id',$customers->keys())->pluck('pph', 'order_trucking_id');

        // Query jurnal
        $jurnal = Jurnal::where('coa_id', $coa_id)
            ->whereIn('order_trucking_id', $order)
            ->whereNull('invoice_vendor')
            ->whereNotNull('invoice_trucking')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get(['order_id', 'order_trucking_id', 'debit', 'credit', 'nama', 'nomor', 'created_at', 'invoice_trucking','invoice_vendor']);
            $nomor = $jurnal->pluck('nomor');
            $pph =  Jurnal::where('coa_id',52)
                ->whereIn('nomor',$nomor)
                ->pluck('nomor')->unique();
        // Kelompokkan jurnal berdasarkan invoice
        $groupedJurnal = $jurnal->groupBy('invoice_trucking')->map(function ($items) use ($transaksi, $subjek, $coa_id,$pph,$nomor){
            $data = [
                'nomor_d' => $items->where('debit', '>', 0)->pluck('nomor')->first(),
                'tgl_d' => implode('<br>', $items->where('debit', '>', 0)->pluck('created_at')->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))->toArray()),
                'nomor_k' =>  $items->where('credit', '>', 0)
                        ->pluck('nomor')
                        ->map(fn($nomor) => '<a href="' . url('admin/jurnal-edit?jurnal=' . $nomor) . '" target="_blank">' . $nomor . '</a>')
                        ->implode('<br>'),
                'tgl_k' => implode('<br>', $items->where('credit', '>', 0)->pluck('created_at')->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))->toArray()),
                'invoice_trucking' => $items->first()->invoice_trucking,
                'debit' => $items->sum('debit'),
                'credit' => $items->sum('credit'),
                'keterangan' => $items->pluck('nama')->unique()->implode('<br>') , // Gabungkan semua keterangan
            ];
            if ($subjek == 'customer_trucking' && $coa_id == 47) {
                $orderIds = $items->pluck('order_trucking_id')->unique();
                $data['pph'] = $orderIds
                    ->map(fn($id) => isset($transaksi[$id]) ? round($transaksi[$id]) : null)
                    ->filter()
                    ->implode('<br>');
                    $data['debit'] =  $data['debit'];
            }            
            return $data;
        });
        

        // Hitung total debit dan credit
        foreach ($groupedJurnal as $detail) {
            $totalDebit += $detail['debit'];
            $totalCredit += $detail['credit'];
        }

        // Saldo total
        $totalSaldo = $totalDebit - $totalCredit;
    }


        if ($subjek == 'pelayaran') {
        // Ambil data terkait 
        $pelayaran = Pelayaran::where('nama', $customer)->first();

        if (!$pelayaran) {
            return response()->json(['error' => 'Pelayaran tidak ditemukan'], 404);
        }
        // Ambil semua no BG dari relasi
        $no_bgs = $pelayaran->bg(); // atau $pelayaran->bg_list jika pakai accessor

        $jurnal = Jurnal::where('coa_id', $coa_id)
            ->whereIn('no_bg', $no_bgs) // Menggunakan array $customer
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get(['order_id', 'debit', 'credit', 'nama', 'nomor', 'created_at', 'no_bg']);
        // Kelompokkan jurnal berdasarkan invoice
        $groupedJurnal = $jurnal->groupBy('no_bg')->map(function ($items) {
            return [
                'nomor_d' => $items->where('debit', '>', 0)->pluck('nomor')->first(),
                'tgl_d' => implode('<br>', $items->where('debit', '>', 0)->pluck('created_at')->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))->toArray()),
                'nomor_k' =>  $items->where('credit', '>', 0)
                        ->pluck('nomor')
                        ->map(fn($nomor) => '<a href="' . url('admin/jurnal-edit?jurnal=' . $nomor) . '" target="_blank">' . $nomor . '</a>')
                        ->implode('<br>'),
                'tgl_k' => implode('<br>', $items->where('credit', '>', 0)->pluck('created_at')->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))->toArray()),
                'no_bg' => $items->first()->no_bg,
                'debit' => $items->sum('debit'),
                'credit' => $items->sum('credit'),
                'keterangan' => $items->pluck('nama')->unique()->implode('<br>') , // Gabungkan semua keterangan
            ];
        });

        // Hitung total debit dan credit
        foreach ($groupedJurnal as $detail) {
            $totalDebit += $detail['debit'];
            $totalCredit += $detail['credit'];
        }

        // Saldo total
        $totalSaldo = $totalCredit - $totalDebit;
    }

    return view('admin.jurnal.buku_besar_pembantu_detail', compact('coa_id','customerPelayaran','customer', 'subjek', 'totalSaldo', 'groupedJurnal', 'totalDebit', 'totalCredit'));
}



    // public function buku_besar_pembantu_detail($year, $month, $coa_id, $pelayaran)
    // {
    //     // dd($year,$month,$coa_id,$pelayaran);
    //     $pelayaran = Pelayaran::where('nama', 'like', $pelayaran)->first();
    //     if (!$pelayaran) {
    //         return back()->with('danger', 'Mohon maaf sistem ada yang salah!');
    //     }
    //     $pelayaran_id = $pelayaran->id;
    //     $bgs = array();
    //     $data = HutangPelayaran::where('pelayaran_id', $pelayaran_id)->select('no_bg_opp', 'no_bg_opt', 'no_bg_ut')->get();
    //     foreach ($data as $bg) {
    //         if (!is_null($bg->no_bg_opp)) {
    //             array_push($bgs, $bg->no_bg_opp);
    //         }
    //         if (!is_null($bg->no_bg_opt)) {
    //             array_push($bgs, $bg->no_bg_opt);
    //         }
    //         if (!is_null($bg->no_bg_ut)) {
    //             array_push($bgs, $bg->no_bg_ut);
    //         }
    //     }
    //     $bgs = array_unique($bgs);
    //     $c = new Carbon($year . '-' . sprintf('%02d', $month) . '-01');
    //     $now = $c->startOfMonth()->format('Y-m-d');
    //     $last = $c->endOfMonth()->format('Y-m-d');
    //     $start = '2022-12-01';
    //     $query = Jurnal::query();
    //     $query->join('coa', 'coa.id', '=', 'jurnal.coa_id');
    //     $query->select('jurnal.*');
    //     $query->where('jurnal.coa_id', $coa_id);
    //     $query->whereIn('jurnal.no_bg', $bgs);
    //     $query->whereBetween('jurnal.created_at', [$start, $last]);
    //     $query->orderBy('created_at');
    //     $jurnals = $query->get();
    //     return view('admin.jurnal.buku_besar_pembantu_detail', compact('jurnals', 'pelayaran_id'));
    // }

    public function datatable()
    {
        $data = Jurnal::orderBy('created_at', 'desc')->get();

        return Datatables::of($data)
            ->addColumn('debit', function ($data) {
                return $data->debit == 0 ? '-' : number_format($data->debit, 2, '.', ',');
            })
            ->addColumn('credit', function ($data) {
                return $data->credit == 0 ? '-' : number_format($data->credit, 2, '.', ',');
            })
            ->addColumn('coa_id', function ($data) {
                return $data->coa->nama;
            })
            ->addColumn('code', function ($data) {
                return $data->coa->kode;
            })
            ->addColumn('created_at', function ($data) {
                return date('d/m/y', strtotime($data->created_at));
            })
            ->addColumn('order_id', function ($data) {
                $name = '-';
                if ($data->order) {
                    $name = $data->order->job . '-' . sprintf('%02d', $data->order->no_job);
                }
                return $name;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function exportJurnalBatch()
    {
        (new JurnalBatchExport(request('year'), request('month')))->queue('buku-besar.xlsx', 'public');
        return back()->with('success', 'Request Export telah dibuat, silahkan tunggu beberapa saat hingga proses export selesai. refresh halaman secara berkala untuk melihat proses export. Process kurang lebih 3-5 menit.');
    }

    public function exportMonth(Request $request)
    {
        return Excel::download(new JurnalMonth($request->from, $request->to, $request->tipe, $request->year, $request->month, $request->is_sample), 'jurnal.xlsx');
    }

    public function syncJob()
    {
        $last = Carbon::now()->subDays(35)->format('Y-m-d');
        $data = Jurnal::whereNotNull('order_id')->whereNull('container')->whereBetween('created_at', [$last, date('Y-m-d')])->get();
        foreach ($data as $item) {
            $item->update([
                'container' => $item->order->container ?? null,
            ]);
        }

        $data = Jurnal::whereNotNull('order_trucking_id')->whereNull('order_id')->whereBetween('created_at', [$last, date('Y-m-d')])->get();
        $awal = $data->count();
        $akhir = 0;
        $subs = 0;
        foreach ($data as $item) {
            if (!is_null($item->order_trucking->container ?? null) && !is_null($item->order_trucking->seal ?? null)) {
                $order = Order::where('container', $item->order_trucking->container)->where('seal', $item->order_trucking->seal)->first();
                if ($order) {
                    $item->update([
                        'order_id' => $order->id,
                        'container' => $item->order_trucking->container ?? null,
                        'nopol' => $item->order_trucking->kendaraan->nopol ?? null,
                    ]);
                    $akhir++;
                } else {
                    $awal--;
                }
            }
        }

        return back()->with('success', $akhir . '/' . $awal . ' data berhasil disinkronisasi!');
    }

    public function filter()
    {
        return view('admin.jurnal.filter');
    }

    public function jurnal_bupot_trucking()
    {
        return view('admin.jurnal.bupot_trucking');
    }

    public function jurnal_bupot_trucking_store(Request $request)
    {
        $data = $request->all();
        $no = Jurnal::where('tipe', $data['tipe'])->whereYear('created_at', date('Y', strtotime($data['created_at'])))->max('no') + 1;
        if ($data['tipe'] == 'JNL') {
            $no = Jurnal::where('tipe', 'JNL')->whereMonth('created_at', date('m', strtotime($data['created_at'])))->whereYear('created_at', date('Y', strtotime($data['created_at'])))->max('no') + 1;
        }

        $jurnal_model = new Jurnal();
        if ($data['simpan'] == 'tampungan') {
            $jurnal_model = new JurnalTampungan();
        }
$inputIds = $data['invoice']; // array:11 berisi id: [7, 19, 2, ...]

$order = OrderTrucking::whereIn('id', $inputIds)
    ->get(['id', 'invoice']);

// Urutkan sesuai urutan input
$invoice = collect($inputIds)->map(function ($id) use ($order) {
    return optional($order->firstWhere('id', $id))->invoice;
})->filter(); // filter jika ada null

$orders = collect($inputIds)->map(function ($id) use ($order) {
    return optional($order->firstWhere('id', $id))->id;
})->filter(); // filter jika ada null


        $month = ['JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 'JULY', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'];

        for ($i = 0; $i < count($data['debit_coa_id']); $i++) {
            if ($data['name'][$i] && $data['amount'][$i]) {
                $name = $data['name'][$i];
                $trx = TransaksiTrucking::where('invoice', $invoice[$i])->first();
                if ($trx) {
                    $name = str_replace('[1]', $trx->customer->nama, $name);
                }
                if ($data['tipe'] == 'JNL') {
                    $nomor = sprintf('%02d', date('m', strtotime($data['created_at']))) . '-' . sprintf('%03d', $no) . '/' . ($this->sno == 'ALB' ? 'ALB/' : '') . date('y', strtotime($data['created_at']));
                } else {
                    $nomor = sprintf('%03d', $no) . '/' . $data['tipe'] . '-' . $this->sno . '/' . date('y', strtotime($data['created_at']));
                }
                if ($data['debit_coa_id'][$i] && $data['credit_coa_id'][$i]) {
                    $jurnal_model->create([
                        'tipe' => $data['tipe'],
                        'order_trucking_id' => $orders[$i],
                        'invoice_vendor' => !str_contains($invoice[$i], 'RAS-LT') ? $invoice[$i] : null,
                        'invoice_trucking' => str_contains($invoice[$i], 'RAS-LT') ? $invoice[$i] : null,
                        'coa_id' => $data['debit_coa_id'][$i],
                        'nomor' => $nomor,
                        'relasi' => $nomor,
                        'nama' => $name,
                        'debit' => $data['amount'][$i],
                        'created_at' => $data['created_at'],
                        'no' => $no
                    ]);
                    $jurnal_model->create([
                        'tipe' => $data['tipe'],
                        'order_trucking_id' => $orders[$i],
                       'invoice_vendor' => !str_contains($invoice[$i], 'RAS-LT') ? $invoice[$i] : null,
                        'invoice_trucking' => str_contains($invoice[$i], 'RAS-LT') ? $invoice[$i] : null,
                        'coa_id' => $data['credit_coa_id'][$i],
                        'nomor' => $nomor,
                        'relasi' => $nomor,
                        'nama' => $name,
                        'credit' => $data['amount'][$i],
                        'created_at' => $data['created_at'],
                        'no' => $no
                    ]);
                } else {
                    if ($data['debit_coa_id'][$i]) {
                        $jurnal_model->create([
                            'tipe' => $data['tipe'],
                            'order_trucking_id' => $orders[$i],
                             'invoice_vendor' => !str_contains($invoice[$i], 'RAS-LT') ? $invoice[$i] : null,
                        'invoice_trucking' => str_contains($invoice[$i], 'RAS-LT') ? $invoice[$i] : null,
                            'coa_id' => $data['debit_coa_id'][$i],
                            'nomor' => $nomor,
                            'relasi' => $nomor,
                            'nama' => $name,
                            'debit' => $data['amount'][$i],
                            'created_at' => $data['created_at'],
                            'no' => $no
                        ]);
                    }
                    if ($data['credit_coa_id'][$i]) {
                        $jurnal_model->create([
                            'tipe' => $data['tipe'],
                            'order_trucking_id' => $orders[$i],
                           'invoice_vendor' => !str_contains($invoice[$i], 'RAS-LT') ? $invoice[$i] : null,
                        'invoice_trucking' => str_contains($invoice[$i], 'RAS-LT') ? $invoice[$i] : null,
                            'coa_id' => $data['credit_coa_id'][$i],
                            'nomor' => $nomor,
                            'relasi' => $nomor,
                            'nama' => $name,
                            'credit' => $data['amount'][$i],
                            'created_at' => $data['created_at'],
                            'no' => $no
                        ]);
                    }
                }

                TransaksiTrucking::where('invoice', $invoice[$i])->update([
                    'bupot' => $data['amount'][$i],
                    'masa_bupot' => date('F Y', strtotime($data['masa_bupot'][$i])),
                    'tanggal_bupot' => $data['tanggal_bupot'][$i],
                    'no_bupot' => $data['no_bupot'][$i],
                ]);
            }
        }

        return back()->with('success', 'Data berhasil disimpan');
    }
}
