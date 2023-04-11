<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Satuan;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function getBarang(Request $request){
        try {
            $isPaging = $request->has('page');
            $query = Barang::query();
            if ($request->has('cari')) {
                $query->where('nama', 'like', "%$request->cari%");
                $counts = $query->count();
            } else {
                $counts = $query->count();
            }
            $items = $query->limit(20)->offset($isPaging ? ($request->page - 1) * 20 : 0)->get(['id', 'nama as text']);
        } catch (\Throwable $th) {
            return response(['message' => 'Gagal mendapatkan data pengirim', 'system' => $th->getMessage()], 500);
        }
        return response([
            'items' => $items,
            'counts' => $counts,
        ], 200);
    }

    public function getNama()
    {
        $data = Barang::pluck('nama')->toArray();
        return response($data);
    }

    public function getNamaSatuan()
    {
        $data = Satuan::pluck('nama')->toArray();
        return response($data);
    }
}
