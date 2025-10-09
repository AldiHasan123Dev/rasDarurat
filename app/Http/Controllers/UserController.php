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
            'password' => 'required|min:8',
            'email' => 'required|email|unique:users'
        ]);
        $data = $request->all();
        $data['password'] = Hash::make($request->password);
        User::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function edit(User $uservaleg55)
    {
        return view('admin.user.edit',compact('uservaleg55'));
    }

    public function update(User $uservaleg55, Request $request)
    {
        $request->validate([
            'email' => 'email|unique:users,email,'.$uservaleg55->id
        ]);
        $data = $request->all();
        if (!is_null($request->password)) {
            $data['password'] = Hash::make($request->password);
        }else{
            unset($data['password']);
        }
       $uservaleg55->update($data);

        return back()->with('success','Data berhasil diupdate');
    }
    

    public function destroy(User $uservaleg55)
    {
        $uservaleg55->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
{
    $data = User::leftJoin('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.*')
                ->get();

    return Datatables::of($data)
       ->addColumn('usia', function($data) {
    if ($data->tgl_lahir) {
        $tglLahir = \Carbon\Carbon::parse($data->tgl_lahir);
        $now = \Carbon\Carbon::now();

        $years = $tglLahir->diffInYears($now);
        $months = $tglLahir->addYears($years)->diffInMonths($now);
        $days = $tglLahir->addMonths($months)->diffInDays($now);

        return "{$years} tahun {$months} bulan";
    }
    return '-';
})
->addColumn('lama_masuk', function($data) {
    if ($data->tgl_masuk) {
        $tglLahir = \Carbon\Carbon::parse($data->tgl_masuk);
        $now = \Carbon\Carbon::now();

        $years = $tglLahir->diffInYears($now);
        $months = $tglLahir->addYears($years)->diffInMonths($now);
        $days = $tglLahir->addMonths($months)->diffInDays($now);

        return "{$years} tahun {$months} bulan";
    }
    return '-';
})

        ->addColumn('role', function($data){
            return $data->role->name ?? '-';
        })
        ->addColumn('action', function ($data) {
            $view = view('admin.user.form1', ['uservaleg55' => $data])->render();
            $html = '<div class="d-flex gap-1">
                        <form action="'.route('uservaleg55.destroy', $data).'" method="post">
                            <input type="hidden" name="_token" value="'.csrf_token().'" />
                            <input type="hidden" name="_method" value="delete" />
                            <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                        </form>
                        <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasUserUpdate'.$data->id.'" aria-controls="offcanvasUserUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                    </div>

                    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasUserUpdate'.$data->id.'" aria-labelledby="offcanvasUserUpdate'.$data->id.'Label">
                        <div class="offcanvas-header">
                            <h5 class="offcanvas-title" id="offcanvasUserUpdate'.$data->id.'Label">Form User</h5>
                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body">
                            <form action="'.route('uservaleg55.update', $data).'" method="post">
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
