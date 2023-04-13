<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TransaksiSopir extends Model
{
    use HasFactory;
    protected $table = 'transaksi_sopir';
    protected $fillable = [
        'tgl_invoice',
        'invoice',
        'sopir_id',
        'order_id',
        'order_trucking_id',
        'total',
        'order',
        'submited_by',
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

    public function sopir()
    {
        return $this->belongsTo(Sopir::class,'sopir_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'submited_by');
    }
}
