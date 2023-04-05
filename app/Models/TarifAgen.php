<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TarifAgen extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tarif_agen';
    protected $fillable = [
        'agen_id',
        'tanggal',
        'dari',
        'tujuan',
        'tipe',
        'tarif',
        'kubikasi',
        'keterangan',
        'is_active',
    ];

    public function agen()
    {
        return $this->belongsTo(Agen::class,'agen_id');
    }

    public function dariInfo()
    {
        return $this->belongsTo(Lokasi::class,'dari');
    }

    public function tujuanInfo()
    {
        return $this->belongsTo(Lokasi::class,'tujuan');
    }

    public function shipment()
    {
        return $this->belongsTo(Shipment::class,'tipe');
    }
}
