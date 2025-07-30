<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

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
        'eta',
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

    public function lapPelayaran() {
        return $this->hasMany(LapPelayaran::class, 'jadwal_kapal_id');
    }

    public function hasInvoice()
    {
        $count = Order::where('jadwal_kapal_id', $this->id)->whereNotNull('invoice')->count();
        if($count==0){
            return false;
        }

        return true;
    }

    public function kapal()
    {
        return $this->belongsTo(Kapal::class,'kapal_id');
    }

    public function pelayaran()
    {
        return $this->belongsTo(Pelayaran::class,'pelayaran_id');
    }
}
