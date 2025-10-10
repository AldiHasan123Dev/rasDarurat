<?php

namespace App\Http\Controllers;

use App\Models\HutangAgen;
use App\Models\JadwalKapal;
use App\Models\Kapal;
use App\Models\Order;
use App\Models\Pelayaran;
use App\Models\Tarif;
use App\Models\TarifAgen;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class JadwalKapalController extends Controller
{
    public function index()
    {
        $kapal = Kapal::orderBy('nama')->pluck('nama','id');
        $pelayaran = Pelayaran::pluck('nama','id');
        return view('admin.jadwalkapal.index', compact('pelayaran','kapal'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $kapal = Kapal::find($request->kapal_id);
        if (!$kapal) {
            $kapal = Kapal::create(['nama'=>$request->kapal_id]);
        }
        if ($data['td']&&!is_null($data['td'])) {
            $data['is_active'] = 0;
        }else{
            $data['is_active'] = 1;
        }
        $data['kapal_id'] = $kapal->id;
        $cek = JadwalKapal::where('kapal_id',$data['kapal_id'])->where('voyage',$request->voyage)->first();
        if ($cek) {
            return back()->with('danger','Jadwal Kapal Sudah dibuat!');
        }
        JadwalKapal::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(JadwalKapal $jadwalkapal, Request $request)
    {
        $data = $request->all();
          if ($request->has('td')) {
        $data['is_active'] = !empty($data['td']) && $data['td'] != '0000-00-00' ? 0 : 1;

        Tarif::where('jadwal_kapal_id', $jadwalkapal->id)->update([
            'is_active' => $data['is_active']
        ]);
    }

    $jadwalkapal->update($data);

    return back()->with('success', 'Data berhasil diupdate');
    }

    public function destroy(JadwalKapal $jadwalkapal)
    {
        Order::where('jadwal_kapal_id',$jadwalkapal->id)->update([
            'jadwal_kapal_id' => null
        ]);
        $jadwalkapal->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function jqgrid(Request $request)
{
    $page = $request->input('page', 1);
    $limit = $request->input('rows', 10);
    $sidx = $request->input('sidx', 'etd');
    $sord = $request->input('sord', 'asc');

    // jika sidx kosong, gunakan default
    if (empty($sidx)) {
        $sidx = 'etd';
    }

  $query = JadwalKapal::join('kapal', 'kapal.id', '=', 'jadwal_kapal.kapal_id')
    ->join('pelayaran', 'pelayaran.id', '=', 'jadwal_kapal.pelayaran_id')
    ->select(
        'jadwal_kapal.*',
        'kapal.nama as nama_kapal',
        'pelayaran.nama as nama_pelayaran'
    )
    ->whereNull('jadwal_kapal.td')
    ->whereDate('jadwal_kapal.etd', '<', \Carbon\Carbon::today());


    $count = $query->count();
    $totalPages = $count > 0 ? ceil($count / $limit) : 0;

    $rows = $query
        ->orderBy($sidx, $sord)
        ->skip(($page - 1) * $limit)
        ->take($limit)
        ->get();

    return response()->json([
        'page' => $page,
        'total' => $totalPages,
        'records' => $count,
        'rows' => $rows,
    ]);
}



    public function datatable()
    {
        $limit = request('length');
        $start = request('start') * request('length');
        $data = JadwalKapal::join('kapal','kapal.id','jadwal_kapal.kapal_id')
                ->join('pelayaran','pelayaran.id','=','jadwal_kapal.pelayaran_id')
                ->select('jadwal_kapal.*','kapal.nama as nama_kapal','pelayaran.nama as nama_pelayaran')
                ->offset($start)->limit($limit);
        $count = JadwalKapal::select('id')->count();
        return Datatables::of($data)
        ->order(function ($query) {
            $query->orderBy('is_active', 'desc')->orderBy('closing', 'desc');
        })
        ->addColumn('tools', function($data){
            $html = '<div class="dropend">
                        <button class="no-attr dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-list"></i></button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="'.route('cetak.shipment',['jadwal_kapal_id'=>$data->id]).'">Lihat SI</a></li>
                        </ul>
                    </div>';
            return $html;
        })
        ->addColumn('kapal', function($data){
            return $data->nama_kapal;
        })
        ->addColumn('pelayaran', function($data){
            return $data->nama_pelayaran;
        })
        ->addColumn('etd', function($data){
            return is_null($data->etd)?'-':date('d/m/Y',strtotime($data->etd));
        })
        ->addColumn('td', function($data){
            return is_null($data->td)?'-':date('d/m/Y',strtotime($data->td));
        })
         ->addColumn('eta', function($data){
            return is_null($data->eta)?'-':date('d/m/Y',strtotime($data->eta));
        })
        ->addColumn('closing', function($data){
            return is_null($data->closing)?'-':date('d/m/Y',strtotime($data->closing));
        })
        ->addColumn('ba_kirim', function($data){
            return is_null($data->ba_kirim)?'-':date('d/m/Y',strtotime($data->ba_kirim));
        })
        ->addColumn('status', function($data){
            $val = $data->is_active==1?0:1;
            $class = $data->is_active==1?'success':'danger';
            $name = $data->is_active==1?'active':'unactive';
            // $html = '<form method="post" action="'.route('jadwalkapal.update',$data).'">
            //             <input type="hidden" name="_token" value="'.csrf_token().'" />
            //             <input type="hidden" name="_method" value="PUT" />
            //             <input type="hidden" name="is_active" value="'.$val.'" />
            //             <div class="form-check form-switch">
            //                 <input class="form-check-input" disabled onchange="submit()" value="'.$val.'" type="checkbox" name="is_active" role="switch" id="flexSwitchCheckDefault" '.$checked.'>
            //                 <label class="form-check-label">'.$name.'</label>
            //             </div>
            //         </form>';
            $html = '<span class="text-'.$class.'">'.$name.'</span>';
            return  $html;
        })
        ->addColumn('action', function ($data) {
                $pelayaran = Pelayaran::pluck('nama','id');
                $kapal = Kapal::pluck('nama','id');
                $view = view('admin.jadwalkapal.form',['jadwalkapal'=>$data,'pelayaran'=>$pelayaran,'kapal'=>$kapal])->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('jadwalkapal.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasJadwalKapalUpdate'.$data->id.'" aria-controls="offcanvasJadwalKapalUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasJadwalKapalUpdate'.$data->id.'" aria-labelledby="offcanvasJadwalKapalUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasJadwalKapalUpdate'.$data->id.'Label">Form JadwalKapal</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('jadwalkapal.update',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                    <input type="hidden" name="_method" value="PUT" />
                                    '.$view.'
                                </form>
                            </div>
                        </div>';
                return $html;
            })
            ->setFilteredRecords($count)
            ->rawColumns(['action','status','tools'])
            ->make(true);
    }
}
