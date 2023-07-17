<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'order';
    protected $fillable = [
        'jurnal_piutang',
        'jadwal_kapal_id',
        'no',
        'job',
        'invoice',
        'invoice_bayar',
        'invoice_date',
        'nsfp',
        'no_job',
        'tarif_id',
        'pengirim_id',
        'penerima_id',
        'penerima_bl_id',
        'barang_id',
        'komisi',
        'komisi_print',
        'ba_kirim',
        'stuffing',
        'stuffing_type',
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
        'tipe_asuransi',
        'asuransi',
        'asuransi_id',
        'pertanggungan',
        'agen_id',
        'agen',
        'satuan',
        'asuransi_date',
        'asuransi_cetak',
        'tgl_komisi',
        'created_at',
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

    public function penerima_bl()
    {
        return $this->belongsTo(Customer::class,'penerima_bl_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class,'barang_id');
    }

    public function bttb()
    {
        return $this->hasMany(BTTB::class,'order_id');
    }

    public function jadwal_kapal()
    {
        return $this->belongsTo(JadwalKapal::class,'jadwal_kapal_id');
    }

    public function satuanInfo()
    {
        return $this->belongsTo(Satuan::class,'satuan');
    }

    public function agent()
    {
        return $this->belongsTo(Agen::class,'agen_id');
    }

    public function asuransiInfo()
    {
        return $this->belongsTo(Asuransi::class,'asuransi_id');
    }

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class,'order_id');
    }

    public function transaksi()
    {
        return $this->hasOne(Transaksi::class,'job','job');
    }

    public function truckingInfo()
    {
        return $this->hasOne(OrderTrucking::class,'order_id');
    }

    public function jurnals()
    {
        return $this->hasMany(Jurnal::class,'order_id');
    }

    public function hutang_pelayaran()
    {
        return $this->hasOne(HutangPelayaran::class,'order_id');
    }
}
