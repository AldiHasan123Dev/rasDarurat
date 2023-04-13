<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Asuransi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'asuransi';
    protected $fillable = [
        'pelayaran_id',
        'nama',
        'rate',
        'admin',
        'min',
        'max',
        'keterangan',
    ];

    public function pelayaran()
    {
        return $this->belongsTo(Pelayaran::class);
    }

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
