<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Barang;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Satuan;
use App\Models\Tarif;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function getOrderWithNopol($nopol)
    {
        $orders = Order::where('nopol', 'LIKE', '%' . $nopol . '%')->get(['id', 'job', 'no_job', 'container']);
        return response($orders);
    }

    public function index()
    {
        $start = request('start');
        $limit = request('limit');
        $data = Order::all()->sortBy('job')->sortBy('no')->skip($start)->take($limit);
        $count = Order::select('id')->count();
        $data = OrderResource::collection($data);
        return response([
            'start' => $start + $limit,
            'count' => $count,
            'data' => $data
        ]);
    }

    public function show(Order $order)
    {
        $data = new OrderResource($order);
        return response($data);
    }

    public function update(Request $request)
    {
        $data = $request->all();
        $order = Order::find($request->order_id);
        if ($request->ba) {
        } elseif ($request->asuransi_update) {
            $data['pertanggungan'] = str_replace(['.', ','], '', $request->pertanggungan);
            $data['asuransi_date'] = date('Y-m-d H:i:s');
            if ($request->tipe_asuransi == 'job') {
                Order::where('job', $order->job)->update([
                    'pertanggungan' => $data['pertanggungan'],
                    'tipe_asuransi' => 'job',
                    'asuransi_id' => $request->asuransi_id,
                    'asuransi_date' => date('Y-m-d H:i:s')
                ]);
            }
        } else {
            $barang = Barang::find($request->barang_id);
            if (!$barang) {
                $barang = Barang::create(['nama' => $request->barang_id]);
            }

            $data['pengirim_id'] = Customer::where('nama', $request->pengirim_id)->first()->id;
            $data['penerima_id'] = Customer::where('nama', $request->penerima_id)->first()->id;
            if ($request->satuan) {
                $satuan = Satuan::find($request->satuan);
                if (!$satuan) {
                    $satuan = Satuan::create(['nama' => $request->satuan]);
                }
                $data['satuan'] = $satuan->id;
            }
            $data['barang_id'] = $barang->id;
        }
        $order->update($data);
        $order = Order::find($request->order_id);
        return response($order);
    }

    public function ba_kembali()
    {
        $start = request('start');
        $limit = request('limit');
        $data = Order::whereHas('tarif', function ($q) {
            $q->whereIn('kondisi', [5, 6,10]);
            $q->whereHas('customer', function ($qu) {
                $qu->where('ba_kembali', 1);
            });
        })->whereHas('jadwal_kapal', function ($q) {
            $q->whereNotNull('td');
        })->whereNull('invoice')
            ->whereNull('ba_kembali')->orderBy('job', 'asc')->orderBy('no', 'asc')->skip($start)->take($limit)->get();
        $count = Order::select('id')->count();
        $data = OrderResource::collection($data);
        return response([
            'start' => $start + $limit,
            'count' => $count,
            'data' => $data
        ]);
    }

    public function update_request()
    {
        $order = Order::find(request('id'));
        if ($order) {
            $order->update(request()->all());
        }

        return response('success');
    }

    public function pre_invoice()
    {
        $ids = array();
        $data1 = Order::whereHas('tarif', function ($q) {
            $q->whereIn('kondisi', [1, 6]);
        })->whereHas('jadwal_kapal', function ($q) {
            $q->whereNotNull('td');
        })->whereNull('invoice')->pluck('id');
        foreach ($data1 as $item) {
            array_push($ids, $item);
        }

        $data2 = Order::whereHas('tarif', function ($q) {
            $q->whereIn('kondisi', [5, 7]);
            $q->whereHas('customer', function ($qu) {
                $qu->where('ba_kembali', 1);
            });
        })->whereHas('jadwal_kapal', function ($q) {
            $q->whereNotNull('td');
        })->whereNull('invoice')->whereNotNull('ba_kembali')->pluck('id');
        foreach ($data2 as $item) {
            array_push($ids, $item);
        }

        $data3 = Order::whereHas('tarif', function ($q) {
            $q->whereIn('kondisi', [5, 7]);
            $q->whereHas('customer', function ($qu) {
                $qu->where('ba_kembali', 0);
            });
        })->whereHas('jadwal_kapal', function ($q) {
            $q->whereNotNull('td');
        })->whereNull('invoice')->pluck('id');
        foreach ($data3 as $item) {
            array_push($ids, $item);
        }

        $count = count($ids);
        $data = Order::whereIn('id', $ids)->orderBy('job', 'asc')->orderBy('no', 'asc')->get();
        $data = OrderResource::collection($data);
        return response([
            'start' => 0,
            'count' => $count,
            'data' => $data
        ]);
    }

    public function jqgrid()
    {
        $page = request('page'); // get the requested page
        $limit = request('rows'); // get how many rows we want to have into the grid
        $sidx = request('sidx'); // get index row - i.e. user click to sort
        $sord = request('sord'); // get the direction
        $search = request('_search'); // get the search
        $is_search = false;
        if ($search == 'true') {
            $is_search = true;
        }
        $now = Carbon::now()->addMonths(1)->format('Y-m-d');
        $last = Carbon::now()->subMonths(6)->format('Y-m-d');
        $query = Order::query();
        $query->join('tarif', 'tarif.id', '=', 'order.tarif_id');
        if (!$is_search) {
            $query->whereBetween('order.created_at', [$last, $now]);
        }


        $start = $limit * $page - $limit;
        if ($start < 0) {
            $start = 0;
        }

      if (request('ba_kembali_null')) {
    $query->whereNull('order.ba_kembali');
     $query->whereHas('jadwal_kapal',function ($j){
                 $j->whereNotNull('eta');
            });
    $query->whereHas('tarif', function ($a) {
        $a->whereIn('kondisi', [5, 7, 10]);
        $a->whereHas('customer', function ($qu) {
                $qu->where('ba_kembali', 1);
            });
    });

    // Tambahan kondisi: jika tujuan = 97, maka ba_diantar_sby harus tidak null
    $query->where(function ($q) {
        $q->whereHas('tarif', function ($t) {
            $t->where('tujuan', '!=', 97);
        })->orWhere(function ($q2) {
            $q2->whereHas('tarif', function ($t2) {
                $t2->where('tujuan', 97);
            })->whereNotNull('order.ba_diantar_sby')->whereNotNull('order.barang_diantar');
        });
    });
}

          if (request('barang_diantar_null') ) {
            $query->whereNull('order.invoice')->whereNull('order.barang_diantar');
            $query->whereHas('jadwal_kapal',function ($j){
                 $j->whereNotNull('eta');
            });
          $query->whereHas('tarif', function ($a) {
                $a->whereIn('kondisi', [5, 7, 10]);
                $a->where('tujuan',97);
            });
        }

        if (request('ba_diantar_sby_null') ) {
            $query->whereNull('order.invoice')->whereNull('order.ba_diantar_sby');
             $query->whereHas('jadwal_kapal',function ($j){
                 $j->whereNotNull('eta');
            });
            $query->whereHas('tarif', function ($a) {
                $a->whereIn('kondisi', [5, 7, 10]);
                $a->where('tujuan',97);
            });
        }

        if (request('input_invoice_bayar')) {
            $query->where('order.komisi', '>', 0)->whereNull('order.tgl_komisi')->whereNull('order.invoice_bayar')->whereNull('order.komisi_print');
        }
        if (request('input_komisi')) {
            $query->where('order.komisi', '>', 0)->whereNull('order.tgl_komisi')->whereNotNull('order.invoice_bayar')->whereNull('order.komisi_print');
        }
        if (request('komisi_print')) {
            $query->where('order.komisi', '>', 0)->whereNotNull('order.tgl_komisi')->whereNotNull('order.invoice_bayar')->whereNull('order.komisi_print');
        }
        if (request('komisi_print_done')) {
            $query->where('order.komisi', '>', 0)->whereNotNull('order.tgl_komisi')->whereNotNull('order.invoice_bayar')->whereNotNull('order.komisi_print');
        }
        $query->join('customers', 'customers.id', '=', 'tarif.customer_id');

        if (request('job')) {
            $query->where('order.job', 'LIKE', '%' . request('job') . '%');
        }

        if (request('customer_id')) {
            $query->whereHas('tarif', function ($q) {
                $q->where('customer_id', request('customer_id'));
            });
        }

        if (request('type') == 'monitoring_pembayar') {
            $query->whereDate('order.created_at', '>=', '2024-09-01');
        }

        if (request('no')) {
            $me = explode('-', request('no'));
            $query->where('order.job', 'LIKE', '%' . $me[0] . '%');
            if (!empty($me[1])) {
                $query->where('order.no_job', (int)$me[1]);
            }
        }
        if (request('invoice')) {
            $query->where('order.invoice', 'LIKE', '%' . request('invoice') . '%');
        }
        if (request('asuransi')) {
            $query->where('order.asuransi', 'LIKE', '%' . request('asuransi') . '%');
        }
        if (request('nopol')) {
            $query->where('order.nopol', 'LIKE', '%' . request('nopol') . '%');
        }
        if (request('trucking')) {
            $query->where('order.trucking', 'LIKE', '%' . request('trucking') . '%');
        }
        if (request('container')) {
            $query->where('order.container', 'LIKE', '%' . request('container') . '%');
        }
        if (request('seal')) {
            $query->where('order.seal', 'LIKE', '%' . request('seal') . '%');
        }
        if (request('agen')) {
            $query->where('order.agen', 'LIKE', '%' . request('agen') . '%');
        }
        if (request('keterangan')) {
            $query->where('order.keterangan', 'LIKE', '%' . request('keterangan') . '%');
        }
        if (request('pembayar')) {
            $query->where('customers.nama', 'LIKE', '%' . request('pembayar') . '%');
        }
        if (request('penerima_bl')) {
            $query->whereHas('agent', function ($q) {
                $q->where('nama', 'LIKE', '%' . request('penerima_bl') . '%');
            })->orWhereHas('penerima_bl', function ($a) {
                $a->where('nama', 'LIKE', '%' . request('penerima_bl') . '%');
            });
        }
        if (request('barang')) {
            $query->whereHas('barang', function ($q) {
                $q->where('nama', 'LIKE', '%' . request('barang') . '%');
            });
        }
        if (request('barang_detail')) {
            $query->whereHas('bttb', function ($q) {
                $q->whereHas('barang', function ($a) {
                    $a->where('nama', 'LIKE', '%' . request('barang_detail') . '%');
                });
            });
        }

        if (request('pelayaran')) {
            $query->whereHas('jadwal_kapal', function ($q) {
                $q->whereHas('pelayaran', function ($a) {
                    $a->where('nama', 'LIKE', '%' . request('pelayaran') . '%');
                });
            });
        }
        if (request('kapal')) {
            $query->whereHas('jadwal_kapal', function ($q) {
                $q->whereHas('kapal', function ($a) {
                    $a->where('nama', 'LIKE', '%' . request('kapal') . '%');
                });
            });
        }
        if (request('voyage')) {
            $query->whereHas('jadwal_kapal', function ($q) {
                $q->where('voyage', 'LIKE', '%' . request('voyage') . '%');
            });
        }
        if (request('dari')) {
            $query->whereHas('tarif', function ($q) {
                $q->whereHas('dari_lokasi', function ($a) {
                    $a->where('nama', 'LIKE', '%' . request('dari') . '%');
                });
            });
        }
        if (request('tujuan')) {
            $query->whereHas('tarif', function ($q) {
                $q->whereHas('tujuan_lokasi', function ($a) {
                    $a->where('nama', 'LIKE', '%' . request('tujuan') . '%');
                });
            });
        }
        if (request('shipment')) {
            $query->whereHas('tarif', function ($q) {
                $q->whereHas('shipmentInfo', function ($a) {
                    $a->where('nama', 'LIKE', '%' . request('shipment') . '%');
                });
            });
        }
        if (request('kondisi')) {
            $query->whereHas('tarif', function ($q) {
                $q->whereHas('kondisiInfo', function ($a) {
                    $a->where('nama', 'LIKE', '%' . request('kondisi') . '%');
                });
            });
        }
        if (request('pengirim')) {
            $query->whereHas('pengirim', function ($q) {
                $q->where('nama', 'LIKE', '%' . request('pengirim') . '%');
            });
        }
        if (request('penerima')) {
            $query->whereHas('penerima', function ($q) {
                $q->where('nama', 'LIKE', '%' . request('penerima') . '%');
            });
        }
        if (request('marketing')) {
            $query->whereHas('tarif', function ($q) {
                $q->whereHas('customer', function ($a) {
                    $a->whereHas('marketing', function ($b) {
                        $b->where('name', 'LIKE', '%' . request('marketing') . '%');
                    });
                });
            });
        }
        if (request('marketing_id')) {
            $query->whereHas('tarif', function ($q) {
                $q->whereHas('customer', function ($a) {
                    $a->where('marketing_id', request('marketing_id'));
                });
            });
        }
        if (request('cs')) {
            $query->whereHas('tarif', function ($q) {
                $q->whereHas('customer', function ($a) {
                    $a->whereHas('cs', function ($b) {
                        $b->where('name', 'LIKE', '%' . request('cs') . '%');
                    });
                });
            });
        }

        if ($sidx) {
            if ($sidx == 'pembayar') {
                $query->select('order.*', 'customers.nama as pembayar');
                $data = $query->orderBy('pembayar', $sord)->orderBy('order.job')->orderBy('order.no_job')->skip($start)->take($limit)->get();
            } else {
                $query->select('order.*');
                $data = $query->orderBy('job')->orderBy('no_job')->skip($start)->take($limit)->get();
            }
        } else {
            $query->select('order.*');
            $data = $query->orderBy('job')->orderBy('no_job')->skip($start)->take($limit)->get();
        }

        // if($is_search){
        //     $count = $query->count();
        // }else{
        // }
        $count = Order::whereBetween('order.created_at', [$last, $now])->get('id')->count();
        if (request('marketing_id')) {
            $count = Order::whereBetween('order.created_at', [$last, $now])->whereHas('tarif', function ($q) {
                $q->whereHas('customer', function ($a) {
                    $a->where('marketing_id', request('marketing_id'));
                });
            })->count();
        }
       if (request('ba_kembali_null')) {
    $count = Order::whereNull('ba_kembali')
        ->whereHas('tarif', function ($a) {
            $a->whereIn('kondisi', [5, 7, 10])
              ->whereHas('customer', function ($qu) {
                  $qu->where('ba_kembali', 1);
              });
        })
        ->where(function ($q) {
            $q->whereHas('tarif', function ($t) {
                $t->where('tujuan', '!=', 97);
            })->orWhere(function ($q2) {
                $q2->whereHas('tarif', function ($t2) {
                    $t2->where('tujuan', 97);
                })
                ->whereNotNull('ba_diantar_sby')
                ->whereNotNull('barang_diantar');
            });
        })
        ->whereHas('jadwal_kapal', function ($j) {
            $j->whereNotNull('eta');
        })
        ->count();
}

        if (request('barang_diantar_null')) {
            $count = Order::whereNull('invoice')->whereNull('barang_diantar')->whereHas('tarif', function ($a) {
                $a->whereIn('kondisi', [5, 7]);
            })->whereHas('jadwal_kapal',function ($j){
                 $j->whereNotNull('eta');
            })->count();
        }
         if (request('ba_diantar_sby_null')) {
            $count = Order::whereNull('invoice')->whereNull('ba_diantar_sby')->whereHas('tarif', function ($a) {
                $a->whereIn('kondisi', [5, 7]);
                $a->where('tujuan',97);
            })->whereHas('jadwal_kapal',function ($j){
                 $j->whereNotNull('eta');
            })->count();
        }
        if (request('input_invoice_bayar')) {
            $count = Order::where('komisi', '>', 0)->whereNull('tgl_komisi')->whereNull('invoice_bayar')->count();
        }
        if (request('input_komisi')) {
            $count = Order::where('komisi', '>', 0)->whereNull('tgl_komisi')->whereNotNull('invoice_bayar')->count();
        }
        if (request('komisi_print')) {
            $count = Order::where('komisi', '>', 0)->whereNotNull('tgl_komisi')->whereNotNull('invoice_bayar')->count();
        }
        if ($count > 0 && $limit > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }

        if ($page > $total_pages) {
            $page = $total_pages;
        }

        $response = OrderResource::collection($data);
        return response([
            'page' => $page,
            'total' => $total_pages,
            'records' => $count,
            'rows' => $response
        ]);
    }

    public function getArrayId(Request $request)
    {
        if ($request->type && $request->type == 'job') {
            $id = $request->id;
            $id = array_values(array_filter($id));
            $ids_ordered = implode(',', $id);
            $orders = Order::whereIn('job', $id)->orderByRaw("FIELD(job,$ids_ordered)")->get();
            $data = OrderResource::collection($orders);
        } else {
            $id = $request->id;
            $id = array_values(array_filter($id));
            $ids_ordered = implode(',', $id);
            $orders = Order::whereIn('id', $id)->orderByRaw("FIELD(id,$ids_ordered)")->get();
            $data = OrderResource::collection($orders);
        }
        return response($data);
    }

    public function updateLockAll(Request $request)
    {
        $id = $request->id;
        Order::whereIn('id', $id)->update([
            'lock_omset' => 1
        ]);

        return response('seccess');
    }
    public function updateUnlockAll(Request $request)
    {
        $id = $request->id;
        Order::whereIn('id', $id)->update([
            'lock_omset' => 0
        ]);

        return response('seccess');
    }
}
