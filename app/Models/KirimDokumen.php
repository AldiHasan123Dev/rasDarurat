<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class KirimDokumen extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kirim_dokumen';
    protected $fillable = [
        'jasa_kirim_id',
        'order_id',
        'nama'
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

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
