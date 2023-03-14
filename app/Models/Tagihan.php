<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tagihan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tagihan';
    protected $fillable = [
        'order_id',
        'nama',
        'jumlah',
        'catatan',
        'status',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class,'order_id');
    }
}
