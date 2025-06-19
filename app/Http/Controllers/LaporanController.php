<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\COA;
use App\Models\Customer;
use App\Models\Jurnal;
use Carbon\Carbon;
use App\Models\Kendaraan;
use App\Models\Lokasi;
use App\Models\Order;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Cache;
use App\Models\OrderTrucking;
use App\Models\Pelayaran;
use App\Models\Sopir;
use App\Models\Tarif;
use App\Models\User;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function pelayaran()
    {
        $year = request('year') ?? date('Y');
        $data = Pelayaran::all();
        $count = Order::where('job','LIKE',$year.'%')->count();
        return view('admin.laporan.pelayaran', compact('data','year','count'));
    }

    public function rekap_piutang()
    {
    
    $totalTelahBayar = Jurnal::withTrashed()
    ->where('tipe', 'BBM')
    ->whereNull('deleted_at')
    ->where('debit', '!=', 0)
    ->whereNotNull('invoice')
    ->sum('debit');

    $totalNilaiInvoice = Transaksi::whereNotNull('tanggal_kirim')->sum('total');
    $totalInvoiceCount = Transaksi::whereNotNull('tanggal_kirim')->count('invoice');
    $totalBelumBayar = $totalNilaiInvoice - $totalTelahBayar;

        return view('admin.laporan.rekap-piutang', compact('totalTelahBayar','totalNilaiInvoice',
        'totalInvoiceCount','totalBelumBayar'));
    }
