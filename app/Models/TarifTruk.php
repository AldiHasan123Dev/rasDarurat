<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TarifTruk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tarif_truk';
    protected $fillable = [
        'truk_id',
        'tanggal',
        'dari',
        'tujuan',
        'tipe',
        'tarif',
        'kubikasi',
        'keterangan',
        'is_active',
    ];

    public function truk()
    {
        return $this->belongsTo(Truk::class,'truk_id');
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
