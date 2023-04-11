<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kendaraan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kendaraan';
    protected $fillable = [
        'pkb',
        'no_rangka',
        'no_mesin',
        'tipe',
        'nopol',
        'milik',
        'is_active',
        'keterangan',
    ];
}
