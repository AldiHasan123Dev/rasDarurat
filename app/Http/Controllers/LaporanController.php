<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\COA;
use App\Models\Customer;
use App\Models\Jurnal;
use Carbon\Carbon;
use App\Models\Kendaraan;
use App\Models\Lokasi;
use App\Exports\RekapPiutangExport;
use Maatwebsite\Excel\Facades\Excel;
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
    $customers = Customer::select('nama')->distinct()->get();

        return view('admin.laporan.rekap-piutang', compact('totalTelahBayar','totalNilaiInvoice',
        'totalInvoiceCount','totalBelumBayar','customers'));
    }

    public function data_rekap_piutang_addcost(Request $request)
{
    $page = $request->input('page', 1);
    $rows = $request->input('rows', 20);
    $searchField = $request->input('searchField');
    $searchString = $request->input('searchString');
    $tglInvFilter = $request->input('tgl_inv');
    $customersFilter = $request->input('customers');
    $invFilter = $request->input('inv');

    // Ambil total debit per invoice
    $jurnalInvX = Jurnal::withTrashed()
        ->select('order_id', 'invoice_external', \DB::raw('SUM(debit) as total_debit'))
        ->where('coa_id', 46)
        ->whereNull('deleted_at')
        ->where('debit', '!=', 0)
        ->whereNotNull('invoice_external')
        ->groupBy('invoice_external', 'order_id')
        ->get()
        ->keyBy('invoice_external');

    // Ambil total credit dan daftar tanggal bayar per invoice
    $jurnals = Jurnal::withTrashed()
        ->where('coa_id', 46)
        ->whereNull('deleted_at')
        ->where('credit', '!=', 0)
        ->whereNotNull('invoice_external')
        ->select(
            'order_id',
            'invoice_external',
            \DB::raw('SUM(credit) as total_credit'),
            \DB::raw("GROUP_CONCAT(DATE_FORMAT(created_at, '%Y-%m-%d') ORDER BY created_at ASC SEPARATOR '<br>') as daftar_tanggal")
        )
        ->groupBy('invoice_external', 'order_id')
        ->get()
        ->keyBy('invoice_external');

    // Group data berdasarkan invoice_external
    $ordersByInvoice = $jurnalInvX->groupBy('invoice_external');

    // Ambil total_credit dan order_id untuk setiap invoice_external
    $jurnalNilai = $jurnals->mapWithKeys(function ($item) {
        return [
            $item->invoice_external => [
                'order_id' => $item->order_id,
                'total_credit' => $item->total_credit,
            ]
        ];
    });

    // Ambil data customer dari relasi order.tarif.customer
    $customers = $jurnalInvX->pluck('order.tarif.customer', 'invoice_external');

    // Proses rekap per invoice
    $rekapData = $ordersByInvoice->map(function ($group, $invoice) use ($customers, $jurnals, $jurnalInvX) {
        $cust = $customers[$invoice] ?? null;
        if (!$cust || !$cust->nama) {
    return null; // Lewati jika customer tidak ditemukan
}
        $jurnalDebit = $jurnalInvX[$invoice]->total_debit ?? 0;

        $jumlah_harga = round($jurnalDebit);
        $top = (int)($cust->top ?? 0);
       $invoiceDates = $group->first()->created_at ?? null;



$invoiceDate = Carbon::parse($invoiceDates)->subDay();


        $tempoDate = Carbon::parse($invoiceDate)->addDays($top);
        $tempoFormatted = $tempoDate->format('Y-m-d');

        $jurnal = $jurnals[$invoice] ?? null;
        $dibayar_tgl = $jurnal->daftar_tanggal ?? null;
        $sebesar = $jurnal->total_credit ?? 0;
        $kurang_bayar = $jumlah_harga - $sebesar;

        $today = Carbon::now();
        $daysDiff = $tempoDate->diffInDays($today, false);
        $warna_status = '';

        if ($kurang_bayar == 0) {
            $warna_status = 'hijau';
        } elseif ($kurang_bayar < 0) {
            $warna_status = 'biru';
        } elseif ($tempoDate->isFuture()) {
            $diff = Carbon::now()->diffInDays($tempoDate, false);
            if ($diff > 0 && $diff <= 4) {
                $warna_status = 'kuning';
            }
        } elseif ($daysDiff > 0) {
            $warna_status = 'merah';
        }

        return [
            'tanggal' => now()->toDateString(),
            'invoice_external' => $invoice,
            'customer' => $cust->nama ?? '-',
            'jumlah_harga' => $jumlah_harga,
            'top' => $top,
            'ditagih_tgl' => $invoiceDate,
            'tempo' => $tempoFormatted,
            'hitung_tempo' => $tempoDate->copy()->addDay()->format('Y-m-d'),
            'dibayar_tgl' => $dibayar_tgl,
            'sebesar' => $sebesar,
            'kurang_bayar' => $kurang_bayar,
            'warna_status' => $warna_status,
        ];
    })->filter()->sortByDesc('invoice_external')->values();

    // Filter berdasarkan tanggal ditagih
    if ($ditagihFilter = $request->input('ditagih_tgl')) {
        $rekapData = $rekapData->filter(function ($row) use ($ditagihFilter) {
            return Str::contains($row['ditagih_tgl'], $ditagihFilter);
        })->values();
    }

    // Filter warna_status dari jqGrid
    if ($filters = $request->input('filters')) {
        $filterRules = json_decode($filters, true)['rules'] ?? [];
        foreach ($filterRules as $rule) {
            if ($rule['field'] === 'warna_status') {
                $value = $rule['data'];
                $rekapData = $rekapData->filter(fn($item) => $item['warna_status'] === $value)->values();
            }
        }
    }


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
    // Tampilkan hasil akhir
}

    public function exportRekapData(Request $request)
{
    $orders = Order::with([
        'tarif.customer:id,nama,top,marketing_id,cs_id',
        'tarif.customer.cs:id,name',
        'tarif.customer.marketing:id,name',
        'tarif.shipmentInfo:id,nama',
        'transaksi' => function ($query) {
            $query->whereNotNull('tanggal_kirim')
                ->select('id', 'job', 'total', 'pph', 'tanggal_kirim');
        },
        'jurnals' => function ($query) {
            $query->where('coa_id', 46)
                ->where(function ($q) {
                    $q->where('debit', '!=', 0)
                      ->orWhere('credit', '!=', 0);
                })
                ->select('id', 'invoice', 'order_id', 'debit', 'credit', 'coa_id', 'created_at');
        },
        'jadwal_kapal.kapal'
    ])
    ->whereNull('deleted_at') // ✅ tambahkan ini
    ->select('id', 'no_job', 'jadwal_kapal_id', 'invoice', 'container', 'invoice_date', 'job', 'tarif_id', 'created_at')
    ->orderBy('id', 'asc') // jangan pakai order.id, cukup id
    ->get();


    $grouped = $orders->groupBy('invoice');

    $finalRows = collect();
    foreach ($grouped as $invoice => $groupOrders) {
    $cs        = $groupOrders->pluck('tarif.customer.cs.name')->filter()->unique()->implode(', ');
    $marketing = $groupOrders->pluck('tarif.customer.marketing.name')->filter()->unique()->implode(', ');

    foreach ($groupOrders as $order) {
        $tarif   = (float) ($order->tarif->tarif ?? 0);
        $ppn     = $tarif * 0.11;
        $total   = $tarif + $ppn;
        $credit = $order->jurnals->pluck('credit')
              ->filter(fn($c) => $c != 0)
              ->implode("\n") ?: '-';

        $finalRows->push([
            'CS'        => $cs,
            'Marketing' => $marketing,
            'Invoice'   => $invoice,
            'No Job'    => ($order->job ?? '-') . '-' . sprintf('%02d', $order->no_job),
            'Customer'  => $order->tarif->customer->nama ?? '-',
            'Shipment'  => $order->tarif->shipmentInfo->nama ?? '-',
            'Kapal'     => $order->jadwal_kapal->kapal->nama ?? '-',
            'Voyage'    => $order->jadwal_kapal->voyage ?? '-',
            'Container' => $order->container ?? '-',
            'TD' => optional($order->jadwal_kapal)->td 
          ? date('d-m-Y', strtotime($order->jadwal_kapal->td)) 
          : '-',
            'Tarif'     => $tarif,
            'PPN'       => round($ppn),
            'Total'     => round($total),
            'Credit'    => $credit,
        ]);
    }
}

    return Excel::download(new RekapPiutangExport($finalRows), 'rekap_'.date('Ymd').'.xlsx');
}

 public function lapOutstandingBlumInv()
    {
        return view('admin.laporan.rekap-piutang-blum');
    }





