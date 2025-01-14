<?php

namespace App\Imports;

use App\Models\COA;
use App\Models\Jurnal;
use App\Models\Order;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;

class JurnalImport implements ToModel
{
    public function model(array $row)
    {
        if($row[2]){
            $coa = COA::where('kode',str_replace(' ','',$row[2]))->first();
            if ($coa) {
                if(str_contains($row[1],'BBK')){
                    $tipe = 'BBK';
                }else if(str_contains($row[1],'BBM')){
                    $tipe = 'BBM';
                }else if(str_contains($row[1],'BKK')){
                    $tipe = 'BKK';
                }else if(str_contains($row[1],'BKM')){
                    $tipe = 'BKM';
                }else{
                    $tipe = 'JNL';
                }
                $order_array = explode('-',$row[3]);
                $no = explode('/',$row[1]);
                $no = (int)$no[0];
                dd($order_array,count($order_array),$order_array[0]);
                // dd(c);
                $container = null;
                $invoice = null;
                $nopol = null;
                if(count($order_array)==2){
                    $order = Order::where('job',$order_array[0])->where('no_job',(int)$order_array[1])->first();
                    if($order){
                        $container = $order->container;
                        $invoice = $order->invoice;
                        $nopol = $order->nopol;
                    }
                }else{
                    $order = Order::where('job',$order_array[0])->first();
                }
                Jurnal::create([
                    'no' => $no,
                    'coa_id' => $coa->id,
                    'order_id' => $order ? $order->id : null,
                    'nomor' => $row[1],
                    'nama' => str_replace(["'"],'',$row[4]),
                    'debit' => $row[5] ?? 0,
                    'credit' => $row[6] ?? 0,
                    'is_balik' => 1,
                    'created_at' => $row[0],
                    'tipe' => $tipe,
                    'container' => $container,
                    'invoice' => $invoice,
                    'nopol' => $nopol,
                ]);
            }
        }
    }
}
