<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Role;
use App\Models\RoleAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class RoleController extends Controller
{
    public function index()
    {
        return view('admin.role.index');
    }

    public function create()
    {
        $menus = Menu::all();
        return view('admin.role.create', compact('menus'));
    }

    public function edit(Role $role)
    {
        $menus = Menu::all();
        return view('admin.role.edit', compact('menus','role'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        Role::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(Role $role, Request $request)
    {
        $data = $request->all();
        $role->update([
            'name' => $data['name']
        ]);

        RoleAccess::where('role_id',$role->id)->delete();
        foreach ($data['sub_menu_id'] as $item ) {
            RoleAccess::create([
                'role_id' => $role->id,
                'sub_menu_id' => $item
            ]);
        }

        return redirect()->route('role.index')->with('success','Data berhasil disimpan');

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = Role::all()->sortByDesc('created_at');

        return Datatables::of($data)
            ->addColumn('menu',function($data){
                $menu = '';
                foreach ($data->access as $item ) {
                    $menu .= $item->sub_menu->title.'; ';
                }
                return $menu;
            })
            ->addColumn('action', function ($data) {
                $view = view('admin.role.form',['role'=>$data])->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('role.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <a href="'.route('role.edit',$data).'" class="no-attr text-primary"><i class="fas fa-pencil"></i></a>
                        </div>';
                return $html;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
