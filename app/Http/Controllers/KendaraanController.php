<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;

class KendaraanController extends Controller
{

    public function index()
{
    $kendaraanList = Kendaraan::where('is_active', 1)
        ->whereIn('milik', ['R1', 'R2'])
        ->get();

    $today = Carbon::today();
    $reminders = [];

    foreach ($kendaraanList as $kendaraan) {
        $reminder = [];

        // Reminder KIR
        if ($kendaraan->kir) {
          $days = $today->diffInDays(Carbon::parse($kendaraan->kir), false);
            if ($days === -30) {
                $reminder[] = 'KIR akan jatuh tempo dalam 30 hari';
            } elseif ($days < 0) {
                $reminder[] = 'KIR HABIS!';
            }
        }

        // Reminder PKB
        if ($kendaraan->masa_pkb) {
            $days = $today->diffInDays(Carbon::parse($kendaraan->masa_pkb), false);
            if ($days === -30) {
                $reminder[] = 'PKB akan jatuh tempo dalam 30 hari';
            } elseif ($days < 0) {
                $reminder[] = 'PKB HABIS!';
            }
        }

        // Reminder STID
        if ($kendaraan->stid) {
           $days = $today->diffInDays(Carbon::parse($kendaraan->stid), false);
            if ($days === -30) {
                $reminder[] = 'STID akan jatuh tempo dalam 30 hari';
            } elseif ($days < 0) {
                $reminder[] = 'STID HABIS!';
            }
        }

        if (!empty($reminder)) {
            $reminders[] = [
                'id' => $kendaraan->id,
                'no_rangka' => $kendaraan->no_rangka,
                'no_mesin' => $kendaraan->no_mesin,
                'nopol' => $kendaraan->nopol,
                'milik' => $kendaraan->milik,
                'masa_pkb' => $kendaraan->masa_pkb,
                'kir' => $kendaraan->kir,
                'stid' => $kendaraan->stid,
                'reminder' => $reminder,
                'status' => $kendaraan->is_active
            ];
        }
    }
    return view('admin.kendaraan.index', compact('reminders'));
}


    public function store(Request $request)
    {
        $data = $request->all();
        Kendaraan::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

        public function massUpdate(Request $request)
    {
        $items = $request->input('items', []);
        foreach ($items as $item) {
            Kendaraan::where('id', $item['id'])->update([
                'masa_pkb' => $item['masa_pkb'],
                'kir'      => $item['kir'],
                'stid'     => $item['stid'],
                'is_active'     => $item['is_active'],
            ]);
        }

        return back()->with('success', 'Semua data reminder berhasil diperbarui.');
    }


    public function update(Kendaraan $kendaraan, Request $request)
    {
        $data = $request->all();
        $kendaraan->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(Kendaraan $kendaraan)
    {
        $kendaraan->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
       
        $today = Carbon::today();
$threshold = Carbon::today()->subDays(30);

$data = Kendaraan::where(function ($query) use ($threshold) {
        $query->whereIn('milik', ['R1', 'R2'])
              ->where('is_active', 1)
              ->whereDate('kir', '>', $threshold)
              ->whereDate('masa_pkb', '>', $threshold)
              ->whereDate('stid', '>', $threshold);
    })
    ->orWhere(function ($query) {
        $query->whereIn('milik', ['R1', 'R2'])
              ->where('is_active', 0);
    })
    ->orWhere(function ($query) {
        $query->whereNotIn('milik', ['R1', 'R2']);
    })
    ->orderBy('is_active', 'desc')
    ->orderBy('created_at', 'desc')
    ->get();

        return Datatables::of($data)
            ->addColumn('created_at', function($data){
                return date('d/m/y', strtotime($data->created_at));
            })
            ->addColumn('is_active', function($data){
                return $data->is_active ? 'Aktif' : 'Non Aktif';
            })
            ->addColumn('action', function ($data) {
                $view = view('admin.kendaraan.form',['kendaraan'=>$data])->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('kendaraan.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasKendaraanUpdate'.$data->id.'" aria-controls="offcanvasKendaraanUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasKendaraanUpdate'.$data->id.'" aria-labelledby="offcanvasKendaraanUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasKendaraanUpdate'.$data->id.'Label">Form Kendaraan</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('kendaraan.update',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                    <input type="hidden" name="_method" value="PUT" />
                                    '.$view.'
                                </form>
                            </div>
                        </div>';
                return $html;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
