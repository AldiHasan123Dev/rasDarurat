<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Tarif extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tarif';
    protected $fillable = [
        'pelayaran_id',
        'jadwal_kapal_id',
        'dari',
        'tujuan',
        'shipment',
        'kondisi',
        'satuan',
        'keterangan',
        'unit',
        'tarif',
        'min_qty',
        'stuffing',
        'customer_id',
        'is_active',
        'created_by',
        'updated_by',
        'satuan_inv',
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

    public function jadwal_kapal()
    {
        return $this->belongsTo(JadwalKapal::class,'jadwal_kapal_id');
    }

    public function dari_lokasi()
    {
        return $this->belongsTo(Lokasi::class,'dari');
    }

    public function tujuan_lokasi()
    {
        return $this->belongsTo(Lokasi::class,'tujuan');
    }

    public function shipmentInfo()
    {
        return $this->belongsTo(Shipment::class,'shipment');
    }

    public function kondisiInfo()
    {
        return $this->belongsTo(Kondisi::class,'kondisi');
    }

    public function satuanInfo()
    {
        return $this->belongsTo(Satuan::class,'satuan');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class,'customer_id');
    }

    public function pelayaran()
    {
        return $this->belongsTo(Pelayaran::class,'pelayaran_id');
    }
}
