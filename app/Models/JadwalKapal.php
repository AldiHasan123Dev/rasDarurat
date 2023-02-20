<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JadwalKapal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jadwal_kapal';
    protected $fillable = [
        'kapal_id',
        'voyage',
        'pelayaran_id',
        'rute',
        'closing',
        'etd',
        'td',
        'ba_kirim',
        'keterangan',
        'is_active',
    ];

    public function kapal()
    {
        return $this->belongsTo(Kapal::class,'kapal_id');
    }

    public function pelayaran()
    {
        return $this->belongsTo(Pelayaran::class,'pelayaran_id');
    }
}
