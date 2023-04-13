<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class BTTB extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bttb';
    protected $fillable = [
        'order_id',
        'no_gudang',
        'barang_id',
        'qty',
        'satuan_id',
        'p',
        'l',
        't',
        'vol',
        'berat',
        'tgl_masuk',
        'pengirim_id',
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

    public function pengirim()
    {
        return $this->belongsTo(Customer::class,'pengirim_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class,'barang_id');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class,'satuan_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class,'order_id');
    }
}
