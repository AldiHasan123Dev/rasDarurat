<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\RoleAccess;
use Illuminate\Http\Request;

class RoleAccessController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->all();
        $role = Role::create([
            'name' => $data['name']
        ]);
        foreach ($data['sub_menu_id'] as $item ) {
            RoleAccess::create([
                'role_id' => $role->id,
                'sub_menu_id' => $item
            ]);
        }

        return redirect()->route('role.index')->with('success','Data berhasil disimpan');
    }
}
