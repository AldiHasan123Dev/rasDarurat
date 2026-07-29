<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JadwalKapalResource;
use App\Models\JadwalKapal;
use App\Models\HutangPelayaran;
use App\Models\Tarif;
use Illuminate\Http\Request;

class JadwalKapalController extends Controller
{
    public function getOne()
    {
        $data = JadwalKapal::find(request('id'));
        $res = new JadwalKapalResource($data);
        return response($res);
    }

public function getByPelayaran(Request $request, $id)
{
    $orderId = $request->order_id;

    $hutangPelayaran = HutangPelayaran::where('order_id', $orderId)->where('status', 1)->first();

    if ($hutangPelayaran) {
        return response()->json([
            'status' => false,
            'message' => 'Anda tidak melakukan pindah kapal pada job ini, karena sudah terjurnal pada Hutang Pelayaran.'
        ], 422);
    }

    $tarif = Tarif::findOrFail($id);

    $jadwalKapal = JadwalKapal::whereNull('td')
        ->where('pelayaran_id', $tarif->pelayaran_id)
        ->get();

    $jadwal = [];

    foreach ($jadwalKapal as $kapal) {
        $jadwal[$kapal->id] = $kapal->kapal->nama .
            ' || Voy. ' . $kapal->voyage .
            ' || ' . $kapal->rute;
    }

    return response()->json($jadwal);
}
}
