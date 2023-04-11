<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransaksiTrucking extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transaksi_trucking';
    protected $fillable = [
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
        'order',
        'submited_by',
    ];

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
