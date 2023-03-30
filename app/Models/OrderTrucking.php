<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderTrucking extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'order_trucking';
    protected $fillable = [
        'order_id',
        'customer_id',
        'sopir_id',
        'tarif_id',
        'kendaraan_id',
        'dari',
        'tujuan',
        'container',
        'seal',
        'tipe',
        'sangu',
        'simpanan',
        'tagihan',
        'kuli',
        'sj_kembali',
        'sj_kembali_fa',
        'keterangan',
        'ambil_empty_tambak_langon',
        'ambil_empty_teluk_langon',
        'bongkar_full_teluk_langon',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class,'order_id');
    }

    public function customer()
    {
        return $this->belongsTo(CustomerTrucking::class,'customer_id');
    }

    public function sopir()
    {
        return $this->belongsTo(Sopir::class,'sopir_id');
    }

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class,'kendaraan_id');
    }

    public function tarif()
    {
        return $this->belongsTo(TarifTrucking::class,'tarif_id');
    }
}
