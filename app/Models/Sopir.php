<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sopir extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sopir';
    protected $fillable = [
        'nama',
        'alamat',
        'hp',
        'is_active',
    ];
}
