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
use Illuminate\Support\Facades\Auth;

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

    auth()->shouldUse('web'); // <-- Penting
    $request->validate([
        'pelayaran_id' => 'required',
        'customer_id' => 'required',
        'stuffing' => 'required',
        'shipment' => 'required',
        'dari' => 'required',
        'tujuan' => 'required',
        'kondisi' => 'required',
        'satuan' => 'required',
        'tarif' => 'required',
    ]);

    $data = $request->all();

    // 🔹 Cek dan buat jika belum ada
    $shipment = Shipment::find($request->shipment) ?? Shipment::create(['nama' => $request->shipment]);
    $dari = Lokasi::find($request->dari) ?? Lokasi::create(['nama' => $request->dari]);
    $tujuan = Lokasi::find($request->tujuan) ?? Lokasi::create(['nama' => $request->tujuan]);
    $kondisi = Kondisi::find($request->kondisi) ?? Kondisi::create(['nama' => $request->kondisi]);
    $userId = null;
    $userId = auth()->id();
        if (!$userId && $request->user()) {
            $userId = $request->user()->id;
        }
        if (!$userId) {
            try {
                $userId = Auth::guard('sanctum')->id();
                dd($userId);
            } catch (\Throwable $e) {
                // guard might not exist or throw; ignore and continue
            }
        }
        if (!$userId && $request->filled('created_by')) {
            $userId = $request->input('created_by');
             $userId = $request->input('updated_by');
        }

    // 🔹 Tentukan satuan berdasarkan huruf pertama shipment
    $satuan = strtoupper(substr($shipment->nama, 0, 1)) === 'F' ? 1 : 2;

    // 🔹 Isi data akhir
    $data['shipment'] = $shipment->id;
    $data['dari'] = $dari->id;
    $data['tujuan'] = $tujuan->id;
    $data['kondisi'] = $kondisi->id;
    $data['satuan'] = $satuan;

    // 🔹 Tambahkan user yang membuat & mengupdate
    $data['created_by'] = $userId;
    $data['updated_by'] = $userId;

    // 🔹 Simpan ke database
    $tarif = Tarif::create($data);

    return response()->json([
        'status' => 'success',
        'data' => $tarif,
        'message' => 'Data berhasil ditambahkan!',
    ]);
}

    public function update(Request $request)
    {
        Tarif::find($request->id)->update($request->all());

        return response('success');
    }
}
