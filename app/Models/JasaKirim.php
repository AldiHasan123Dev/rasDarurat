<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class JasaKirim extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jasa_kirim';
    protected $fillable = [
        'jadwal_kapal_id',
        'agen_id',
        'lokasi_id',
        'no_dooring',
        'barcode',
        'tgl_kirim',
        'tgl_terima',
        'nominal',
        'ekspedisi',
        'no',
        'created_by',
        'updated_by',
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

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class);
    }

    public function jadwal_kapal()
    {
        return $this->belongsTo(JadwalKapal::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class,'jasa_kirim_id');
    }
}
