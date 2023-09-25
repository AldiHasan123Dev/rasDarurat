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
        'invoice',
        'tgl_invoice',
        'jurnal',
        'no_bg_opp',
        'tgl_bg_opp',
        'nominal_bg_opp',
        'no_bg_opt',
        'tgl_bg_opt',
        'nominal_bg_opt',
        'no_bg_ut',
        'tgl_bg_ut',
        'nominal_bg_ut',
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
        'no',
    ];

    public function pelayaran()
    {
        return $this->belongsTo(Pelayaran::class,'pelayaran_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class,'order_id');
    }
}
