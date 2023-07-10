<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class TarifAgen extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tarif_agen';
    protected $fillable = [
        'agen_id',
        'pembayar_id',
        'penerima_id',
        'tanggal',
        'dari',
        'tujuan',
        'tipe',
        'tarif',
        'kubikasi',
        'keterangan',
        'is_active',
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

    public function penerima()
    {
        return $this->belongsTo(Customer::class,'penerima_id');
    }

    public function pembayar()
    {
        return $this->belongsTo(Customer::class,'pembayar_id');
    }

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
