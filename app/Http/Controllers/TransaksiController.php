<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class TransaksiController extends Controller
{
    public function index()
    {
        return view('admin.transaksi.index');
    }

    public function store(Request $request)
    {
              $data = $request->all();
        Transaksi::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(Transaksi $transaksi, Request $request)
    {
        $data = $request->all();
        $transaksi->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(Transaksi $transaksi)
    {
        $transaksi->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = Transaksi::all()->sortByDesc('created_at');

        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                $view = view('admin.transaksi.form',['transaksi'=>$data])->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('transaksi.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTransaksiUpdate'.$data->id.'" aria-controls="offcanvasTransaksiUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasTransaksiUpdate'.$data->id.'" aria-labelledby="offcanvasTransaksiUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasTransaksiUpdate'.$data->id.'Label">Form Transaksi</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('transaksi.update',$data).'" method="post">
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
