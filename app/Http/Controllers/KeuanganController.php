<?php

namespace App\Http\Controllers;

use App\Exports\LaporanPPNExport;
use App\Exports\PajakExport;
use App\Exports\MultipleSheetExport;
use App\Http\Resources\LaporanPPNResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\TransaksiResource;
use App\Imports\InvoiceImport;
use App\Models\Customer;
use App\Models\Lokasi;
use App\Models\NSFP;
use App\Models\Tarif;
use App\Models\User;
use App\Models\Order;
use App\Models\Transaksi;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

class KeuanganController extends Controller
{
    public function order()
    {
        return view('admin.keuangan.order');
    }

    public function ba_kembali()
    {
        return view('admin.keuangan.ba_kembali');
    }
    public function draft_invoice(Request $request)
    {
        return view('admin.keuangan.draft_invoice');
    }

    public function draft_invoice1(Request $request)
    {
        return view('admin.keuangan.draft1_invoice');
    }

    public function draftInvoiceData(Request $request)
    {
        $orders = Order::with([
            'tarif.customer.marketing',
            'tarif.customer.cs',
            'barang',
            'pengirim',
            'penerima',
            'tarif.dari_lokasi',
            'jadwal_kapal.kapal',
            'tarif.shipmentInfo'
        ]);

        // Tambahkan pencarian berdasarkan parameter filter yang diterima
        $searchFilters = $request->input('_search') ? $request->only('searchField', 'searchString', 'searchOper') : [];

        if (!empty($searchFilters)) {
            foreach ($searchFilters as $field => $value) {
                if ($value) {
                    $orders->where($field, 'like', "%$value%");
                }
            }
        }

        // Paginasi
        $totalRecords = $orders->count();
        $page = (int)$request->input('page', 1);
        $limit = (int)$request->input('rows', 10);
        $start = ($page - 1) * $limit;

        // Pastikan page tidak lebih besar dari total halaman
        $totalPages = ceil($totalRecords / $limit);
        $page = min($page, $totalPages);  // Pastikan page tidak lebih besar dari totalPages

        $paginatedOrders = $orders->skip($start)->take($limit)->get();

        // Format data untuk jqGrid
        $rows = $paginatedOrders->map(function ($order) {
            return [
                'created_at' => $order->created_at->format('d/m/y'),
                'marketing' => optional($order->tarif->customer->marketing)->name ?? '-',
                'cs' => optional($order->tarif->customer->cs)->name ?? '-',
                'job_number' => $order->job . '-' . sprintf('%02d', $order->no_job),
                'invoice' => $order->invoice ?? '-',
                'customer' => optional($order->tarif->customer)->nama ?? '-',
                'barang' => optional($order->barang)->nama ?? '-',
                'pengirim' => optional($order->pengirim)->nama ?? '-',
                'penerima' => optional($order->penerima)->nama ?? '-',
                'trucking' => $order->trucking ?? '-',
                'seal' => $order->seal ?? '-',
                'container' => $order->container ?? '-',
                'nopol' => $order->nopol ?? '-',
                'dari_lokasi' => optional($order->tarif->dari_lokasi)->nama ?? '-',
                'kapal' => optional($order->jadwal_kapal->kapal)->nama ?? '-',
                'voyage' => $order->jadwal_kapal->voyage ?? '-',
                'shipment_info' => $order->tarif->shipmentInfo->nama ?? '-',
            ];
        });

        $response = [
            'page' => $page,
            'total' => $totalPages,  // Total halaman yang benar
            'records' => $totalRecords,
            'rows' => $rows, // Konversi ke array
        ];

        return response()->json($response);
    }