public function data_rekap_piutang(Request $request)
{
    $page = $request->input('page', 1);
    $rows = $request->input('rows', 20);
    $searchField = $request->input('searchField');
    $searchString = $request->input('searchString');
    $tglInvFilter = $request->input('tgl_inv');
    $invFilter = $request->input('inv');

    // Ambil invoice dengan relasi yang diperlukan
    $orders = Order::with([
        'tarif.customer:id,nama,top',
        'transaksi:id,job,total,pph'
    ])
    ->select('id', 'invoice', 'invoice_date', 'job', 'tarif_id', 'created_at')
    ->when($searchField && $searchString, function ($q) use ($searchField, $searchString) {
        $q->where($searchField, 'like', "%$searchString%");
    })
    ->when($tglInvFilter, function ($q) use ($tglInvFilter) {
        $q->where('invoice_date', 'like', "%$tglInvFilter%");
    })
    ->when($invFilter, function ($q) use ($invFilter) {
        $q->where('invoice', 'like', "%$invFilter%");
    })
    ->orderByDesc('created_at')
    ->get();

    // Index untuk mapping
    $ordersByInvoice = $orders->groupBy('invoice');
    $transaksis = $orders->pluck('transaksi', 'invoice');
    $customers = $orders->pluck('tarif.customer', 'invoice');

    // Ambil jurnal dan group by invoice
    $jurnals = Jurnal::withTrashed()
        ->where('tipe', 'BBM')
        ->whereNull('deleted_at')
        ->where('credit', '!=', 0)
        ->whereNotNull('invoice')
        ->select(
            'invoice',
            \DB::raw('SUM(credit) as total_credit'),
            \DB::raw("GROUP_CONCAT(DATE_FORMAT(created_at, '%Y-%m-%d') ORDER BY created_at ASC SEPARATOR '<br>') as daftar_tanggal")
        )
        ->groupBy('invoice')
        ->get()
        ->keyBy('invoice');

    // Hitung data rekap
    $rekapData = $ordersByInvoice->map(function ($group, $invoice) use ($transaksis, $customers, $jurnals) {
        $trans = $transaksis[$invoice] ?? null;
        $cust = $customers[$invoice] ?? null;

        $subtotal = $trans->total ?? 0;
        $pph = $trans->pph ?? 0;
        $jumlah_harga = round($subtotal);
        $top = (int)($cust->top ?? 0);
        $invoiceDate = $group->first()->invoice_date;
        $tempo = Carbon::parse($invoiceDate)->addDays($top)->format('Y-m-d');

        $jurnal = $jurnals[$invoice] ?? null;
        $dibayar_tgl = $jurnal->daftar_tanggal ?? null;
        $sebesar = $jurnal->total_credit ?? 0;
        $kurang_bayar = $jumlah_harga - $sebesar;

        $warna_status = '';

        // Jika lunas
        if ($kurang_bayar == 0) {
            $warna_status = 'hijau';
        }

         elseif ($kurang_bayar < 0) {
            $warna_status = 'biru';
        }
        // Jika PPh sama dengan kurang bayar
        elseif ($pph == $kurang_bayar) {
            $warna_status = 'oranye';
        }
        // Jika jatuh tempo dalam 1-4 hari ke depan
        elseif (Carbon::parse($tempo)->isFuture()) {
            $daysDiff = Carbon::now()->diffInDays(Carbon::parse($tempo), false);
            if ($daysDiff > 0 && $daysDiff <= 4) {
                $warna_status = 'kuning';
            }
        }
        // Jika sudah jatuh tempo
        elseif (Carbon::parse($tempo)->isPast()) {
            $warna_status = 'merah';
        }


        return [
            'tanggal' => now()->toDateString(),
            'invoice' => $invoice,
            'customer' => $cust->nama ?? '-',
            'jumlah_harga' => $jumlah_harga,
            'pph' => round($pph),
            'top' => $top,
            'ditagih_tgl' => $invoiceDate,
            'tempo' => $tempo,
            'hitung_tempo' => Carbon::parse($invoiceDate)->addDays($top),
            'dibayar_tgl' => $dibayar_tgl,
            'sebesar' => $sebesar,
            'kurang_bayar' => $kurang_bayar,
            'warna_status' => $warna_status, // <== TAMBAH DI SINI
        ];
    })->values();

    // Filter berdasarkan tanggal ditagih jika ada
    $ditagihFilter = $request->input('ditagih_tgl');
    if ($ditagihFilter) {
        $rekapData = $rekapData->filter(function ($row) use ($ditagihFilter) {
            return Str::contains($row['ditagih_tgl'], $ditagihFilter);
        })->values();
    }
    
    // Tambahkan filter dari jqGrid (khusus untuk warna_status)
$filters = $request->input('filters');
if ($filters) {
    $filterRules = json_decode($filters, true)['rules'] ?? [];
    foreach ($filterRules as $rule) {
        if ($rule['field'] === 'warna_status') {
            $value = $rule['data'];
            $rekapData = $rekapData->filter(fn($item) => $item['warna_status'] === $value)->values();
        }
    }
}
// Pagination
    $totalRecords = $rekapData->count();
    $indexStart = ($page - 1) * $rows;
    $paginated = $rekapData->slice($indexStart, $rows)->values()->map(function ($item, $index) use ($indexStart) {
        $item['no'] = $indexStart + $index + 1;
        return $item;
    });

    return response()->json([
        'rows' => $paginated,
        'current_page' => $page,
        'last_page' => ceil($totalRecords / $rows),
        'total' => $totalRecords,
        'records' => $totalRecords,
    ]);
}

