<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HutangAgen extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hutang_agen';
    protected $fillable = [
        'order_id',
        'jurnal',
        'invoice',
        'draf',
        'tarif',
        'ppn',
        'pph',
        'tanggal',
        'status',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class,'invoice_agen','invoice');
    }

    public function order()
    {
        return $this->belongsTo(Order::class,'order_id');
    }

    public function agen()
    {
        $order = $this->order;
        return $order->agent->nama ?? '-';
    }
}
