<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class OrderTrucking extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'order_trucking';
    protected $fillable = [
        'jurnal_piutang',
        'jurnal_hutang',
        'order_id',
        'invoice',
        'tgl_invoice',
        'customer_id',
        'sopir_id',
        'tarif_id',
        'tarif_nominal',
        'kendaraan_id',
        'dari',
        'tujuan',
        'container',
        'seal',
        'tipe',
        'tarif_vendor',
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
        'lain',
        'uang_makan',
        'margin',
        'total_invoice',
        'total_sopir',
        'invoice_sopir',
        'order_sopir',
        'tgl_total',
        'sj_kembali',
        'sj_kembali_fa',
        'keterangan',
        'keterangan_lain',
        'ambil_empty_tambak_langon',
        'ambil_empty_teluk_langon',
        'bongkar_full_teluk_langon',
        'created_at',
        'tgl_muat',
        'is_seal',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->created_by = Auth::id();
            $model->updated_by = Auth::id();
        });
        static::saving(function ($model) {
            $model->updated_by = Auth::id();
        });
    }

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

    public function tagihans()
    {
        return $this->hasMany(TagihanTrucking::class,'order_id');
    }

    public function jurnals()
    {
        return $this->hasMany(Jurnal::class,'order_trucking_id');
    }
}
