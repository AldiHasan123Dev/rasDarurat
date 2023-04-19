<?php

namespace App\Http\Controllers;

use App\Models\SubMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class SubMenuController extends Controller
{
    public function index()
    {
        return view('admin.submenu.index');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['order'] = SubMenu::where('menu_id',$data['menu_id'])->count() + 1;
        SubMenu::create($data);

        return back()->with('success','Data berhasil disimpan');
    }

    public function update(SubMenu $submenu, Request $request)
    {
        if(request('order')){
            $count = SubMenu::where('menu_id',$submenu->menu_id)->count();
            if((int)$request->order>$count){
                return back()->with('danger','Urutan melampaui jumlah menu!');
            }
            SubMenu::where('menu_id',$submenu->menu_id)->where('order',$request->order)->first()->update([
                'order' => $submenu->order
            ]);
            $submenu->update([
                'order' => $request->order
            ]);
        }else{
            $data = $request->all();
            $submenu->update($data);
        }

        return back()->with('success','Data berhasil diupdate');
    }

    public function destroy(SubMenu $submenu)
    {
        $submenu->delete();

        return back()->with('success','Data berhasil dihapus');
    }

    public function datatable()
    {
        $data = SubMenu::all()->sortByDesc('created_at');
        if(request('menu_id')&&request('menu_id')>0){
            $data = SubMenu::all()->where('menu_id', request('menu_id'))->sortByDesc('created_at');
        }
        return Datatables::of($data)
            ->addColumn('menu_id', function($data){
                return $data->menu->name;
            })
            ->addColumn('order', function($data){
                $count = SubMenu::where('menu_id',$data->menu_id)->count();
                $html = '<form action="'.route('submenu.update',$data).'" method="post">
                            <input type="hidden" name="_token" value="'.csrf_token().'" />
                            <input type="hidden" name="_method" value="PUT" />
                            <input type="number" name="order" value="'.$data->order.'" onchange="submit()" max="'.$count.'"/>
                        </form>';
                return $html;
            })
            ->addColumn('action', function ($data) {
                $view = view('admin.submenu.form',['submenu'=>$data])->render();
                $html = '<div class="d-flex gap-1">
                            <form action="'.route('submenu.destroy',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSubMenuUpdate'.$data->id.'" aria-controls="offcanvasSubMenuUpdate'.$data->id.'"><i class="fas fa-pencil"></i></button>
                        </div>

                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasSubMenuUpdate'.$data->id.'" aria-labelledby="offcanvasSubMenuUpdate'.$data->id.'Label">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="offcanvasSubMenuUpdate'.$data->id.'Label">Form SubMenu</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="'.route('submenu.update',$data).'" method="post">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                    <input type="hidden" name="_method" value="PUT" />
                                    '.$view.'
                                </form>
                            </div>
                        </div>';
                return $html;
            })
            ->rawColumns(['action','order'])
            ->make(true);
    }
}
