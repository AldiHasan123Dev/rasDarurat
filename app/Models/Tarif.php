<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tarif extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tarif';
    protected $fillable = [
        'jadwal_kapal_id',
        'dari',
        'tujuan',
        'shipment',
        'kondisi',
        'satuan',
        'keterangan',
        'unit',
        'min_qty',
        'customer_id',
        'is_active',
    ];

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
}
