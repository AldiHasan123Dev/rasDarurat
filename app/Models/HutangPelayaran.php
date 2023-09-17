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
        'jurnal',
        'no_bg',
        'tgl_bg',
        'pelayaran_id',
        'order_id',
        'jumlah',
        'opp',
        'apbs',
        'cleaning',
        'thc',
        'lss',
        'opp_stamp',
        'opt',
        'opt_stamp',
        'ut',
        'bl',
        'ut_stamp',
        'pph',
        'pembulatan',
        'status',
        'is_lock',
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