    public function draftInvoiceData1(Request $request)
    {
        $orders = Order::with([
            'tarif.customer.marketing',
            'tarif.customer.cs',
            'barang',
            'pengirim',
            'penerima',
            'tarif.dari_lokasi',
            'jadwal_kapal.kapal',
            'tarif.shipmentInfo'
        ])->whereNull('invoice')
        ->orderBy('created_at', 'DESC'); // ⬅️ URUTAN DESC;

        // Tambahkan pencarian berdasarkan parameter filter yang diterima
        $searchFilters = $request->input('_search') ? $request->only('searchField', 'searchString', 'searchOper') : [];

        if (!empty($searchFilters)) {
            foreach ($searchFilters as $field => $value) {
                if ($value) {
                    $orders->where($field, 'like', "%$value%");
                }
            }
        }

        if (request('job')) {
            $orders->where('order.job', 'LIKE', '%' . request('job') . '%');
        }
         if (request('customer')) {
            $orders->whereHas('tarif', function ($q) {
                $q->whereHas('customer', function ($a) {
                     $a->where('nama', 'LIKE', '%' . request('customer') . '%');
                });
            });
        }

        // Paginasi
        $totalRecords = $orders->count();
        $page = (int)$request->input('page', 1);
        $limit = (int)$request->input('rows', 10);
        $start = ($page - 1) * $limit;

        // Pastikan page tidak lebih besar dari total halaman
        $totalPages = ceil($totalRecords / $limit);
        $page = min($page, $totalPages);  // Pastikan page tidak lebih besar dari totalPages

        $paginatedOrders = $orders->skip($start)->take($limit)->get();

        // Format data untuk jqGrid
        $rows = $paginatedOrders->map(function ($order) {
            return [
                 'order_id' => $order->id ?? '-',
    'created_at' => optional($order->created_at)->format('d/m/y') ?? '-',

    'marketing' => optional(optional(optional($order->tarif)->customer)->marketing)->name ?? '-',
    'cs' => optional(optional(optional($order->tarif)->customer)->cs)->name ?? '-',

    'job' => ($order->job ?? '-') . '-' . sprintf('%02d', $order->no_job ?? 0),
    'invoice' => $order->invoice ?? '-',

    'customer' => optional(optional($order->tarif)->customer)->nama ?? '-',
    'barang' => optional($order->barang)->nama ?? '-',
    'pengirim' => optional($order->pengirim)->nama ?? '-',
    'penerima' => optional($order->penerima)->nama ?? '-',

    'trucking' => $order->trucking ?? '-',
    'seal' => $order->seal ?? '-',
    'container' => $order->container ?? '-',
    'nopol' => $order->nopol ?? '-',

    'dari_lokasi' => optional(optional($order->tarif)->dari_lokasi)->nama ?? '-',
    'kapal' => optional(optional($order->jadwal_kapal)->kapal)->nama ?? '-',
    'voyage' => optional($order->jadwal_kapal)->voyage ?? '-',
    'shipment' => optional(optional($order->tarif)->shipmentInfo)->nama ?? '-',

    'is_draft' => $order->is_draft ?? 0,
            ];
        });

        $response = [
            'page' => $page,
            'total' => $totalPages,  // Total halaman yang benar
            'records' => $totalRecords,
            'rows' => $rows, // Konversi ke array
        ];

        return response()->json($response);
    }


    public function customer()
    {
        return view('admin.keuangan.customer');
    }

    public function fee_cust()
    {
        $data = Order::where('komisi', '>', 0)->get();
        return view('admin.keuangan.fee_cust', compact('data'));
    }

    public function fee_cust_bayar(Request $request)
    {
        $id = explode(',', $request->order_id);
        if (count($id) == 0) {
            return back()->with('danger', 'Harap checklist item!');
        }
        if (request('komisi_print')) {
            Order::whereIn('id', $id)->update([
                'komisi_print' => $request->komisi_print
            ]);
        }
        $orders = Order::whereIn('id', $id)->get();
        $order = $orders->first();
        return view('admin.cetak.fee_cust', compact('orders', 'order', 'id'));
    }

    public function pre_invoice()
    {
        $data1_id = [];
        $data1 = Order::whereHas('tarif', function ($q) {
            $q->whereIn('kondisi', [1, 6]);
        })->whereHas('jadwal_kapal', function ($q) {
            $q->whereNotNull('td');
        })->whereNull('invoice')->pluck('id');
        foreach ($data1 as $item) {
            array_push($data1_id, $item);
        }

         $data4 = Order::whereHas('tarif', function ($q) {
            $q->whereIn('kondisi', [5, 7,10,8,9]);
            $q->whereHas('customer', function ($qu) {
                $qu->where('ba_kembali', 0);
            });
        })->whereHas('jadwal_kapal', function ($q) {
            $q->whereNotNull('td');
        })->whereNull('invoice')->pluck('id');
        foreach ($data4 as $item) {
            array_push($data1_id, $item);
        }

        $data2 = Order::whereHas('tarif', function ($q) {
            $q->whereIn('kondisi', [5, 7, 10,8,9]);
            $q->whereHas('customer', function ($qu) {
                $qu->where('ba_kembali', 1);
            });
        })->whereHas('jadwal_kapal', function ($q) {
            $q->whereNotNull('td');
        })->whereNull('invoice')->whereNotNull('ba_kembali')->get();
        foreach ($data2 as $item) {
                array_push($data1_id, $item->id);
        }

        $data3 = Order::whereHas('tarif', function ($q) {
            $q->whereIn('kondisi', [5, 7, 10, 9,8]);
            $q->whereHas('customer', function ($qu) {
                $qu->where('ba_kembali', 0);
            });
        })->whereHas('jadwal_kapal', function ($q) {
            $q->whereNotNull('td');
        })->whereNull('invoice')->get();

        $data1 = Order::whereIn('id', $data1_id)->get();
        $data1 = OrderResource::collection($data1);
        $data2 = OrderResource::collection($data3);

        return view('admin.keuangan.pre_invoice2', compact('data1', 'data2'));
    }

    public function pre_invoice1()
    {
        return view('admin.keuangan.pre_invoice1');
    }

    public function invoice()
    {
        $start_date = request('start_date') ?? date('Y-m') . '-01';
        $end_date = request('end_date') ?? Carbon::now()->endOfMonth()->format('Y-m-d');
        return view('admin.keuangan.invoice', compact('start_date', 'end_date'));
    }

