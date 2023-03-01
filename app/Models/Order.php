<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'order';
    protected $fillable = [
        'job',
        'invoice',
        'no_job',
        'tarif_id',
        'pengirim_id',
        'penerima_id',
        'barang_id',
        'ba_kirim',
        'stuffing',
        'full',
        'barang_diantar',
        'ba_kembali',
        'resi',
        'trucking',
        'nopol',
        'container',
        'seal',
        'keterangan',
        'no_bl',
    ];

    public function tarif()
    {
        return $this->belongsTo(Tarif::class);
    }

    public function pengirim()
    {
        return $this->belongsTo(Customer::class,'pengirim_id');
    }

    public function penerima()
    {
        return $this->belongsTo(Customer::class,'penerima_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class,'barang_id');
    }

    public function bttb()
    {
        return $this->hasMany(BTTB::class,'order_id');
    }
}