public function data_rekap_piutang(Request $request)
    {
    $page = $request->input('page', 1);
    $rows = $request->input('rows', 20);
    $searchField = $request->input('searchField');
    $searchString = $request->input('searchString');
    $tglInvFilter = $request->input('tgl_inv');
    $customersFilter = $request->input('customers');
    $invFilter = $request->input('inv');
    $tfMasukVal = $request->input('tf_masuk');
    //  if ($tglInvFilter) {
    //     $tahun = (int) substr($tglInvFilter, 0, 4);
    //     if ($tahun < 2025) {
    //         return collect(); // atau response()->json([], 200);
    //     }
    // }
    // Ambil invoice dengan relasi yang diperlukan

   if ($customersFilter && !$tglInvFilter && !$invFilter) {
    // ❗ Hanya filter customer
    $orders = Order::with([
            'tarif.customer:id,nama,top',
            'transaksi' => function ($query) {
                $query->whereNotNull('tanggal_kirim')
                      ->select('id', 'job', 'total', 'pph', 'tanggal_kirim');
            },
            'jurnals' => function ($query) {
                $query->where('coa_id', 46)
                      ->where('debit', '!=', 0)
                      ->select('order_id', 'debit','coa_id');
            },
        ])
        ->whereHas('tarif.customer', function ($q) use ($customersFilter) {
            $q->where('nama', 'like', "%$customersFilter%");
        })
        ->select('id', 'invoice', 'invoice_date', 'job', 'tarif_id', 'created_at')
        ->orderByDesc('created_at')
        ->get()
        ->map(function ($order) {
        $order->tanggal_kirim = $order->transaksi->tanggal_kirim ?? null;
        return $order;
    });

} elseif (!$customersFilter && ($tglInvFilter || $invFilter)) {
    // ❗ Hanya filter tanggal dan/atau invoice
    $orders = Order::with([
            'tarif.customer:id,nama,top',
            'transaksi' => function ($query) {
                $query->whereNotNull('tanggal_kirim')
                      ->select('id', 'job', 'total', 'pph', 'tanggal_kirim');
            },
            'jurnals' => function ($query) {
                $query->where('coa_id', 46)
                      ->where('debit', '!=', 0)
                     ->select('order_id', 'debit','coa_id','created_at');
            },
        ])
        ->select('id', 'invoice', 'invoice_date', 'job', 'tarif_id', 'created_at')
        ->when($tglInvFilter, function ($q) use ($tglInvFilter) {
            $q->where('invoice_date', 'like', "%$tglInvFilter%");
        })
        ->when($invFilter, function ($q) use ($invFilter) {
            $q->where('invoice', 'like', "%$invFilter%");
        })
        ->orderByDesc('created_at')
        ->get()
        ->map(function ($order) {
        $order->tanggal_kirim = $order->transaksi->tanggal_kirim ?? null;
        return $order;
    });

} elseif ($customersFilter && ($tglInvFilter || $invFilter)) {
    // ❗ Jika customer dan tanggal/invoice digabung → hanya filter customer saja
    $orders = Order::with([
            'tarif.customer:id,nama,top',
            'transaksi' => function ($query) {
                $query->whereNotNull('tanggal_kirim')
                      ->select('id', 'job', 'total', 'pph', 'tanggal_kirim');
            },
            'jurnals' => function ($query) {
                $query->where('coa_id', 46)
                      ->where('debit', '!=', 0)
                      ->select('order_id', 'debit','coa_id');
            },
        ])
        ->whereHas('tarif.customer', function ($q) use ($customersFilter) {
            $q->where('nama', 'like', "%$customersFilter%");
        })
        ->select('id', 'invoice', 'invoice_date', 'job', 'tarif_id', 'created_at')
        ->orderByDesc('created_at')
        ->get()
        ->map(function ($order) {
        $order->tanggal_kirim = $order->transaksi->tanggal_kirim ?? null;
        return $order;
        });
} elseif (request('full')) {

    $orders = Order::with([
            'tarif.customer:id,nama,top',
            'transaksi' => function ($query) {
                $query->whereNotNull('tanggal_kirim')
                      ->select('id', 'job', 'total', 'pph', 'tanggal_kirim');
            },
            'jurnals' => function ($query) {
                $query->where('coa_id', 46)
                      ->where('debit', '!=', 0)
                      ->select('order_id', 'debit','coa_id');
            },
        ])
        ->select('id', 'invoice', 'invoice_date', 'job', 'tarif_id', 'created_at')
        ->orderByDesc('created_at')
        ->get()
        ->map(function ($order) {
        $order->tanggal_kirim = $order->transaksi->tanggal_kirim ?? null;
        return $order;
    });;
    // ❗ Semua filter kosong → kosongkan hasil
} 

elseif ($tfMasukVal) {

    $orders = Order::with([
            'tarif.customer:id,nama,top',
            'transaksi' => function ($query) {
                $query->whereNotNull('tanggal_kirim')
                      ->select('id', 'job', 'total', 'pph', 'tanggal_kirim');
            },
            'jurnals' => function ($query) {
                $query->where('coa_id', 46)
                      ->where('debit', '!=', 0)
                      ->select('order_id', 'debit','coa_id');
            },
        ])
        ->select('id', 'invoice', 'invoice_date', 'job', 'tarif_id', 'created_at')
        ->orderByDesc('created_at')
        ->get()
        ->map(function ($order) {
        $order->tanggal_kirim = $order->transaksi->tanggal_kirim ?? null;
        return $order;
    });;
    // ❗ Semua filter kosong → kosongkan hasil
} 
else {
    $orders = collect();
}

    $jurnalNilaiInv = Jurnal::withTrashed()
    ->select('invoice', \DB::raw('SUM(debit) as total_debit'))
    ->where('coa_id', 46)
    ->whereNull('deleted_at')
    ->where('debit', '!=', 0)
    ->whereNotNull('invoice')
    ->groupBy('invoice')
    ->get()
    ->keyBy('invoice');
    // Index untuk mapping
    $ordersByInvoice = $orders->groupBy('invoice');
    $jurnalNilai = $orders->pluck('jurnals', 'invoice');
    $transaksis = $orders->pluck('transaksi', 'invoice');
    $customers = $orders->pluck('tarif.customer', 'invoice');
    // Ambil jurnal dan group by invoice
    $jurnals = Jurnal::withTrashed()
        ->where('coa_id', 46)
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
    $rekapData = $ordersByInvoice->map(function ($group, $invoice) use ($transaksis, $customers, $jurnals,$jurnalNilai,$jurnalNilaiInv) {
        $trans = $transaksis[$invoice] ?? null;
        $cust = $customers[$invoice] ?? null;
        $jurnalN = $jurnalNilaiInv[$invoice]->total_debit ?? 0;
        $subtotal = $jurnalN;

                // $subtotal = $trans->total ?? 0;
                $pph = $trans->pph ?? 0;
                $jumlah_harga = round($subtotal);
                $top = (int)($cust->top ?? 0);
                $invoiceDate = $group->first()->tanggal_kirim;
                $tempo1 = Carbon::parse($invoiceDate)->addDays($top);
                $tempo = $tanggalTempo = Carbon::parse($invoiceDate)->addDays($top)->format('Y-m-d');;
                if ($jumlah_harga == 0) {
                return null;
                }

                $jurnal = $jurnals[$invoice] ?? null;
                $dibayar_tgl = $jurnal->daftar_tanggal ?? null;
                $sebesar = $jurnal->total_credit ?? 0;
                $kurang_bayar = $jumlah_harga - $sebesar;
                $today = Carbon::now();
                $daysDiff = $tempo1->diffInDays($today, false); // FALSE agar hasil bisa negatif
                $warna_status = '';

                // Jika lunas
                if ($kurang_bayar == 0) {
                    $warna_status = 'hijau';
                }

                elseif ($kurang_bayar < 0) {
                    $warna_status = 'biru';
                }
                // Jika PPh sama dengan kurang bayar
                elseif (round($pph)== $kurang_bayar) {
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
                elseif ($daysDiff > 0) {
                    $warna_status = 'merah';
                }

                $tfMasuk = $kurang_bayar - round($pph);



                return [
                    'tanggal' => now()->toDateString(),
                    'invoice' => $invoice,
                    'customer' => $cust->nama ?? '-',
                    'jumlah_harga' => $jumlah_harga,
                    'pph' => round($pph),
                    'top' => $top,
                    'ditagih_tgl' => $invoiceDate,
                    'tempo' => $tempo,
                    'hitung_tempo' => Carbon::parse($invoiceDate)->addDays($top + 1),
                    'dibayar_tgl' => $dibayar_tgl,
                    'sebesar' => $sebesar,
                    'kurang_bayar' => $kurang_bayar,
                    'tf_masuk' => (int)$tfMasuk,
                    'warna_status' => $warna_status, // <== TAMBAH DI SINI
                ];
            })->filter()->sortByDesc('invoice')->values();
if ($tfMasukVal !== null && $tfMasukVal !== '') {
    $inputVal = preg_replace('/[^\d]/', '', $tfMasukVal); // tetap string angka saja

    $rekapData = $rekapData->filter(function ($row) use ($inputVal) {
        $tfMasukRow = (string) $row['tf_masuk'];
        return strpos($tfMasukRow, $inputVal) !== false;
    })->values();
}

            
            if (request('full')) {
    $rekapData = $rekapData->sortBy([
        ['customer', 'asc'],
        ['ditagih_tgl', 'asc'],
    ])->values();

    $filters = request()->input('filters');
    if ($filters) {
        $filterRules = json_decode($filters, true)['rules'] ?? [];
        foreach ($filterRules as $rule) {
            if ($rule['field'] === 'warna_status') {
                $value = $rule['data'];
                $rekapData = $rekapData->filter(fn($item) => $item['warna_status'] === $value)->values();
            }
        }
    } else {
        // Jika tidak ada filter, default filter ke warna_status merah
        $rekapData = $rekapData->filter(fn($item) => $item['warna_status'] === 'merah')->values();
    }
} else {
    $rekapData = $rekapData->sortByDesc('invoice')->values();
}


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

// ⬇⬇ Tambahkan di sini, sebelum filter warna_status



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
        $jurnal61 = Jurnal::whereIn('order_id',$ids)->where('coa_id',93)->sum('debit');
         $jurnalDebit = Jurnal::whereIn('order_id', $ids)
            ->where('coa_id', 93)
            ->sum('debit');

        $jurnalKredit = Jurnal::whereIn('order_id', $ids)
            ->where('coa_id', 93)
            ->sum('credit');

        $jurnal61 = $jurnalDebit - $jurnalKredit;

        // Ambil data jurnal
        $jurnalList61 = Jurnal::whereIn('order_id', $ids)
            ->where('coa_id', 93)
            ->get();

        // Kelompokkan berdasarkan bulan dari created_at
        $jurnalPerBulan = $jurnalList61->groupBy(function ($jurnal) {
            return Carbon::parse($jurnal->created_at)->format('Y-m'); // contoh: "2025-07"
        });

        // Rekap data per bulan dengan pengurangan debit - kredit
        $rekapPerBulan = $jurnalPerBulan->map(function ($items, $bulan) {
            return [
                'periode' => $bulan,
                'total_debit' => $items->sum('debit'),
                'total_kredit' => $items->sum('credit'),
                'net_total' => $items->sum('debit') - $items->sum('credit'), // net = debit - kredit
            ];
        })->values();
        $is_pra = false;
        return view('admin.laporan.omset', compact('rekapPerBulan','jurnal61','is_pra','data','year','months','month','tipe','ids','coa'));
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
        $jurnalDebit = Jurnal::whereIn('order_id', $ids)
            ->where('coa_id', 31)
            ->sum('debit');

        $jurnalKredit = Jurnal::whereIn('order_id', $ids)
            ->where('coa_id', 31)
            ->sum('credit');

        $jurnal161 = $jurnalDebit - $jurnalKredit;

        // Ambil data jurnal
        $jurnalList161 = Jurnal::whereIn('order_id', $ids)
            ->where('coa_id', 31)
            ->get();

        // Kelompokkan berdasarkan bulan dari created_at
        $jurnalPerBulan = $jurnalList161->groupBy(function ($jurnal) {
            return Carbon::parse($jurnal->created_at)->format('Y-m'); // contoh: "2025-07"
        });

        // Rekap data per bulan dengan pengurangan debit - kredit
                $rekapPerBulan = $jurnalPerBulan->map(function ($items, $bulan) {
            return [
                'periode' => $bulan,
                'total_debit' => $items->sum('debit'),
                'total_kredit' => $items->sum('credit'),
                'net_total' => $items->sum('debit') - $items->sum('credit'),
            ];
        })->sortBy('periode')->values();


        // Tampilkan hasil
        $coa = COA::where('is_active',1)->get();
        $is_pra = true;
        return view('admin.laporan.pra_omset', compact('rekapPerBulan','is_pra','jurnal161','data','year','months','month','tipe','ids','coa'));
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
            $jurnal_id = Jurnal::whereIn('order_trucking_id',$get_id)->whereIn('coa_id',[61,81])->pluck('order_trucking_id')->toArray();
            $data = OrderTrucking::whereIn('id',$get_id)->get()->groupBy('seal');
            $jurnalMiss = Jurnal::whereIn('order_trucking_id', $get_id)
                        ->whereNotIn('coa_id', [61, 81])
                        ->where(function ($query) {
                            $query->where('nama', 'like', '%sangu kuli%')
                                ->orWhere('nama', 'like', '%sangu sopir%');
                        })
                        ->get();

            $jurnalPerBulan = $jurnalMiss->groupBy(function ($jurnal) {
                    return Carbon::parse($jurnal->created_at)->format('Y-m'); 
            });

            $rekapPerBulan = $jurnalPerBulan
                            ->map(function ($items, $periode) {
                                return [
                                    'periode' => $periode,
                                    'total_debit' => $items->sum('debit'),
                                    'list_jurnal_d' => $items->where('debit', '>', 0)
                                        ->groupBy('nomor')
                                        ->map(function ($group, $nomor) {
                                            $ids = $group->pluck('id')->unique()->values()->implode(',');
                                            return '<a href="' . url('admin/jurnal-edit-coa?jurnal=' . $nomor) . '" target="_blank">'
                                                . $nomor . ' (' . $ids . ')'
                                                . '</a>';
                                        })
                                        ->values()
                                ];
                            })
                            ->sortBy('periode')
                            ->values();
            $rekapPerBulan1 = null;
            $rekapPerBulan2 = null;
        }else{
           $order_id = Jurnal::whereNotNull('order_trucking_id')
    ->whereMonth('created_at', $month)
    ->whereYear('created_at', $year)
    ->whereIn('coa_id', [87])
    ->whereNull('jurnal_balik')
    ->pluck('order_trucking_id')
    ->toArray();


            $jurnal_id = Jurnal::whereIn('order_trucking_id', $order_id)
    ->pluck('order_trucking_id')
    ->toArray();
            $data = OrderTrucking::whereIn('id',$order_id)->get()->groupBy('seal');

             $jurnalList521 = Jurnal::whereIn('order_trucking_id',$jurnal_id)->where('coa_id', 98)->get();

             $jurnalNull = Jurnal::where(function ($q) {
        // Kondisi kalau order_trucking_id null
        $q->whereNull('order_trucking_id')
          // Kondisi kalau order_trucking_id ada
          ->orWhere(function ($q2) {
              $q2->whereNotNull('order_trucking_id')
                 ->whereNull('invoice_vendor')
                 ->whereNull('invoice_trucking')
                 // Filter orderTrucking yang invoice tidak ada LT
                 ->whereHas('order_trucking', function ($sub) {
                     $sub->where(function ($w) {
                         $w->whereNull('invoice')
                           ->orWhere('invoice', 'NOT LIKE', '%LT%');
                     });
                 });
          });
    })
    ->where('tipe', 'BKK')
    ->whereMonth('created_at', $month)
    ->whereYear('created_at', $year)
    ->whereIn('coa_id', [98])
    ->get();

  $jurnalHrs621 = Jurnal::where('coa_id', '!=', 98)
    ->whereNotNull('order_trucking_id')
    ->whereNull('order_id')
     ->whereMonth('created_at', $month)
     ->where('debit', '>', 0)
    ->whereYear('created_at', $year)
    ->where(function ($q) {
        $q->whereRaw("LOWER(nama) LIKE 'sangu sopir%'")
          ->orWhereRaw("LOWER(nama) LIKE 'sangu kuli%'");
    })
    ->whereHas('order_trucking', function ($sub) {
                     $sub->where(function ($w) {
                         $w->whereNull('order_id');
                     });
                 })
    ->get();
    $jurnalPerBulan1 = $jurnalNull->groupBy(function ($jurnal) {
        return Carbon::parse($jurnal->created_at)->format('Y-m'); // contoh: "2025-07"
    });

    $jurnalPerBulan2 = $jurnalHrs621->groupBy(function ($jurnal) {
        return Carbon::parse($jurnal->created_at)->format('Y-m'); // contoh: "2025-07"
     });

    // Kelompokkan berdasarkan bulan dari created_at
    $jurnalPerBulan = $jurnalList521->groupBy(function ($jurnal) {
        return Carbon::parse($jurnal->created_at)->format('Y-m'); // contoh: "2025-07"
    });

  $currentPeriod = \Carbon\Carbon::createFromDate($year, $month, 1)->format('Y-m');

$rekapPerBulan = $jurnalPerBulan
    ->map(function ($items, $periode) use ($currentPeriod) {
        return [
            'periode' => $periode,
            'total_debit' => $items->sum('debit'),
            'list_jurnal_d' => $periode === $currentPeriod
                ? collect([]) // array kosong kalau periode sama
                : $items->where('debit', '>', 0)
                    ->groupBy('nomor')
                    ->map(function ($group, $nomor) {
                        $ids = $group->pluck('id')->unique()->values()->implode(',');
                        return '<a href="' . url('admin/jurnal-edit-coa?jurnal=' . $nomor) . '" target="_blank">'
                               . $nomor . ' (' . $ids . ')'
                               . '</a>';
                    })
                    ->values()
        ];
    })
    ->sortBy('periode')
    ->values();



  $rekapPerBulan2 = $jurnalPerBulan2->map(function ($items, $month) {
    return [
        'periode' => $month,
        'total_debit' => $items->sum('debit'),
        'list_jurnal_d' => $items->where('debit', '>', 0)
            ->groupBy('nomor')
            ->map(function ($group, $nomor) {
                $ids = $group->pluck('id')->unique()->values()->implode(',');
                return '<a href="' . url('admin/jurnal-edit-coa?jurnal=' . $nomor) . '" target="_blank">' 
                       . $nomor . ' (' . $ids . ')' 
                       . '</a>';
            })
            ->values()
    ];
})->sortBy('periode')->values();

                       

$rekapPerBulan1 = $jurnalPerBulan1->map(function ($items, $month) {
    return [
        'periode' => $month,
        'total_debit' => $items->sum('debit'),
        'list_jurnal_d' => $items->where('debit', '>', 0)
            ->groupBy('nomor')
            ->map(function ($group, $nomor) {
                $ids = $group->pluck('id')->unique()->values()->implode(',');
                return '<a href="' . url('admin/jurnal-edit-coa?jurnal=' . $nomor) . '" target="_blank">' 
                       . $nomor . ' (' . $ids . ')' 
                       . '</a>';
            })
            ->values()
    ];
})->sortBy('periode')->values();

        }
        return view('admin.laporan.omset_trucking', compact('data','year','months','month','tipe','jurnal_id','rekapPerBulan','rekapPerBulan1','rekapPerBulan2'));
    }
}
