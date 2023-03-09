<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TarifResource;
use App\Models\Kondisi;
use App\Models\Lokasi;
use App\Models\Satuan;
use App\Models\Shipment;
use App\Models\Tarif;
use Illuminate\Http\Request;

class TarifController extends Controller
{
    public function getOne()
    {
        $data = Tarif::find(request('id'));
        $res = new TarifResource($data);
        return response($res);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $shipment = Shipment::find($request->shipment);
        $dari = Lokasi::find($request->dari);
        $tujuan = Lokasi::find($request->tujuan);
        $kondisi = Kondisi::find($request->kondisi);
        $satuan = Satuan::find($request->satuan);
        if(!$shipment){
            $shipment = Shipment::create(['nama'=>$request->shipment]);
        }
        if(!$dari){
            $dari = Lokasi::create(['nama'=>$request->dari]);
        }
        if(!$tujuan){
            $tujuan = Lokasi::create(['nama'=>$request->tujuan]);
        }
        if(!$kondisi){
            $kondisi = Kondisi::create(['nama'=>$request->kondisi]);
        }
        if($shipment->nama[0]=='F'||$shipment->nama[0]=='f'){
            $satuan = 1;
        }else{
            $satuan = 2;
        }
        $data['shipment'] = $shipment->id;
        $data['dari'] = $dari->id;
        $data['tujuan'] = $tujuan->id;
        $data['kondisi'] = $kondisi->id;
        $data['satuan'] = $satuan;
        $tarif = Tarif::create($data);

        return response([
            'status' => 'success',
            'data' => $tarif,
            'message' => 'Data berhasil ditambahkan!'
        ]);
    }
}
