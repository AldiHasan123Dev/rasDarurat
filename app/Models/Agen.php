<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agen extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'agen';
    protected $fillable = [
        'kode',
        'nama',
        'pic',
        'alamat',
        'kota',
        'telp',
        'fax',
        'email',
        'hp',
    ];
}
