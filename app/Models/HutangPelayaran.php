<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HutangPelayaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hutang_pelayaran';
    protected $fillable = [
        'tarif_pelayaran_id',
        'order_id',
        'jumlah',
        'opp',
        'apbs',
        'thc',
        'cleaning',
        'lss',
        'status',
    ];

    public function tarif_pelayaran()
    {
        return $this->belongsTo(TarifPelayaran::class,'tarif_pelayaran_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class,'order_id');
    }
}