    public function generateInvoice(Request $request, Order $order)
    {
        $setting = Setting::find(1);
        $data = $request->all();
        $tahunLompat = $setting->tahun_lompat; // contoh: 2025
        $tahunLompaty = substr($tahunLompat, -2); // hasil: 25
        $customer_id = $order->tarif->customer->id;
        // $job = $data['job'];
        // $year = substr($job, 0, 4);
        $nsfp = null;
        if ($customer_id != 318 && $customer_id != 3134) {
            $nsfp = NSFP::where('available', 1)->orderBy('nomor', 'asc')->first();
            if (!$nsfp) {
                return back()->with('danger', 'Tidak ada NSFP yang tersedia! Harap input NSFP terlebih dahulu');
            }
        }
        $no = Transaksi::whereYear('created_at', $tahunLompat)->max('order') + 1;
        $roman_numerals = array("", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII"); // daftar angka Romawi
        $month_number = date("n"); // mengambil nomor bulan dari tanggal
        $month_roman = $roman_numerals[$month_number]; // mengambil angka Romawi yang sesuai
        $invoice = sprintf('%04d', $no) . '/' . $setting->short_name . '/' . $month_roman . '/' . $tahunLompaty;
        $data['invoice'] = $invoice; 
        $data['nsfp'] = $nsfp->nomor ?? null;
        $data['order'] = $no;
        $data['order_id'] = $order->id;
        $data['created_at'] = date('Y-m-d');
        Transaksi::create($data);
        Order::where('job', $order->job)->update([
            'invoice' => $invoice,
            'nsfp' => $nsfp->nomor ?? null,
            'invoice_date' => date('Y-m-d'),
            'lock_biaya' => 1
        ]);
        if ($nsfp) {
            $nsfp->update([
                'available' => 0,
                'invoice' => $invoice
            ]);
        }

        return back()->with('success', 'Invoice berhasil dibuat');
    }

    public function laporanPPn()
    {
        $start = request('start') ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $end = request('end') ?? Carbon::now()->endOfMonth()->format('Y-m-d');
        $transaksi = Transaksi::whereBetween('created_at', [$start, $end])->orderBy('created_at')->get();
        // dd($transaksi);
        $data = TransaksiResource::collection($transaksi);
        $faktur = NSFP::where('available', 1)->first();
       $startOfYear = Carbon::now()->startOfYear(); // 1 Januari tahun ini
$now = Carbon::now(); // waktu sekarang

$invoices = Order::whereNotNull('invoice')
        ->whereBetween('created_at', [$startOfYear, $now])
        ->distinct()
        ->pluck('invoice'); // ubah ke array biasa
$no = '-';
if ($faktur) {
    $no = '010' . substr($faktur->nomor, 3, 50);
}
$customers = Customer::pluck('nama');
        $lokasi = Lokasi::pluck('nama');
        $ppn = $transaksi->sum('ppn');
        $pph = $transaksi->sum('pph');
        $total = $transaksi->sum('total');
        $sub_total = $transaksi->sum('sub_total');
        return view('admin.keuangan.laporan_ppn', compact('invoices','transaksi', 'data', 'start', 'end', 'no', 'customers', 'lokasi', 'ppn', 'pph', 'total', 'sub_total'));
    }

    public function PPNExport()
    {
        return Excel::download(new LaporanPPNExport(request('start'), request('end')), 'laporan.xlsx');
    }

    public function PajakExport()
    {
        return Excel::download(new PajakExport(request('start'), request('end')), 'laporan_pajak.csv', \Maatwebsite\Excel\Excel::CSV, ['Content-Type' => 'text/csv',]);
    }

    public function XmlExport()
    {
       return Excel::download(
    new MultipleSheetExport(request('start'), request('end')),
    'bahan_xml.xlsx',
    \Maatwebsite\Excel\Excel::XLSX
);

    }


    public function invoiceTable()
    {
        $limit = request('length');
        $start = request('start') * request('length');
        $data = Transaksi::query()
            ->join('customers', 'customers.id', '=', 'transaksi.pembayar_id')
            ->select('transaksi.*');
        $count = $data->count();
        return Datatables::of($data->offset($start)->limit($limit))
            ->order(function ($query) {
                $query->orderBy('order');
            })
            ->addColumn('invoice', function ($data) {
                return $data->invoice;
            })
            ->addColumn('created_at', function ($data) {
                return date('d/m/Y', strtotime($data->created_at)) ?? '-';
            })
            ->addColumn('job', function ($data) {
                return $data->job;
            })
            ->addColumn('no_job', function ($data) {
                return $data->job . '-01/' . sprintf('%02d', $data->jobs->count());
            })
            ->addColumn('pembayar', function ($data) {
                return $data->pembayar->nama ?? '-';
            })
            ->addColumn('tanggal_kirim', function ($data) {
                return is_null($data->tanggal_kirim) ? '-' : date('d/m/Y', strtotime($data->tanggal_kirim));
            })
            ->addColumn('total', function ($data) {
                return number_format($data->total) ?? '-';
            })
            ->setFilteredRecords($count)
            ->toJson();
    }

    public function import(Request $request)
    {
        Excel::import(new InvoiceImport, $request->file);

        return back()->with('success', 'All good!');
    }
}
