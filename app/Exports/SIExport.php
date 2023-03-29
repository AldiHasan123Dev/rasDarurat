<?php

namespace App\Exports;

use App\Models\Order;
use App\Models\Pengirim;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class SIExport implements FromView
{
    private $attn;
    private $to;
    private $jadwal_kapal_id;
    private $tujuan;

    public function __construct($attn, $to, $jadwal_kapal_id, $tujuan)
    {
        $this->attn = $attn;
        $this->to = $to;
        $this->jadwal_kapal_id = $jadwal_kapal_id;
        $this->tujuan = $tujuan;
    }

    public function view(): View
    {
        $lokasi = $this->tujuan;
        $attn = $this->attn;
        $to = $this->to;
        $pengirim = Pengirim::all();
        $orders = Order::where('jadwal_kapal_id', $this->jadwal_kapal_id)->whereHas('tarif', function($q) use($lokasi){
            $q->where('tujuan',$lokasi);
        })->get();

        return view('exports.si', compact('attn','to','orders','pengirim'));
    }
}
