<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelayaran;
use App\Models\JadwalKapal;
use App\Http\Resources\LapPelayaranResource;
use App\Models\LapPelayaran;
use App\Models\Lokasi;
use App\Models\Kondisi;
use App\Models\Shipment;

class LapPelayaranController extends Controller
{
    public function index() {
        $pelayaran = Pelayaran::all();
        $shipment = Shipment::all();
        $lokasi = Lokasi::all();
        $kondisi = Kondisi::all();
        $jadwalKapal = JadwalKapal::with('kapal')->get();
        return view('admin.laporan.lap_pelayaran',compact('lokasi','shipment','jadwalKapal','pelayaran','kondisi'));
    }

    public function data() {
        $page = request('page'); // get the requested page
        $limit = request('rows') ?? 0; // get how many rows we want to have into the grid
        $sidx = request('sidx'); // get index row - i.e. user click to sort
        $sord = request('sord'); // get the direction
        $search = request('_search'); // get the search
        $is_sample = request('is_sample');
        $keterangan = request('keterangan1');
        $tujuan = request('tujuan');
        $kondisiS = request('kondisi1');
        $shipmentsS = request('shipments1');
        $pelayaranS = request('pelayaran1');


        $is_search = false;
        if($search=='true'){
            $is_search = true;
        }

        $start = max(0, $limit * $page - $limit);

        $hasFilter = false;

       $query = LapPelayaran::with('pelayaran', 'kondisi1', 'shipment', 'lokasi', 'jadwalKapal');

        if ($tujuan) {
            $query->whereHas('lokasi', function ($q) use ($tujuan) {
                $q->where('nama', 'like', '%' . $tujuan . '%');
            });
        }


        // Hitung total data (tanpa limit)
        $count = $query->count();

        // Pagination
        if ($count > 0 && $limit > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }

        if ($page > $total_pages) {
            $page = $total_pages;
        }
        $start = max(0, $limit * ($page - 1));

        // Ambil data sesuai limit & offset
            $data = $query
            ->orderBy('status', 'desc')
            ->orderBy('harga', 'asc')
                        ->skip($start)
                        ->take($limit)
                        ->get();

//                         if ($tujuan) {
//     $data = $query
//         ->orderBy('tujuan', 'desc')
//         ->skip($start)
//         ->take($limit)
//         ->get();
// } else {
//     $data = collect(); // atau bisa juga pakai []
// }

        // Format response
$response = LapPelayaranResource::collection($data);
return response([
    'page' => $page,
    'total' => $total_pages,
    'records' => $count,
    'rows' => $response
]);
    
    }
public function store(Request $request)
{
    // Validasi data input
    $validated = $request->validate([
        'pelayaran_id'     => 'required|exists:pelayaran,id',
        'kondisi'          => 'required|exists:kondisi,id',
        'tujuan'           => 'required|exists:lokasi,id',
        'jadwal_kapal_id'  => 'nullable',
        'shipments'        => 'required|exists:shipments,id',
        'tgl_info'         => 'required|date',
        'keterangan'       => 'nullable|string',
        'comodity'         => 'nullable|string',
        'sales'            => 'nullable|string',
        'harga'            => 'nullable',
        'status'           => 'nullable'
    ]);

    // Ubah harga jadi float/double jika diisi
    if (!empty($validated['harga'])) {
        // Hilangkan koma atau titik ribuan kalau ada, lalu konversi ke float
        $validated['harga'] = (double) str_replace([',', '.'], '', $validated['harga']);
    }

    // Simpan ke tabel lap_pelayaran
    $lapPelayaran = LapPelayaran::create($validated);

    // Redirect dengan pesan sukses
    return redirect()->back()->with('success', 'Data laporan pelayaran berhasil disimpan.');
}


public function show(Request $request)
    {
        $id = $request->get('id');
        $data = LapPelayaran::find($id);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

   public function update(Request $request, $id)
{
    $data = LapPelayaran::find($id);

    if (!$data) {
        return redirect()->back()->with('error', 'Data tidak ditemukan.');
    }

    $validated = $request->validate([
        'pelayaran_id'     => 'required|exists:pelayaran,id',
        'jadwal_kapal_id'  => 'nullable',
        'shipments'        => 'nullable',
        'tujuan'           => 'required|exists:lokasi,id',
        'kondisi'          => 'required|exists:kondisi,id',
        'comodity'         => 'nullable|string|max:255',
        'harga'            => 'required|numeric',
        'keterangan'       => 'nullable|string|max:255',
        'sales'            => 'nullable|string|max:255',
        'tgl_info'         => 'nullable|date',
        'status'           => 'required|in:0,1',
    ]);

    try {
        $data->update($validated);

        // Cek apakah request dari AJAX
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Data berhasil diupdate']);
        }
    } catch (\Exception $e) {
        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat update: ' . $e->getMessage()], 500);
        }
    }
}

}
