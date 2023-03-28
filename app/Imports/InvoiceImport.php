<?php

namespace App\Imports;

use App\Models\NSFP;
use App\Models\Order;
use App\Models\Transaksi;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class InvoiceImport implements ToModel, WithStartRow
{

    public function model(array $row)
    {
        $invoice = str_replace(["'","`"," "],'',$row[1]);
        $order = Order::where('job',substr($row[14],0,10))->whereNull('invoice')->first();
        $tgl = str_replace("'",'',$row[7]);
        $date = substr($tgl,6,4).'-'.substr($tgl,3,2).'-'.substr($tgl,0,2);
        $faktur = str_replace(["'"," ","`"],'',$row[10]);
        $status = null;
        $no = (int)substr($invoice,0,4);
        if(substr($faktur,0,3)=='051'){
            $status = 'revisi';
        }
        if($order){
            $nsfp = NSFP::create([
                'invoice' => $invoice,
                'nomor' => $faktur,
                'available' => 0,
                'status' => $status
            ]);
            $transaksi = Transaksi::create([
                'tipe_invoice' => 'global',
                'order_id' => $order->id,
                'pembayar_id' => $order->tarif->customer_id,
                'job' => $order->job,
                'invoice' => $invoice,
                'nsfp' => $faktur,
                'keterangan' => $row[9],
                'tujuan' => $row[8],
                'sub_total' => $row[11],
                'tagihan' => 0,
                'ppn' => $row[12],
                'asuransi' => 0,
                'admin' => 0,
                'total' => $row[13],
                'pph' => 0,
                'order' => $no,
                'created_at' => $date,
                'tanggal_kirim' => null,
            ]);
            Order::where('job',$order->job)->update([
                'invoice' => $invoice,
                'invoice_date' => $date,
                'nsfp' => $faktur
            ]);
        }
    }

    public function startRow(): int
    {
        return 2;
    }
}
