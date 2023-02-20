<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class ShipmentController extends Controller
{
    public function index()
    {
        return view('admin.shipment.index');
    }

    public function store(Request $request)
    {
              $data = $request->all();
        Shipment::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(Shipment $shipment, Request $request)
    {
        $data = $request->all();
        $shipment->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(Shipment $shipment)
    {
        $shipment->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = Shipment::all()->sortByDesc('created_at');

        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                $view = view('admin.shipment.form',['shipment'=>$data])->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('shipment.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasShipmentUpdate'.$data->id.'" aria-controls="offcanvasShipmentUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasShipmentUpdate'.$data->id.'" aria-labelledby="offcanvasShipmentUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasShipmentUpdate'.$data->id.'Label">Form Shipment</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('shipment.update',$data).'" method="post">
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
