<?php

namespace App\Http\Controllers;

use App\Models\BTTB;
use App\Models\Customer;
use App\Models\JadwalKapal;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class CetakController extends Controller
{
    public function suratJalan()
    {
        $penerima = Customer::get();
        // $pdf = PDF::loadView('pdf.contoh');
        // return $pdf->stream('document.pdf');
        return view('admin.cetak.surat_jalan', compact('penerima'));
        // $mpdf = new PDF();

        // Write some HTML code:
        // $mpdf->WriteHTML('Hello World');

        // Output a PDF file directly to the browser
        // $mpdf->Output();
        // $pdf = PDF::loadView('admin.tagihan.pdf', compact('bills','today'));

        // $content = $mpdf->download()->getOriginalContent();
        // Storage::put('public/bills/bubla.pdf',$content);
    }

    public function pdfSuratJalan(Request $request)
    {
        $customer = Customer::find($request->penerima);
        $data = $request->all();
        $data['penerima'] = $customer->nama;
        $data['kota'] = $customer->kota;
        // dd($data);
        $pdf = Pdf::loadView('pdf.surat_jalan', compact('data'));
        return $pdf->stream('document.pdf');
        return view('pdf.surat_jalan',compact('data'));
    }

    public function pickOrder()
    {
        $pengirim = Customer::get();
        $penerima = Customer::get();
        $jadwal_kapal = JadwalKapal::join('kapal','kapal.id','=','jadwal_kapal.kapal_id')->select('jadwal_kapal.*')->where('jadwal_kapal.is_active',1)->get();
        return view('admin.cetak.pick_order', compact('pengirim','penerima','jadwal_kapal'));
    }

    public function packingList()
    {
        $order = Order::find(request('order_id'));
        if (!$order) {
            return redirect()->route('order.index');
        }

        $data = Order::where('job',$order->job)->get();
        return view('admin.cetak.packing_list', compact('order','data'));
    }

    public function packingListKubikasi()
    {
        $order = Order::find(request('order_id'));
        if (!$order) {
            return redirect()->route('order.index');
        }

        $data = Order::where('job',$order->job)->get();
        return view('admin.cetak.packing_list_kubikasi', compact('order','data'));
    }

    public function bttb()
    {
        $order = Order::find(request('order_id'));
        if (!$order) {
            return redirect()->route('order.index');
        }

        $data = BTTB::where('order_id',$order->id)->get();
        return view('admin.cetak.bttb', compact('order','data'));
    }

    public function bttbKubikasi()
    {
        $order = Order::find(request('order_id'));
        if (!$order) {
            return redirect()->route('order.index');
        }

        $data = BTTB::where('order_id',$order->id)->get();
        return view('admin.cetak.bttb_kubikasi', compact('order','data'));
    }

    public function shipment()
    {
        $id = request('jadwal_kapal_id');
        $jadwal_kapal = JadwalKapal::find($id);

        if (!$jadwal_kapal) {
            return redirect()->route('order.index');
        }

        $orders = Order::where('jadwal_kapal_id', $id)->get();

        return view('admin.cetak.shipment', compact('orders','jadwal_kapal'));
    }
}
