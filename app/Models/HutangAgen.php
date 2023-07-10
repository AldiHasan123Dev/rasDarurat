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
        'tarif_agen_id',
        'order_id',
        'jumlah',
        'status'
    ];

    public function tarif_agen()
    {
        return $this->belongsTo(TarifAgen::class,'tarif_agen_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class,'order_id');
    }
}
