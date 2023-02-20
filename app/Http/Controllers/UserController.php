<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.user.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8'
        ]);
        $data = $request->all();
        $data['password'] = Hash::make($request->password);
        User::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(User $user, Request $request)
    {
        $data = $request->all();
        if (!is_null($request->password)) {
            $data['password'] = Hash::make($request->password);
        }else{
            unset($data['password']);
        }
        $user->update($data);

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = User::all()->sortByDesc('created_at');

        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                $view = view('admin.user.form',['cus'=>$data])->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('user.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasPelayaranUpdate'.$data->id.'" aria-controls="offcanvasPelayaranUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasPelayaranUpdate'.$data->id.'" aria-labelledby="offcanvasPelayaranUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasPelayaranUpdate'.$data->id.'Label">Form Pelayaran</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('user.update',$data).'" method="post">
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
