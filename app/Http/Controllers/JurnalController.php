<?php

namespace App\Http\Controllers;

use App\Exports\JurnalBatchExport;
use App\Exports\JurnalCoaExport;
use App\Exports\JurnalMonth;
use App\Http\Resources\OrderResource;
use App\Services\SyncService;
use App\Imports\JurnalImport;
use App\Models\COA;
use App\Models\Agen;
use App\Models\HutangPelayaran;
use App\Models\Jurnal;
use App\Models\Customer;
use App\Models\CustomerTrucking;
use App\Models\JurnalSample;
use App\Models\JurnalTampungan;
use App\Models\Order;
use App\Models\OrderTrucking;
use App\Models\Tarif;
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
        $unbalance = Jurnal::select([DB::raw("SUM(debit) as debit"), DB::raw("SUM(credit) as credit"), 'nomor'])->whereBetween('created_at', [$last, $now])->groupBy('nomor')->get()->reject(function ($data) {
            return $data->debit == $data->credit;
        });
        // $unbalance = [];
        $month = request('month') ?? date('m');
        $year = request('year') ?? date('Y');
        $is_sample = request('is_sample') ?? 'real';
        return view('admin.jurnal.index', compact('month', 'unbalance', 'year', 'is_sample'));
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
                $query->where('nama', 'LIKE', request('name'));
            }
            if (request('debit_coa_id_tujuan')) {
                $query->where('coa_id', request('debit_coa_id_tujuan'));
                $query->where('debit', '>', 0);
            }
            if (request('credit_coa_id_tujuan')) {
                $query->where('coa_id', request('credit_coa_id_tujuan'));
                $query->where('credit', '>', 0);
                $query->whereNull('jurnal_balik');
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
        return view('admin.jurnal.balik', compact('coa', 'new', 'coa_debit', 'coa_credit', 'orders', 'data', 'no_1', 'no_2', 'no_3', 'no_4', 'no_5', 'nomor_1', 'nomor_2', 'nomor_3', 'nomor_4', 'nomor_5'));
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
                        'order_trucking_id' => $order_trucking ?? $order_vendor ?? null,
                        'invoice' => $invoice_expdc ?? null,
                        'order_id' => $order_expdc ?? $order_agen ?? null,
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
                        'order_id' => $order_expdc ?? $order_agen ?? null,
                        'invoice_vendor' => $invoice_vendor ?? null,
                        'order_trucking_id' => $order_trucking ?? $order_vendor ?? null,
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
                            'order_id' => $order_expdc ?? $order_agen ?? null,
                            'invoice_vendor' => $invoice_vendor ?? null,
                            'order_trucking_id' => $order_trucking ?? $order_vendor ?? null,
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
                            'order_id' => $order_expdc ?? $order_agen ?? null,
                            'invoice_vendor' => $invoice_vendor ?? null,
                            'order_trucking_id' => $order_trucking ?? $order_vendor ?? null,
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
                // dd($invoice_vendor,$vendor);
                 // Mengambil ID saja
                $nopol = null;
                $container = null;
                $jurnal_external = $data['invoice_external'][$i] ?? null;
                if ($data['order_id'][$i]) {
                    $order_trucking = $data['order_id'][$i];
                    $order = OrderTrucking::find($data['order_id'][$i]);
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
                        'invoice_vendor' => !str_contains($invoice, 'RAS-LT') ? $invoice : null,
                        'invoice_trucking' => str_contains($invoice, 'RAS-LT') ? $invoice : null,
                        'nopol' => $nopol,
                        'container' => $container,
                        'order_trucking_id' => $order_trucking,
                        'nomor' => $nomor,
                        'nama' => $name,
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
                        'invoice_vendor' => !str_contains($invoice, 'RAS-LT') ? $invoice : null,
                        'invoice_trucking' => str_contains($invoice, 'RAS-LT') ? $invoice : null,
                        'nopol' => $nopol,
                        'container' => $container,
                        'order_trucking_id' => $order_trucking,
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
                            'invoice_vendor' => !str_contains($invoice, 'RAS-LT') ? $invoice : null,
                            'invoice_trucking' => str_contains($invoice, 'RAS-LT') ? $invoice : null,
                            'nopol' => $nopol,
                            'container' => $container,
                            'order_trucking_id' => $order_trucking,
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
                            'invoice_vendor' => !str_contains($invoice, 'RAS-LT') ? $invoice : null,
                            'invoice_trucking' => str_contains($invoice, 'RAS-LT') ? $invoice : null,
                            'nopol' => $nopol,
                            'container' => $container,
                            'order_id' => $order_id,
                            'order_trucking_id' => $order_trucking,
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
        $r = 0;
        // dd($request->all());
        foreach ($request->jurnal as $item) {
            $data = $item;
            if (!empty($data['nama'])) {
                $data['created_at'] = date('Y-m-d');
                $data['order_id'] = $data['order_id'] ?? null;
                $data['order_trucking_id'] = $data['order_trucking_id'] ?? null;
                $data['jurnal_balik'] = empty($data['jurnal_balik']) ? null : $data['jurnal_balik'];
                $data['is_balik'] = 1;
                $data['coa_id'] = $request->credit_coa_id_tujuan ?? $request->debit_coa_id_tujuan;
                $data['relasi'] = $request->nomor;
                $data['invoice'] = $data['invoice'] ?? null;
                $data['invoice_external'] = $data['invoice_external'] ?? null;
                $data['invoice_vendor'] = $data['invoice_vendor'] ?? null;
                $data['invoice_agen'] = $data['invoice_agen'] ?? null;
                $data['invoice_trucking'] = $data['invoice_trucking'] ?? null;
                $data['nomor'] = $request->nomor;
                $data['no'] = $request->no;
                $data['tipe'] = $request->tipe;
                $j = Jurnal::create($data);
                if ($j) {
                    if (!empty($data['jurnal_balik'])) {
                        Jurnal::find($data['jurnal_balik'])->update([
                            'jurnal_balik' => $j->id
                        ]);
                    }
                    $r++;
                }
            }
        }
        if ($r == 0) {
            return back()->with('danger', 'Data gagal disimpan');
        }
        return redirect()->route('jurnal.balik.create')->with('success', 'Data berhasil disimpan');
    }

    public function create()
    {
        return view('admin.jurnal.create');
    }

    public function trucking()
    {
        return view('admin.jurnal.trucking');
    }

    public function edit()
    {
        $jurnal = request('jurnal');
        $coa = COA::where('is_active', 1)->orderBy('kode')->get();
        $count = Jurnal::where('nomor', $jurnal)->count();
        $data = Jurnal::where('nomor', $jurnal)->first();
        $deb = Jurnal::where('nomor', $jurnal)->sum('debit');
        $cre = Jurnal::where('nomor', $jurnal)->sum('credit');
        $now = Carbon::now()->addMonths(1)->format('Y-m-d');
        $last = Carbon::now()->subMonths(3)->format('Y-m-d');
        $orders = Order::whereBetween('created_at', [$last, $now])
        ->select('id', 'no_job', 'job', 'seal', 'invoice')
        ->orderBy('job')
        ->orderBy('no_job')
        ->get();
        $orders_expdc = Order::whereBetween('created_at', [$last, $now])
        ->select('id', 'no_job', 'job', 'seal', 'invoice')
        ->whereNotNull('invoice')
        ->orderBy('job')
        ->orderBy('no_job')
        ->get();

    $orders_agen = Order::whereBetween('created_at', [$last, $now])
        ->select('id', 'no_job', 'job', 'seal', 'invoice_agen')
        ->orderBy('job')
        ->whereNotNull('invoice_agen')
        ->orderBy('no_job')
        ->get();

    $orders_trucking = collect();
    $orders_trucking1 = collect(); // Default empty collection
    $orders_vendor = collect(); // Default empty collection

    $tipe = 'xpdc';

    $invx = Jurnal::whereNotNull('invoice_external')
        ->distinct('invoice_external')
        ->orderBy('invoice_external')
        ->pluck('invoice_external')
        ->toArray();

    if ($data->order_trucking_id) {
        $tipe = 'trucking';
        $orders_trucking = OrderTrucking::whereBetween('created_at', [$last, $now])
            ->where('invoice', 'like', '%RAS-LT%')
            ->select('container', 'seal', 'id', 'invoice')
            ->orderBy('container')
            ->get();

        $orders_vendor = OrderTrucking::whereBetween('created_at', [$last, $now])
            ->where('invoice', 'not like', '%RAS-LT%')
            ->select('container', 'seal', 'id', 'invoice')
            ->orderBy('container')
            ->get();
            $orders_trucking1 = OrderTrucking::whereBetween('created_at', [$last, $now])
            ->select('container', 'seal', 'id', 'invoice')
            ->orderBy('container')
            ->get();
    } elseif ($data->order_trucking_id === null && $data->order_id === null) {
        $tipe = 'lain-lain';
        $orders = Order::whereBetween('created_at', [$last, $now])
        ->select('id', 'no_job', 'job', 'seal', 'invoice')
        ->orderBy('job')
        ->orderBy('no_job')
        ->get();
        $orders_expdc = Order::whereBetween('created_at', [$last, $now])
            ->select('id', 'no_job', 'job', 'seal', 'invoice')
            ->orderBy('job')
            ->orderBy('no_job')
            ->get();

        $orders_agen = Order::whereBetween('created_at', [$last, $now])
            ->select('id', 'no_job', 'job', 'seal', 'invoice_agen')
            ->orderBy('job')
            ->orderBy('no_job')
            ->get();

            $orders_trucking1 = OrderTrucking::whereBetween('created_at', [$last, $now])
            ->select('container', 'seal', 'id', 'invoice')
            ->orderBy('container')
            ->get();
        $orders_trucking = OrderTrucking::whereBetween('created_at', [$last, $now])
            ->where('invoice', 'like', '%RAS-LT%')
            ->select('container', 'seal', 'id', 'invoice')
            ->orderBy('container')
            ->get();

        $orders_vendor = OrderTrucking::whereBetween('created_at', [$last, $now])
            ->where('invoice', 'not like', '%RAS-LT%')
            ->select('container', 'seal', 'id', 'invoice')
            ->orderBy('container')
            ->get();
    }
        $jur = $data;
        $bgs = Jurnal::whereNotNull('no_bg')->orderBy('no_bg')->pluck('no_bg')->toArray();
        $bgs = array_unique($bgs);
        $last_relasi = Carbon::now()->subMonths(3)->format('Y-m-d');
        $relasi = Jurnal::where('created_at', '>=', $last_relasi)->distinct('nomor')->orderBy('nomor')->pluck('nomor')->toArray();
        $debit = Jurnal::where('nomor', $jurnal)->whereIn('coa_id', [16, 45, 175])->where('credit', 0)->sum('debit');
        $credit = Jurnal::where('nomor', $jurnal)->whereIn('coa_id', [16, 45, 175])->where('debit', 0)->sum('credit');
        $voucher  = $debit - $credit;
        if ($voucher < 0) {
            $voucher = $voucher * -1;
        }
        // return view('admin.jurnal.edit', compact('data','orders','coa','tipe'));
        return view('admin.jurnal.new_edit', compact('orders','orders_expdc', 'orders_agen', 'orders_trucking', 'orders_trucking1', 'orders_vendor',  'invx','bgs','data', 'relasi', 'coa', 'tipe', 'jur', 'voucher', 'deb', 'cre', 'count'));
    }

    public function editOne(Jurnal $jurnal)
{
    $coa = COA::where('is_active', 1)->orderBy('kode')->get();
    $now = Carbon::now()->addMonths(1)->format('Y-m-d');
    $last = Carbon::now()->subMonths(6)->format('Y-m-d');

    // Default orders and other variables
    $orders = Order::whereBetween('created_at', [$last, $now])
        ->select('id', 'no_job', 'job', 'seal', 'invoice')
        ->orderBy('job')
        ->orderBy('no_job')
        ->get();
    
    $orders_expdc = Order::whereBetween('created_at', [$last, $now])
        ->select('id', 'no_job', 'job', 'seal', 'invoice')
        ->orderBy('job')
        ->whereNotNull('invoice')
        ->orderBy('no_job')
        ->get();

    $orders_agen = Order::whereBetween('created_at', [$last, $now])
        ->select('id', 'no_job', 'job', 'seal', 'invoice_agen')
        ->orderBy('job')
        ->whereNotNull('invoice_agen')
        ->orderBy('no_job')
        ->get();

    $orders_trucking = collect();
    $orders_trucking1 = collect(); // Default empty collection
    $orders_vendor = collect(); // Default empty collection

    $tipe = 'xpdc';

    $invx = Jurnal::whereNotNull('invoice_external')
        ->distinct('invoice_external')
        ->orderBy('invoice_external')
        ->pluck('invoice_external')
        ->toArray();

    if ($jurnal->order_trucking_id) {
        $tipe = 'trucking';
        $orders_trucking1 = OrderTrucking::whereBetween('created_at', [$last, $now])
        ->select('container', 'seal', 'id', 'invoice')
        ->orderBy('container')
        ->get();
        $orders_trucking = OrderTrucking::whereBetween('created_at', [$last, $now])
            ->where('invoice', 'like', '%RAS-LT%')
            ->select('container', 'seal', 'id', 'invoice')
            ->orderBy('container')
            ->get();

        $orders_vendor = OrderTrucking::whereBetween('created_at', [$last, $now])
            ->where('invoice', 'not like', '%RAS-LT%')
            ->select('container', 'seal', 'id', 'invoice')
            ->orderBy('container')
            ->get();
    } elseif ($jurnal->order_trucking_id === null && $jurnal->order_id === null) {
        $tipe = 'lain-lain';
        $orders_trucking1 = OrderTrucking::whereBetween('created_at', [$last, $now])
        ->select('container', 'seal', 'id', 'invoice')
        ->orderBy('container')
        ->get();
        $orders_expdc = Order::whereBetween('created_at', [$last, $now])
            ->select('id', 'no_job', 'job', 'seal', 'invoice')
            ->whereNotNull('invoice')
            ->orderBy('job')
            ->orderBy('no_job')
            ->get();

        $orders_agen = Order::whereBetween('created_at', [$last, $now])
            ->select('id', 'no_job', 'job', 'seal', 'invoice_agen')
            ->orderBy('job')
            ->whereNotNull('invoice_agen')
            ->orderBy('no_job')
            ->get();

        $orders_trucking = OrderTrucking::whereBetween('created_at', [$last, $now])
            ->where('invoice', 'like', '%RAS-LT%')
            ->select('container', 'seal', 'id', 'invoice')
            ->orderBy('container')
            ->get();

        $orders_vendor = OrderTrucking::whereBetween('created_at', [$last, $now])
            ->where('invoice', 'not like', '%RAS-LT%')
            ->select('container', 'seal', 'id', 'invoice')
            ->orderBy('container')
            ->get();
    }

    $bgs = Jurnal::whereNotNull('no_bg')
        ->orderBy('no_bg')
        ->pluck('no_bg')
        ->toArray();
    $bgs = array_unique($bgs);

    $last_relasi = Carbon::now()->subMonths(3)->format('Y-m-d');
    $relasi = Jurnal::where('created_at', '>=', $last_relasi)
        ->distinct('nomor')
        ->orderBy('nomor')
        ->pluck('nomor')
        ->toArray();

    return view('admin.jurnal.form_edit', compact('orders','invx', 'jurnal', 'orders_trucking1', 'orders_expdc', 'orders_agen', 'orders_trucking', 'orders_vendor', 'coa', 'tipe', 'bgs', 'relasi'));
}


    public function updateOne(Request $request, Jurnal $jurnal)
    {
        $data = $request->all();
        $data['relasi'] = $data['relasi'] ?? $request->relasi1;
        $data['invoice'] = null;
        $data['invoice_agen'] = null;
        $data['invoice_trucking'] = null;
        $data['invoice_vendor'] = null;
        $data['order_trucking_id'] = null;
        $data['order_id'] = null;
        $data['nopol'] = null;
        $data['container'] = null;
        if (!empty($data['invoice_external']) || !empty($data['no_bg'])) {
            $data['no_bg'] = $data['no_bg'] ?? null;
            $data['invoice_external'] = $data['invoice_external'] ?? null;
        }
        if (!empty($data['inv_expdc']) || !empty($data['invoice_agen'])) {
            $name = $data['nama'];
            $order_expdc = $data['inv_expdc'] ?? $data['invoice_agen'] ?? null;
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
            $data['invoice'] = $order->invoice ?? null;
            $data['invoice_agen'] = $order->invoice_agen ?? null;
            $data['invoice_trucking'] = null;
            $data['invoice_vendor'] = null;
            $data['order_trucking_id'] = null;
            $data['order_id'] =$order_expdc;
            $data['nopol'] = $order->nopol ?? null;
            $data['container'] = $order->container ?? null;
            $data['nama'] = $name;
        }
        if (!empty($data['inv_trucking'])) {
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
            $data['invoice_vendor'] = null;
            $data['invoice_agen'] = null;
            $data['order_trucking_id'] = $order_expdc;
            $data['container'] = $order->container;
            $data['nama'] = $name;
        }
        if(!empty($data['inv_vendor'])) {
            $name = $data['nama'];
            $order_expdc = $data['inv_vendor'] ?? null;
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
            $data['invoice_vendor'] = $order->invoice ?? null;
            $data['order_trucking_id'] = $order_expdc;
            $data['order_id'] = null;
            $data['invoice'] = null;
            $data['invoice_trucking'] = null;
            $data['invoice_agen'] = null;
            $data['nopol'] = $order->kendaraan->nopol ?? null;
            $data['container'] = $order->container ?? null;
            $data['nama'] = $name;
        }
        if (!empty($data['trucking'])) {
            $name = $data['nama'];
            $order_expdc = $data['trucking'] ?? null;
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
            $data['invoice_vendor'] = !str_contains($invoice, 'RAS-LT') ? $invoice : null;
            $data['invoice_trucking'] = str_contains($invoice, 'RAS-LT') ? $invoice : null;
            $data['order_trucking_id'] = $order_expdc;
            $data['order_id'] = null;
            $data['invoice'] = null;
            $data['invoice_agen'] = null;
            $data['nopol'] = $order->kendaraan->nopol ?? null;
            $data['container'] = $order->container ?? null;
            $data['nama'] = $name;
        }

        if (!empty($data['job'])) {
            $name = $data['nama'];
            $order_expdc = $data['job'] ?? null;
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
            $data['invoice'] = $order->invoice ?? null;
            $data['invoice_agen'] = $order->invoice_agen ?? null;
            $data['invoice_trucking'] = null;
            $data['invoice_vendor'] = null;
            $data['order_trucking_id'] = null;
            $data['order_id'] =$order_expdc;
            $data['nopol'] = $order->nopol ?? null;
            $data['container'] = $order->container ?? null;
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
            $last = $c->endOfMonth()->format('Y-m-d');
            $start = $c->subMonth()->startOfMonth()->format('Y-m-d');
            // $start = '2022-12-01';
            $des = $c->endOfMonth()->format('Y-m-d');
            // dd($start,$des,$last);
            if ($idx == 0) {
                if ($tipe == 'D') {
                    $saldo_awal = Jurnal::where('coa_id', $coa_id)->whereBetween('created_at', ['2022-12-01', $des])->sum('debit') - Jurnal::where('coa_id', $coa_id)->whereBetween('created_at', ['2022-12-01', $des])->sum('credit');
                } else {
                    $saldo_awal = Jurnal::where('coa_id', $coa_id)->whereBetween('created_at', ['2022-12-01', $last])->sum('credit') - Jurnal::where('coa_id', $coa_id)->whereBetween('created_at', ['2022-12-01', $last])->sum('debit');
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
            // Cache jurnal berdasarkan kriteria
            $jurnal = Cache::remember("jurnal_{$coa_id}_{$year}_{$month}_{$order->implode('_')}", 60, function () use ($coa_id, $order, $year, $month) {
                return Jurnal::where('coa_id', $coa_id)
                    ->whereIn('order_id', $order)
                    ->whereNull('order_trucking_id')
                    ->whereNull('invoice_trucking')
                    ->whereNull('invoice_vendor')
                    ->whereNull('invoice_agen')
                    ->whereNotNull('invoice')
                    ->whereYear('input', $year)
                    ->whereMonth('input', $month)
                    ->get(['order_id', 'debit', 'credit']);
            });
            // Proses data untuk hasil akhir
            $finalData = $jurnal->map(function ($item) use ($customer) {
                return [
                    'customer_name' => $customer[$item->order->tarif->customer_id] ?? 'Unknown',
                    'debit' => $item->debit,
                    'credit' => $item->credit,
                ];
            });
            // Kelompokkan dan hitung total
            $groupedData = $finalData->groupBy('customer_name')->map(function ($group) use ($tipe) {
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
            $jurnal = Cache::remember("jurnal_pelayaran_{$coa_id}_{$year}_{$month}", 60, function () use ($coa_id, $year, $month) {
                return Jurnal::with(['order.hutang_pelayaran.pelayaran' => function($query) {
                        $query->select('id', 'nama'); // Pilih kolom 'id' dan 'nama' dari tabel 'pelayaran'
                    }])
                    ->where('coa_id', $coa_id)
                    ->whereNotNull('no_bg')
                    ->whereYear('input', $year)
                    ->whereMonth('input', $month)
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
                return Agen::pluck('nama', 'id');
            });
            $order = Cache::remember("order_agen_list_{$customer->keys()->implode('_')}", 60, function () use ($customer) {
                return Order::whereIn('agen_id', $customer->keys())->pluck('id');
            });
            $jurnal = Cache::remember("jurnal_agen_{$coa_id}_{$year}_{$month}_{$order}", 60, function () use ($coa_id, $year, $month,$order) {
                return Jurnal::where('coa_id', $coa_id)

                    ->whereIn('order_id',$order)
                    ->whereNull('order_trucking_id')
                    ->whereNull('invoice_trucking')
                    ->whereNull('invoice_vendor')
                    ->whereNull('invoice')
                    ->whereNotNull('invoice_agen')
                    ->whereYear('input', $year)
                    ->whereMonth('input', $month)
                    ->get();
            });
            $finalData = $jurnal->map(function ($item) use ($customer) {
                // Ambil nama customer berdasarkan ID dari relasi order_trucking
                $customerName = optional($item->order)->agent->nama ?? 'Unknown'; // Cegah error dengan optional()
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
                ->whereNull('order_id')
                ->whereNotNull('invoice_trucking')
                ->whereNull('invoice_vendor') // Pastikan order_trucking_id tidak null
                ->whereIn('order_trucking_id', $order)
                ->whereYear('input', $year)
                ->whereMonth('input', $month)
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

    if ($subjek == 'customer_xpdc') {
        // Ambil data terkait customer
        $customers = Customer::where('nama', $customer)->pluck('nama', 'id');
        $tarif = Tarif::whereIn('customer_id', $customers->keys())->pluck('id');
        $order = Order::whereIn('tarif_id', $tarif)->pluck('id');

        // Query jurnal
        $jurnal = Jurnal::where('coa_id', $coa_id)
            ->whereIn('order_id', $order)
            ->whereNull('order_trucking_id')
            ->whereNull('invoice_trucking')
            ->whereNull('invoice_vendor')
            ->whereNull('invoice_agen')
            ->whereNotNull('invoice')
            ->whereYear('input', $year)
            ->whereMonth('input', $month)
            ->get(['order_id', 'debit', 'credit', 'nama', 'nomor', 'input', 'invoice']);

        // Kelompokkan jurnal berdasarkan invoice
        $groupedJurnal = $jurnal->groupBy('invoice')->map(function ($items) {
            return [
                'nomor_d' => $items->where('debit', '>', 0)->pluck('nomor')->first(),
                'tgl_d' => $items->where('debit', '>', 0)->pluck('input')->first(),
                'nomor_k' => $items->where('credit', '>', 0)->pluck('nomor')->implode('<br>'),
                'tgl_k' => $items->where('credit', '>', 0)->pluck('input')->implode('<br>'),
                'invoice' => $items->first()->invoice,
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
        $totalSaldo = $totalDebit - $totalCredit;
    }

    if ($subjek == 'agen') {
        // Ambil data terkait customer
        $customers = Agen::where('nama', $customer)->pluck('nama', 'id');
        $order = Order::whereNotNull('invoice_agen')->whereIn('agen_id', $customers->keys())->pluck('id');
        // Query jurnal
        $jurnal = Jurnal::where('coa_id', $coa_id)
            ->whereIn('order_id', $order)
            ->whereNull('order_trucking_id')
            ->whereNull('invoice_trucking')
            ->whereNull('invoice_vendor')
            ->whereNull('invoice')
            ->whereNotNull('invoice_agen')
            ->whereYear('input', $year)
            ->whereMonth('input', $month)
            ->get(['order_id', 'debit', 'credit', 'nama', 'nomor', 'input', 'invoice_agen']);

        // Kelompokkan jurnal berdasarkan invoice
        $groupedJurnal = $jurnal->groupBy('invoice_agen')->map(function ($items) {
            return [
                'nomor_d' => $items->where('debit', '>', 0)->pluck('nomor')->first(),
                'tgl_d' => $items->where('debit', '>', 0)->pluck('input')->first(),
                'nomor_k' => $items->where('credit', '>', 0)->pluck('nomor')->implode('<br>'),
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
        $totalSaldo = $totalDebit - $totalCredit;
    }

    if ($subjek == 'customer_trucking') {
        // Ambil data terkait customer
        $customers = CustomerTrucking::where('nama', $customer)->pluck('nama', 'id');
        $order = OrderTrucking::whereIn('customer_id', $customers->keys())->whereNotNull('invoice')->pluck('id');

        // Query jurnal
        $jurnal = Jurnal::where('coa_id', $coa_id)
            ->whereNull('order_id')
            ->whereIn('order_trucking_id', $order)
            ->whereNull('invoice_vendor')
            ->whereNotNull('invoice_trucking')
            ->whereYear('input', $year)
            ->whereMonth('input', $month)
            ->get(['order_id', 'debit', 'credit', 'nama', 'nomor', 'input', 'invoice_trucking','invoice_vendor']);
        // Kelompokkan jurnal berdasarkan invoice
        $groupedJurnal = $jurnal->groupBy('invoice_trucking')->map(function ($items) {
            return [
                'nomor_d' => $items->where('debit', '>', 0)->pluck('nomor')->first(),
                'tgl_d' => $items->where('debit', '>', 0)->pluck('input')->first(),
                'nomor_k' => $items->where('credit', '>', 0)->pluck('nomor')->implode('<br>'),
                'tgl_k' => $items->where('credit', '>', 0)->pluck('input')->implode('<br>'),
                'invoice_trucking' => $items->first()->invoice_trucking,
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
        $totalSaldo = $totalDebit - $totalCredit;
    }


    if ($subjek == 'pelayaran') {
        // Ambil data terkait 
        $customer1 = json_decode($customer, true); // Dekode JSON menjadi array
        $customers = HutangPelayaran::where('no_bg_opp', $customer1)
        ->orWhere('no_bg_opt', $customer1)
        ->orWhere('no_bg_ut', $customer1) // Tambahkan kondisi orWhere untuk no_bg lainnya
        ->pluck('pelayaran_id');
        $customerPelayaran = Pelayaran::whereIn('id', $customers)->pluck('nama')->first();

    
        // $tarif = Tarif::whereIn('pelayaran_id', $customers->keys())->pluck('id');
        // $order = Order::whereIn('tarif_id', $tarif)->pluck('id','no_bg');
        // dd($order);

        // Query jurnal

        $jurnal = Jurnal::where('coa_id', $coa_id)
            ->whereIn('no_bg', $customer1) // Menggunakan array $customer
            ->whereYear('input', $year)
            ->whereMonth('input', $month)
            ->get(['order_id', 'debit', 'credit', 'nama', 'nomor', 'input', 'no_bg']);
        // Kelompokkan jurnal berdasarkan invoice
        $groupedJurnal = $jurnal->groupBy('no_bg')->map(function ($items) {
            return [
                'nomor_d' => $items->where('debit', '>', 0)->pluck('nomor')->first(),
                'tgl_d' => $items->where('debit', '>', 0)->pluck('input')->first(),
                'nomor_k' => $items->where('credit', '>', 0)->pluck('nomor')->implode('<br>'),
                'tgl_k' => $items->where('credit', '>', 0)->pluck('input')->implode('<br>'),
                'no_bg' => $items->first()->no_bg,
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
        $totalSaldo = $totalDebit - $totalCredit;
    }

    return view('admin.jurnal.buku_besar_pembantu_detail', compact('customerPelayaran','customer', 'subjek', 'totalSaldo', 'groupedJurnal', 'totalDebit', 'totalCredit'));
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
        $data = Jurnal::whereNotNull('order_id')->whereNull('container')->whereBetween('created_at', ['2023-07-01', date('Y-m-d')])->get();
        foreach ($data as $item) {
            $item->update([
                'container' => $item->order->container ?? null,
            ]);
        }

        $data = Jurnal::whereNotNull('order_trucking_id')->whereNull('order_id')->whereBetween('created_at', ['2023-07-01', date('Y-m-d')])->get();
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
        $orders = OrderTrucking::where('invoice', $data['invoice'])->get();
        foreach ($orders as $order) {
            $order_trucking = $order->id;
            # code...
        }

        $month = ['JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 'JULY', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'];

        for ($i = 0; $i < count($data['debit_coa_id']); $i++) {
            if ($data['name'][$i] && $data['amount'][$i]) {
                $name = $data['name'][$i];
                $trx = TransaksiTrucking::where('invoice', $data['invoice'][$i])->first();
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
                        'order_trucking_id' => $order_trucking,
                        'invoice_vendor' => !str_contains($data['invoice'][$i], 'RAS-LT') ? $data['invoice'][$i] : null,
                        'invoice_trucking' => str_contains($data['invoice'][$i], 'RAS-LT') ? $data['invoice'][$i] : null,
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
                        'order_trucking_id' => $order_trucking,
                        'invoice_vendor' => !str_contains($data['invoice'][$i], 'RAS-LT') ? $data['invoice'][$i] : null,
                        'invoice_trucking' => str_contains($data['invoice'][$i], 'RAS-LT') ? $data['invoice'][$i] : null,
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
                            'order_trucking_id' => $order_trucking,
                            'invoice_vendor' => !str_contains($data['invoice'][$i], 'RAS-LT') ? $data['invoice'][$i] : null,
                            'invoice_trucking' => str_contains($data['invoice'][$i], 'RAS-LT') ? $data['invoice'][$i] : null,
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
                            'order_trucking_id' => $order_trucking,
                            'invoice_vendor' => !str_contains($data['invoice'][$i], 'RAS-LT') ? $data['invoice'][$i] : null,
                            'invoice_trucking' => str_contains($data['invoice'][$i], 'RAS-LT') ? $data['invoice'][$i] : null,
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

                TransaksiTrucking::where('invoice', $data['invoice'][$i])->update([
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
