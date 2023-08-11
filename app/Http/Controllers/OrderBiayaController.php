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
        $order = Order::whereHas('tarif',function($q){
            $q->where('tujuan',97);
        })->pluck('id')->map(function ($id) {
            $biaya = OrderBiaya::where('order_id',$id)->first();
            if(!$biaya){
                OrderBiaya::create([
                    'order_id' => $id
                ]);
            }
        });
        return view('admin.keuangan.biaya_order');
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
        $query->join('order','order.id','=','order_biaya.order_id');
        $query->join('tarif','tarif.id','=','order.tarif_id');
        $query->join('customers','customers.id','=','tarif.customer_id');


        $start = $limit * $page - $limit;
        if ($start < 0){
            $start = 0;
        }

        if(request('job')){
            $query->where('order.job','LIKE','%'.request('job').'%');
        }

        if(request('no_job')){
            $me = explode('-',request('no_job'));
            $query->where('order.job','LIKE','%'.$me[0].'%');
            if(!empty($me[1])){
                $query->where('order.no_job',(int)$me[1]);
            }
        }
        if(request('invoice')){
            $query->where('order.invoice','LIKE','%'.request('invoice').'%');
        }
        if(request('asuransi')){
            $query->where('order.asuransi','LIKE','%'.request('asuransi').'%');
        }
        if(request('nopol')){
            $query->where('order.nopol','LIKE','%'.request('nopol').'%');
        }
        if(request('trucking')){
            $query->where('order.trucking','LIKE','%'.request('trucking').'%');
        }
        if(request('container')){
            $query->where('order.container','LIKE','%'.request('container').'%');
        }
        if(request('seal')){
            $query->where('order.seal','LIKE','%'.request('seal').'%');
        }
        if(request('agen')){
            $query->where('order.agen','LIKE','%'.request('agen').'%');
        }
        if(request('keterangan')){
            $query->where('order.keterangan','LIKE','%'.request('keterangan').'%');
        }
        if(request('pembayar')){
            $query->where('customers.nama','LIKE','%'.request('pembayar').'%');
        }
        if(request('penerima_bl')){
            $query->whereHas('agent',function($q){
                $q->where('nama','LIKE','%'.request('penerima_bl').'%');
            })->orWhereHas('penerima_bl', function($a){
                $a->where('nama','LIKE','%'.request('penerima_bl').'%');
            });
        }
        if(request('barang')){
            $query->whereHas('barang',function($q){
                $q->where('nama','LIKE','%'.request('barang').'%');
            });
        }
        if(request('barang_detail')){
            $query->whereHas('bttb',function($q){
                $q->whereHas('barang', function($a){
                    $a->where('nama','LIKE','%'.request('barang_detail').'%');
                });
            });
        }

        if(request('pelayaran')){
            $query->whereHas('jadwal_kapal',function($q){
                $q->whereHas('pelayaran', function($a){
                    $a->where('nama','LIKE','%'.request('pelayaran').'%');
                });
            });
        }
        if(request('kapal')){
            $query->whereHas('jadwal_kapal',function($q){
                $q->whereHas('kapal', function($a){
                    $a->where('nama','LIKE','%'.request('kapal').'%');
                });
            });
        }
        if(request('voyage')){
            $query->whereHas('jadwal_kapal',function($q){
                $q->where('voyage','LIKE','%'.request('voyage').'%');
            });
        }
        if(request('dari')){
            $query->whereHas('tarif',function($q){
                $q->whereHas('dari_lokasi', function($a){
                    $a->where('nama','LIKE','%'.request('dari').'%');
                });
            });
        }
        if(request('tujuan')){
            $query->whereHas('tarif',function($q){
                $q->whereHas('tujuan_lokasi', function($a){
                    $a->where('nama','LIKE','%'.request('tujuan').'%');
                });
            });
        }
        if(request('shipment')){
            $query->whereHas('tarif',function($q){
                $q->whereHas('shipmentInfo', function($a){
                    $a->where('nama','LIKE','%'.request('shipment').'%');
                });
            });
        }
        if(request('kondisi')){
            $query->whereHas('tarif',function($q){
                $q->whereHas('kondisiInfo', function($a){
                    $a->where('nama','LIKE','%'.request('kondisi').'%');
                });
            });
        }
        if(request('pengirim')){
            $query->whereHas('pengirim',function($q){
                $q->where('nama','LIKE','%'.request('pengirim').'%');
            });
        }
        if(request('penerima')){
            $query->whereHas('penerima',function($q){
                $q->where('nama','LIKE','%'.request('penerima').'%');
            });
        }
        if(request('marketing')){
            $query->whereHas('tarif',function($q){
                $q->whereHas('customer', function($a){
                    $a->whereHas('marketing', function($b){
                        $b->where('name','LIKE','%'.request('marketing').'%');
                    });
                });
            });
        }
        if(request('marketing_id')){
            $query->whereHas('tarif',function($q){
                $q->whereHas('customer', function($a){
                    $a->where('marketing_id',request('marketing_id'));
                });
            });
        }
        if(request('cs')){
            $query->whereHas('tarif',function($q){
                $q->whereHas('customer', function($a){
                    $a->whereHas('cs', function($b){
                        $b->where('name','LIKE','%'.request('cs').'%');
                    });
                });
            });
        }

        if($sidx){
            $query->select('order_biaya.*');
            $data = $query->orderBy('job')->orderBy('no_job')->skip($start)->take($limit)->get();
        }else{
            $query->select('order_biaya.*');
            $data = $query->orderBy('job')->orderBy('no_job')->skip($start)->take($limit)->get();
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
