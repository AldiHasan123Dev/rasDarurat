<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Truk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'truk';
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

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->created_by = Auth::id();
            $model->updated_by = Auth::id();
        });
        static::saving(function ($model) {
            $model->updated_by = Auth::id();
        });
    }
}
