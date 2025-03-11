<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderBiayaResource;
use App\Models\Order;
use App\Models\OrderBiaya;
use Illuminate\Http\Request;

class OrderBiayaController extends Controller
{
    public function index()
    {
        $orderIds = Order::whereHas('tarif', function ($q) {
                $q->whereHas('tujuan_lokasi', function ($a) {
                    $a->where('nama', 'like', '%banjarmasin%');
                });
            })
            ->pluck('id');
    
        foreach ($orderIds as $id) {
            $biayaExists = OrderBiaya::where('order_id', $id)
                ->whereHas('orderInfo', function ($o) {
                    $o->whereHas('tarif', function ($t) { // Tambahkan whereHas tarif setelah orderInfo
                        $t->whereHas('tujuan_lokasi', function ($a) {
                            $a->where('nama', 'like', '%banjarmasin%');
                        });
                    });
                })
                ->exists();
    
            if (!$biayaExists) {
                OrderBiaya::create(['order_id' => $id]);
            }
        }
    
        return view('admin.keuangan.biaya_order');
    }
    

    public function jayapura()
{
    // Ambil ID order yang memiliki tujuan Jayapura
    $orders = Order::whereHas('tarif', function ($q) {
            $q->whereHas('tujuan_lokasi', function ($a) {
                $a->where('nama', 'like', '%jayapura%');
            });
        })
        ->pluck('id');

    // Cek dan buat OrderBiaya jika belum ada
    foreach ($orders as $id) {
        $biayaExists = OrderBiaya::where('order_id', $id)
            ->whereHas('orderInfo', function ($o) {
                $o->whereHas('tarif', function ($t) {
                    $t->whereHas('tujuan_lokasi', function ($a) {
                        $a->where('nama', 'like', '%jayapura%');
                    });
                });
            })
            ->exists();

        if (!$biayaExists) {
            OrderBiaya::create([
                'order_id' => $id
            ]);
        }
    }

    // Ambil data OrderBiaya yang hanya terkait dengan Jayapura
    $biayaJayapura = OrderBiaya::whereHas('orderInfo', function ($o) {
            $o->whereHas('tarif', function ($t) {
                $t->whereHas('tujuan_lokasi', function ($a) {
                    $a->where('nama', 'like', '%jayapura%');
                });
            });
        })
        ->get();

    return view('admin.keuangan.biaya_order-jayapura', compact('biayaJayapura'));
}

    public function edit(OrderBiaya $order)
    {
        return view('admin.biaya_order.edit',compact('order'));
    }

    public function update(Request $reqest, OrderBiaya $order)
    {
        $order->update($reqest->all());
        return back()->with('success','Data berhasil tersimpan!');
    }

