<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubMenu extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sub_menu';
    protected $fillable = [
        'menu_id',
        'title',
        'icon',
        'name',
        'url',
        'order',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class,'menu_id');
    }
}
