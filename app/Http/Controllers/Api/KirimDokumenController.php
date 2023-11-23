<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KirimDokumen;
use Illuminate\Http\Request;

class KirimDokumenController extends Controller
{
    public function index()
    {
        $jasa_kirim_id = request('jasa_kirim_id');
        $data = KirimDokumen::where('jasa_kirim_id', $jasa_kirim_id)->get();
        return response($data);
    }

    public function store(Request $request)
    {
        $data = KirimDokumen::create($request->all());
        return response($data);
    }

    public function destroy(KirimDokumen $kirim_dokumen){
        $kirim_dokumen->delete();
        return response('success');
    }
}