    public function jqgrid()
    {
        $page = request('page'); // get the requested page
        $limit = request('rows'); // get how many rows we want to have into the grid
        $sidx = request('sidx'); // get index row - i.e. user click to sort
        $sord = request('sord'); // get the direction
        $search = request('_search'); // get the search
        $is_search = false;
        if($search=='true'){
            $is_search = true;
        }
        $query = OrderBiaya::query();
        $query->whereHas('order');
        $query->join('order','order.id','=','order_biaya.order_id')
        ->join('tarif','tarif.id','=','tarif_id')
        ->join('customers','customers.id','=','tarif.customer_id');


        $start = $limit * $page - $limit;
        if ($start < 0){
            $start = 0;
        }

        if(request('job')){
            $query->whereHas('orderInfo', function($q){
                $q->where('order.job','LIKE','%'.request('job').'%');
            });
        }


        // if(request('no_job')){
        //     $me = explode('-',request('no_job'));
        //     $query->whereHas('orderInfo', function($q) use($me){
        //         $q->where('job','LIKE','%'.$me[0].'%');
        //     });
        //     if(!empty($me[1])){
        //         $query->whereHas('orderInfo', function($q) use($me){
        //             $q->where('no_job',(int)$me[1]);
        //         });
        //     }
        // }
        if(request('invoice')){
            $query->whereHas('orderInfo', function($q){
                $q->where('invoice','LIKE','%'.request('invoice').'%');
            });
        }
        if(request('asuransi')){
            $query->whereHas('orderInfo', function($q){
                $q->where('asuransi','LIKE','%'.request('asuransi').'%');
            });
        }
        if(request('nopol')){
            $query->whereHas('orderInfo', function($q){
                $q->where('nopol','LIKE','%'.request('nopol').'%');
            });
        }
        if(request('trucking')){
            $query->whereHas('orderInfo', function($q){
                $q->where('trucking','LIKE','%'.request('trucking').'%');
            });
        }
        if(request('container')){
            $query->whereHas('orderInfo', function($q){
                $q->where('container','LIKE','%'.request('container').'%');
            });
        }
        if(request('seal')){
            $query->whereHas('orderInfo', function($q){
                $q->where('seal','LIKE','%'.request('seal').'%');
            });
        }
        if(request('agen')){
            $query->whereHas('orderInfo', function($q){
                $q->where('agen','LIKE','%'.request('agen').'%');
            });
        }
        if(request('keterangan')){
            $query->whereHas('orderInfo', function($q){
                $q->where('keterangan','LIKE','%'.request('keterangan').'%');
            });
        }
        if(request('pembayar')){
            $query->whereHas('orderInfo', function($q){
                $q->whereHas('tarif', function($a){
                    $a->whereHas('customer', function($b){
                        $b->where('nama','LIKE','%'.request('pembayar').'%');
                    });
                });
            });
        }
        if(request('penerima_bl')){
            $query->whereHas('orderInfo', function($q){
                $q->whereHas('agent',function($a){
                    $a->where('nama','LIKE','%'.request('penerima_bl').'%');
                })->orWhereHas('penerima_bl', function($b){
                    $b->where('nama','LIKE','%'.request('penerima_bl').'%');
                });
            });
        }
        if(request('barang')){
            $query->whereHas('orderInfo', function($q){
                $q->whereHas('barang',function($a){
                    $a->where('nama','LIKE','%'.request('barang').'%');
                });
            });
        }
        if(request('barang_detail')){
            $query->whereHas('orderInfo', function($q){
                $q->whereHas('bttb',function($a){
                    $a->whereHas('barang', function($b){
                        $b->where('nama','LIKE','%'.request('barang_detail').'%');
                    });
                });
            });
        }

        if(request('pelayaran')){
            $query->whereHas('orderInfo', function($q){
                $q->whereHas('jadwal_kapal',function($a){
                    $a->whereHas('pelayaran', function($b){
                        $b->where('nama','LIKE','%'.request('pelayaran').'%');
                    });
                });
            });
        }
        if(request('kapal')){
            $query->whereHas('orderInfo', function($q){
                $q->whereHas('jadwal_kapal',function($a){
                    $a->whereHas('kapal', function($b){
                        $b->where('nama','LIKE','%'.request('kapal').'%');
                    });
                });
            });
        }
        if(request('voyage')){
            $query->whereHas('orderInfo', function($q){
                $q->whereHas('jadwal_kapal',function($s){
                    $s->where('voyage','LIKE','%'.request('voyage').'%');
                });
            });
        }
        if(request('dari')){
            $query->whereHas('orderInfo', function($q){
                $q->whereHas('tarif',function($a){
                    $a->whereHas('dari_lokasi', function($b){
                        $b->where('nama','LIKE','%'.request('dari').'%');
                    });
                });
            });
        }
        if(request('tujuan')){
            $query->whereHas('orderInfo', function($q){
                $q->whereHas('tarif',function($a){
                    $a->whereHas('tujuan_lokasi', function($b){
                        $b->where('nama','LIKE','%'.request('tujuan').'%');
                    });
                });
            });
        }
        if (request('kota')) {
            $query->whereHas('orderInfo', function ($q) {
                $q->whereHas('tarif', function ($a) {
                    $a->whereHas('tujuan_lokasi', function ($b) {
                        $b->where('nama', 'LIKE', '%' . request('kota') . '%')
                          ->whereNull('deleted_at');
                    })->whereHas('kondisiInfo', function ($c) {
                        $c->whereNotIn('id', [1, 6])
                          ->whereNull('deleted_at');
                    })->whereNull('deleted_at');
                })->whereNull('deleted_at');
            });
        }
        
        if(request('kota1')){
            $query->whereHas('orderInfo', function($q){
                $q->whereHas('tarif',function($a){
                    $a->whereHas('tujuan_lokasi', function($b){
                        $b->where('nama','LIKE','%'.request('kota1').'%');
                    });
                });
            });
        }
        if(request('shipment')){
            $query->whereHas('orderInfo', function($q){
                $q->whereHas('tarif',function($a){
                    $a->whereHas('shipmentInfo', function($b){
                        $b->where('nama','LIKE','%'.request('shipment').'%');
                    });
                });
            });
        }
        if(request('kondisi')){
            $query->whereHas('orderInfo', function($q){
                $q->whereHas('tarif',function($a){
                    $a->whereHas('kondisiInfo', function($b){
                        $b->where('nama','LIKE','%'.request('kondisi').'%');
                    });
                });
            });
        }
        if(request('pengirim')){
            $query->whereHas('orderInfo', function($q){
                $q->whereHas('pengirim',function($a){
                    $a->where('nama','LIKE','%'.request('pengirim').'%');
                });
            });
        }
        if(request('penerima')){
            $query->whereHas('orderInfo', function($q){
                $q->whereHas('penerima',function($a){
                    $a->where('nama','LIKE','%'.request('penerima').'%');
                });
            });
        }
        if(request('marketing')){
            $query->whereHas('orderInfo', function($q){
                $q->whereHas('tarif',function($a){
                    $a->whereHas('customer', function($b){
                        $b->whereHas('marketing', function($c){
                            $c->where('name','LIKE','%'.request('marketing').'%');
                        });
                    });
                });
            });
        }
        if(request('marketing_id')){
            $query->whereHas('orderInfo', function($q){
                $q->whereHas('tarif',function($a){
                    $a->whereHas('customer', function($b){
                        $b->where('marketing_id',request('marketing_id'));
                    });
                });
            });
        }
        if(request('cs')){
            $query->whereHas('orderInfo', function($q){
                $q->whereHas('tarif',function($a){
                    $a->whereHas('customer', function($b){
                        $b->whereHas('cs', function($c){
                            $c->where('name','LIKE','%'.request('cs').'%');
                        });
                    });
                });
            });
        }

        if($sidx){
            $query->select('order_biaya.*','order.job','order.no_job');
            $data = $query->orderBy('order.job')->orderBy('order.no_job')->skip($start)->take($limit)->get();
        }else{
            $query->select('order_biaya.*');
            $data = $query->orderBy('order.job')->orderBy('order.no_job')->skip($start)->take($limit)->get();
        }

        // if($is_search){
        //     $count = $query->count();
        // }else{
        // }
        $count = OrderBiaya::get('id')->count();

        if ($count > 0 && $limit > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }

        if ($page > $total_pages){
            $page = $total_pages;
        }

        $response = OrderBiayaResource::collection($data);
        return response([
            'page' => $page,
            'total' => $total_pages,
            'records' => $count,
            'rows' => $response
        ]);
    }
}
