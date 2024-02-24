<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class TransaksiTrucking extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transaksi_trucking';
    protected $fillable = [
        'jurnal_piutang',
        'jurnal_hutang',
        'customer_id',
        'tgl_invoice',
        'invoice',
        'order_id',
        'order_trucking_id',
        'rit',
        'tipe',
        'lain_lain',
        'pph',
        'total',
        'order_r1',
        'order_r2',
        'order_vendor',
        'submited_by',
        'pengirim',
        'bupot',
        'no_bupot',
        'masa_bupot',
        'tanggal_bupot',
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

    public function customer()
    {
        return $this->belongsTo(CustomerTrucking::class,'customer_id');
    }

    public function order()
    {
        return $this->belongsTo(OrderTrucking::class,'order_trucking_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'submited_by');
    }
}
