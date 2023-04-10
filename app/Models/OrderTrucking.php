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
        'invoice',
        'tgl_invoice',
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
        'simpanan_kuli',
        'borongan_kuli',
        'op',
        'cleaning',
        'stappel',
        'pph_21',
        'pph_23',
        'borongan',
        'tambah_isi',
        'tambah_solar',
        'tb_tl',
        'tally',
        'lain_lain',
        'uang_makan',
        'margin',
        'total_invoice',
        'total_sopir',
        'tgl_total',
        'sj_kembali',
        'sj_kembali_fa',
        'keterangan',
        'ambil_empty_tambak_langon',
        'ambil_empty_teluk_langon',
        'bongkar_full_teluk_langon',
        'created_at',
        'tgl_muat',
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
