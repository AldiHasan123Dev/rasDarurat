<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class TarifPelayaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tarif_pelayaran';
    protected $fillable = [
        'customer_id',
        'pelayaran_id',
        'port_id',
        'tanggal',
        'dari',
        'tujuan',
        'tipe',
        'tarif',
        'kubikasi',
        'komoditi',
        'keterangan',
        'is_active',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

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

    public function pelayaran()
    {
        return $this->belongsTo(Pelayaran::class,'pelayaran_id');
    }

    public function port()
    {
        return $this->belongsTo(Port::class,'port_id');
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
