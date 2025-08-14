<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agen;
use App\Models\Customer;
use App\Models\Lain;
use App\Models\Lokasi;
use App\Models\LSS;
use App\Models\Pelayaran;
use App\Models\TarifAgen;
use App\Models\TarifPelayaran;
use App\Models\TarifTrucking;
use App\Models\THC;

class EstimasiController extends Controller
{
    public function biaya()
    {
        return view('admin.estimasi.biaya');
    }

    public function hpp(Request $request)
{
    $lokasi = Lokasi::orderBy('nama')->get();
    $pelayarans = Pelayaran::orderBy('nama')->get();
    $lokasiPelayaran = Lokasi::orderBy('nama')->get();
    $customers = Customer::orderBy('nama')->get(['id','nama']);

    // Nilai default
    $cont = 20;
    $stuffing = 'dalam';
    $dari = 'PELABUHAN JAYAPURA';
    $tujuan = 'JAYAPURA';
    $pelayaranId = 3;
    $agenId = 1;

    // Filter agen berdasarkan request lokasi_pelayaran (jika ada)
    $agens = collect(); // kosong default
    if ($request->filled('lokasi_pelayaran')) {
        $agens = Agen::where('kota', $request->lokasi_pelayaran)
            ->orderBy('nama')
            ->get();
    }

    $penerima = collect(); // kosong default
    if ($request->filled('penerima')) {
       $agenIds = TarifAgen::where('agen_id', $request->input('penerima'))
        ->whereNotNull('penerima_id')
        ->where('is_active', 1)
        ->pluck('penerima_id');

    // Ambil data penerima dari tabel customers
    $penerima = Customer::whereIn('id', $agenIds)
        ->get(['id', 'nama']);
    }

    return view('admin.estimasi.hpp', compact(
        'lokasi',
        'penerima',
        'pelayarans',
        'lokasiPelayaran',
        'agens',
        'customers',
        'cont',
        'stuffing',
        'dari',
        'tujuan',
        'pelayaranId',
        'agenId'
    ));
}

   public function hitung(Request $request)
    {
        $cont       = $request->cont;
        $stuffing   = $request->stuffing;
        $dari       = $request->dari;
        $tujuan     = $request->tujuan;
        $pelayaran  = $request->pelayaran;
        $agenId     = $request->agen;
        $pembayarId = $request->pembayar_id;
        $penerimaId = $request->penerima_id;

        $truk = TarifTrucking::find($dari);
       $conts = ($cont == 20) ? '20' : (($cont == 40) ? '40' : $cont);

        $lss = LSS::whereHas('lokasi', function ($q) use ($tujuan) {
            $q->where('nama', 'like', '%' . $tujuan . '%');
        })->first();

        $thc = THC::whereHas('lokasi', function ($q) use ($tujuan) {
            $q->where('nama', 'like', '%' . $tujuan . '%');
        })->first();
        
        $agen = TarifAgen::where('agen_id', $agenId)
            ->where('pembayar_id', (int)$pembayarId)
            ->where('penerima_id', (int)$penerimaId)
            ->whereHas('dariInfo', function ($q) use ($dari) {
                $q->where('nama', 'like', '%' . $dari . '%');
            })
            ->whereHas('tujuanInfo', function ($q) use ($tujuan) {
                $q->where('nama', $tujuan);
            })
             ->whereHas('shipment', function ($q) use ($cont) {
                $q->where('nama', 'LIKE', '%' . $cont . '%');
            })
            ->where('is_active', 1)
            ->first();
        $kirimDok = Lokasi::where('nama', $tujuan)->first();

        $pelayarant = TarifPelayaran::where('pelayaran_id', $pelayaran)
            ->whereHas('tujuanInfo', function ($q) use ($tujuan) {
                $q->where('nama', $tujuan);
            })
            ->whereHas('port', function ($q) use ($dari) {
                $q->where('name', $dari);
            })
            ->whereHas('shipment', function ($q) use ($cont) {
                $q->where('nama', 'LIKE', '%' . $cont . '%');
            })
            ->whereNull('deleted_at')
            ->where('is_active', 1)
            ->first();

        // Stuffing lawan kata
        $stuffingLawan = $stuffing == 'dalam' ? 'luar' : 'dalam';

        $lain = Lain::where('nama', 'NOT LIKE', '%' . $stuffingLawan . '%')->whereNotNull('urutan')->orderBy('urutan')->get();

        foreach ($lain as $item) {
    switch ($item->nama) {
        case 'TRUCKING':
            $data['TRUCKING'] = $stuffing == 'dalam' ? 0 : ($truk->tarif ?? 0);
            break;

        case 'Door/ Agen':
            $data['Door/ Agen'] = $agen->tarif ?? 0;
            break;

        case 'Kirim Dokumen':
            $data['Kirim Dokumen'] = $kirimDok->harga ?? 0;
            break;

        case 'PELAYARAN':
            $data['PELAYARAN'] = $pelayarant->tarif ?? 0;
            break;
        //  case 'PELAYARAN':
        //     $data['PELAYARAN'] = $pelayarant->tarif ?? 0;
        //     break;
        case 'UT':
            $data['UT'] = $pelayarant->tarif ?? 0;
            break;
        case 'LSS':
            $data['LSS'] = $cont == 20 ? ($lss->cont_20??0) : ($lss->cont_40??0);
            break;
        case 'THC Tujuan':
            $data['THC Tujuan'] = $cont == 20 ? ($thc->cont_20??0) : ($thc->cont_40??0);
            break;
        case 'THC STUFF DALAM (SBY)':
            if (stripos($dari, 'SURABAYA') !== false) {
                // Kalau ada kata "surabaya" di $dari
                $data['THC STUFF DALAM (SBY)'] = $cont == 20 ? ($item->cont_20 ?? 0) : ($item->cont_40 ?? 0);
            } else {
                // Kalau tidak ada kata "surabaya"
                $data['THC STUFF DALAM (SBY)'] = 0;
            }
        break;
        default:
            $data[$item->nama] = $cont == 20 ? ($item->cont_20 ?? 0) : ($item->cont_40 ?? 0);
            break;
    }
}

        $hpp = array_sum($data);
        $r   = $cont == 20 ? 600000 : 1300000;
        $margin = $hpp > 0 ? ($r / $hpp * 100) : 0;
        $total  = $r + $hpp;
        $pph    = $total * 0.02;
        $totalPph = $pph + $total;
        $ppn    = round($totalPph * 0.011);
        $totalPpn =round($ppn + $totalPph);

        return response()->json([
            'active'      => true,
            'data'        => $data,
            'hpp'         => $hpp,
            'r'           => $r,
            'margin'      => $margin,
            'total'       => $total,
            'pph'         => $pph,
            'total_pph'   => $totalPph,
            'ppn'         => $ppn,
            'total_ppn'   => $totalPpn,
        ]);
    }
}