public function data_total_rekap_piutang(Request $request)
{
    $page = $request->input('page', 1);
    $rows = $request->input('rows', 20);
    $searchField = $request->input('searchField');
    $searchString = $request->input('searchString');
    $thn_inv = $request->input('thn_inv', date('Y'));

    $filterKey = md5($searchField . '_' . $searchString);
    $cacheKeyInvoices = 'invoices_' . $thn_inv . '_' . $filterKey;
    $cacheKeyJurnals = 'jurnals_' . $thn_inv;

    // Ambil data invoice dengan job
    $invoices = Cache::remember($cacheKeyInvoices, now()->addMinutes(60), function () use ($thn_inv, $searchField, $searchString) {
        return Order::with(['transaksi']) // meskipun tidak dipakai relasinya
            ->select('id', 'invoice', 'invoice_date', 'job')
            ->when($searchField && $searchString, function ($q) use ($searchField, $searchString) {
                return $q->where($searchField, 'like', "%$searchString%");
            })
            ->whereNull('deleted_at')
            ->whereYear('invoice_date', $thn_inv)
            ->whereNotNull('invoice')
            ->orderBy('created_at', 'desc')
            ->get();
    });

    // Ambil job unik dari invoices
    $jobs = $invoices->pluck('job')->filter()->unique()->values();
    $transaksis = Transaksi::whereNotNull('tanggal_kirim')->whereIn('job', $jobs)->pluck('total', 'job');


    // Ambil data jurnal
    $jurnals = Cache::remember($cacheKeyJurnals, now()->addMinutes(60), function () use ($thn_inv) {
        return Jurnal::withTrashed()
            ->select('invoice', 'debit', 'created_at')
            ->where('tipe', 'BBM')
            ->whereNull('deleted_at')
            ->where('debit', '!=', 0)
            ->whereNotNull('invoice')
            ->whereYear('created_at', $thn_inv)
            ->orderBy('created_at', 'desc')
            ->get();
    });

    $jurnalsPerInvoice = $jurnals->groupBy('invoice')->map(fn($group) => $group->sum('debit'));

    // Grup dan hitung data per bulan
    $data = $invoices->groupBy(fn($invoice) => Carbon::parse($invoice->invoice_date)->format('Y-m'))
        ->map(function ($group) use ($jurnalsPerInvoice, $transaksis) {
            $subtotal = $group->pluck('job')->unique()->sum(function ($job) use ($transaksis) {
    return $transaksis[$job] ?? 0;
});

            $jumlah_harga = round($subtotal);

            $invoiceNumbers = $group->pluck('invoice')->filter()->unique();
            $telah_bayar = $invoiceNumbers->sum(fn($inv) => $jurnalsPerInvoice[$inv] ?? 0);
            $belum_dibayar = $jumlah_harga - $telah_bayar;

            return [
                'bulan' => Carbon::parse($group->first()->invoice_date)->format('Y-m'),
                'nilai_invoice' => $jumlah_harga,
                'total_invoice' => $invoiceNumbers->count(),
                'telah_bayar' => $telah_bayar,
                'belum_dibayar' => $belum_dibayar,
            ];
        });

    // Finalisasi
    $result = [];
    $index = 1;
    $totalTelahBayar = 0;
    $totalBelumBayar = 0;
    $totalInvoice = 0;
    $nilaiInvoice = 0;

    foreach ($data as $item) {
        $item['no'] = $index++;
        $totalTelahBayar += $item['telah_bayar'];
        $totalBelumBayar += $item['belum_dibayar'];
        $totalInvoice += $item['total_invoice'];
        $nilaiInvoice += $item['nilai_invoice'];
        $result[] = $item;
    }


    // Pagination
    $indexStart = ($page - 1) * $rows;
    $paginatedData = collect($result)->slice($indexStart, $rows)->values();
    $totalRecords = count($result);
    $totalPages = ceil($totalRecords / $rows);

    return response()->json([
        'rows' => $paginatedData,
        'current_page' => $page,
        'last_page' => $totalPages,
        'total' => $totalPages,
        'records' => $totalRecords,
        'sum_telah_bayar' => $totalTelahBayar,
        'sum_belum_bayar' => $totalBelumBayar,
        'count_invoice' => $totalInvoice,
        'sum_nilai_invoice' => $nilaiInvoice
    ]);
}


    public function tujuan()
    {
        $tarif = Tarif::pluck('tujuan')->toArray();
        $id = array_unique($tarif);
        $year = request('year') ?? date('Y');
        $data = Lokasi::whereIn('id',$id)->get();
        $count = Order::where('job','LIKE',$year.'%')->count();
        return view('admin.laporan.tujuan', compact('data','year','count'));
    }
    public function customer()
    {
        $tarif = Tarif::pluck('customer_id')->toArray();
        $id = array_unique($tarif);
        $year = request('year') ?? date('Y');
        $data = Customer::whereIn('id',$id)->get();
        $count = Order::where('job','LIKE',$year.'%')->count();
        return view('admin.laporan.customer', compact('data','year','count'));
    }
    public function omset_customer()
    {
        $tarif = Tarif::pluck('customer_id')->toArray();
        $id = array_unique($tarif);
        $year = request('year') ?? date('Y');
        $data = Customer::whereIn('id',$id)->get();
        return view('admin.laporan.omset_customer', compact('data','year'));
    }
    public function marketing()
    {
        $year = request('year') ?? date('Y');
        $data = User::where('role_id',2)->whereHas('marketing')->get();
        return view('admin.laporan.marketing', compact('data','year'));
    }
    public function cs()
    {
        $year = request('year') ?? date('Y');
        $data = User::where('role_id',2)->whereHas('cs')->get();
        return view('admin.laporan.cs', compact('data','year'));
    }
    public function trucking()
    {
        $year = request('year') ?? date('Y');
        $data = Kendaraan::where('milik','!=','vendor')->where('is_active',1)->get();
        return view('admin.laporan.trucking', compact('data','year'));
    }
    public function sopir()
    {
        $year = request('year') ?? date('Y');
        $data = Sopir::where('milik','!=','vendor')->get();
        return view('admin.laporan.sopir', compact('data','year'));
    }
    public function omset()
    {
        $year = request('year') ?? date('Y');
        $month = request('month') ?? date('m');
        $tipe = request('tipe') ?? 'inv';
        $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $job = $year.sprintf('%02d',$month);
        if($tipe=='inv'){
            $data = Order::whereMonth('invoice_date',$month)->where('lock_omset','!=',0)->whereYear('invoice_date',$year)->get();
        }else{
            $data = Order::where('job','like',$job.'%')->get();
        }
        $ids = $data->pluck('id')->toArray();
        $coa = COA::where('is_active',1)->get();
        $is_pra = false;
        return view('admin.laporan.omset', compact('is_pra','data','year','months','month','tipe','ids','coa'));
    }
    public function praomset()
    {
        $year = request('year') ?? date('Y');
        $month = request('month') ?? date('m');
        $tipe = request('tipe') ?? 'inv';
        $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $job = $year.sprintf('%02d',$month);
        if($tipe=='inv'){
            $data = Order::whereMonth('invoice_date',$month)->whereYear('invoice_date',$year)->get();
        }else{
            $data = Order::where('job','like',$job.'%')->get();
        }
        $ids = $data->pluck('id')->toArray();
        $coa = COA::where('is_active',1)->get();
        $is_pra = true;
        return view('admin.laporan.pra_omset', compact('is_pra','data','year','months','month','tipe','ids','coa'));
    }
    public function invoice()
    {
        $year = request('year') ?? date('Y');
        $data = Order::whereNull('invoice')->get();
        $data = OrderResource::collection($data);
        return view('admin.laporan.preinvoice', compact('data','year'));
    }
    public function omset_trucking()
    {
        $year = request('year') ?? date('Y');
        $month = request('month') ?? date('m');
        $tipe = request('tipe') ?? 'xpdc';
        $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        if($tipe=='xpdc'){
            $order_job = Order::whereMonth('invoice_date',$month)->whereYear('invoice_date',$year)->pluck('id')->toArray();
            $orders = OrderTrucking::whereIn('order_id',$order_job)->get();
            $get_id = array();
            foreach($orders as $order_trucking){
                if($order_trucking->order){
                    $tipe_truck = $order_trucking->kendaraan->milik;
                    if($order_trucking->customer->r2 == 1){
                        $tipe_truck = 'R2';
                    }
                    if($order_trucking->customer->r1 == 1){
                        $tipe_truck = 'R1';
                    }
                    if(($order_trucking->order->trucking == 'xpdc' || $order_trucking->order->trucking == 'XPDC') && $tipe_truck == 'R2'){
                        array_push($get_id,$order_trucking->id);
                    }
                }
            }
            $jurnal_id = Jurnal::whereIn('order_trucking_id',$get_id)->whereIn('coa_id',[61,81])->pluck('id')->toArray();
            $data = OrderTrucking::whereIn('id',$get_id)->get()->groupBy('seal');
        }else{
            $order_id = Jurnal::whereNotNull('order_trucking_id')->whereMonth('created_at',$month)->whereYear('created_at',$year)->where('coa_id',87)->pluck('order_trucking_id')->toArray();
            $jurnal_id = Jurnal::whereNotNull('order_trucking_id')->whereMonth('created_at',$month)->whereYear('created_at',$year)->where('coa_id',87)->pluck('id')->toArray();
            $data = OrderTrucking::whereIn('id',$order_id)->get()->groupBy('seal');
        }
        return view('admin.laporan.omset_trucking', compact('data','year','months','month','tipe','jurnal_id'));
    }
}
