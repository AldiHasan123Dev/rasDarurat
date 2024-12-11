<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class CustomerTrucking extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customer_trucking';
    protected $fillable = [
        'pph_23',
        'r1',
        'r2',
        'nama',
        'pic',
        'alamat',
        'hp',
        'nik',
        'npwp',
        'nama_npwp',
        'alamat_npwp',
        'is_active',
        'keterangan',
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
