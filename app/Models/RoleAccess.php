<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleAccess extends Model
{
    use HasFactory;
    protected $table = 'role_access';
    protected $fillable = [
        'role_id',
        'sub_menu_id'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class,'role_id');
    }

    public function sub_menu()
    {
        return $this->belongsTo(SubMenu::class,'sub_menu_id');
    }
}
