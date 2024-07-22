<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JadwalKapalResource;
use App\Models\JadwalKapal;
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

    public function getByPelayaran($id)
    {
        $tarif = Tarif::find($id);
        $jadwal_kapal = JadwalKapal::all()->whereNull('td')->where('pelayaran_id',$tarif->pelayaran_id);
        $jadwal = array();
        foreach ($jadwal_kapal as $kapl ) {
            $jadwal[$kapl->id] = $kapl->kapal->nama.' || Voy. '.$kapl->voyage.'  || '.$kapl->rute;
        }

        return response($jadwal);
    }
}
