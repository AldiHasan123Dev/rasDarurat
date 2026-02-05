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
        'jurnal_opp',
        'jurnal_opt',
        'jurnal_ut',
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
        'ut_cleaning',
        'pph',
        'pembulatan',
        'penambahan',
        'penambahan_nominal',
        'pengurangan',
        'pengurangan_nominal',
        'status',
        'is_lock',
        'no',
        'hp_seal',
        'vgm',
        'opt_pph',
        'kodongan',
    ];

    public function pelayaran()
    {
        return $this->belongsTo(Pelayaran::class,'pelayaran_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class,'order_id');
    }

    public function jurnal_opp()
    {
        $jurnal = Jurnal::where('no_bg',$this->no_bg_opp)->whereIn('tipe',['JNL','TEST'])->first();
        return $jurnal->nomor ?? '';
    }

    public function jurnal_opt()
    {
        $jurnal = Jurnal::where('no_bg',$this->no_bg_opt)->whereIn('tipe',['JNL','TEST'])->first();
        return $jurnal->nomor ?? '';
    }

    public function jurnal_ut()
    {
        $jurnal = Jurnal::where('no_bg',$this->no_bg_ut)->whereIn('tipe',['JNL','TEST'])->first();
        return $jurnal->nomor ?? '';
    }
}
