<?php

namespace App\Http\Controllers;

use App\Models\BTTB;
use App\Models\Customer;
use App\Models\JadwalKapal;
use App\Models\Lokasi;
use App\Models\Order;
use App\Models\Pengirim;
use App\Models\Tarif;
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

        $data = Order::where('job',$order->job)->orderBy('tarif_id')->get();
        return view('admin.cetak.packing_list', compact('order','data'));
    }

    public function packingListKubikasi()
    {
        $order = Order::find(request('order_id'));
        if (!$order) {
            return redirect()->route('order.index');
        }

        $data = Order::where('job',$order->job)->orderBy('tarif_id')->get();
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
        $lokasi = request('tujuan');
        $tujuan = Lokasi::find($lokasi);
        $pengirim = Pengirim::all();
        $orders = Order::where('jadwal_kapal_id', $id)->get();

        $jadwal_kapals = JadwalKapal::all()->where('is_active',0);
        $pelayaran = $jadwal_kapals->pluck('pelayaran_id')->toArray();
        $lokasi = Tarif::whereIn('pelayaran_id',$pelayaran)->pluck('tujuan')->toArray();
        $data_tarif_lokasi = array_unique($lokasi);
        $data_lokasi = Lokasi::whereIn('id',$data_tarif_lokasi)->get();
        return view('admin.cetak.shipment', compact('orders','jadwal_kapal','tujuan','pengirim','jadwal_kapals','data_lokasi'));
    }

    public function invoice()
    {
        $order = Order::find(request('order_id'));
        $orders = Order::where('job',$order->job)->get();
        if (!$order) {
            return back()->with('danger','Anda harus memilih job terlebih dahulu!');
        }

        $type = strtoupper(strtolower($order->tarif->shipmentInfo->nama[0]));
        if ($type=='F') {
            $nama = 'Cont';
            $kategori = $orders->count().' Cont';
            $jumlah = $orders->count();
            $tarif = $order->tarif->tarif;
            $price = $jumlah * $tarif;
        }else{
            $kategori = 0;
            $jumlah = 0;
            foreach ($orders as $or ) {
                if (is_null($or->berat)||$or->berat<=0) {
                    $kategori+=$or->bttb->sum('vol');
                }else{
                    $kategori+=$or->bttb->sum('berat');
                }
                $jumlah += $or->bttb->sum('qty');
            }
            $nama = 'vol';
            $tarif = $order->tarif->tarif;
            $price = $tarif * $kategori * $jumlah;
        }
        $doc = 0;

        return view('admin.cetak.invoice',compact('order','orders','nama','kategori','jumlah','doc','tarif','price'));
    }
}
