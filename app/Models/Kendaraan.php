<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Kendaraan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kendaraan';
    protected $fillable = [
        'pkb',
        'masa_pkb',
        'kir',
        'stid',
        'warna',
        'tahun',
        'no_rangka',
        'no_mesin',
        'tipe',
        'nopol',
        'milik',
        'is_active',
        'keterangan',
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

    public function orders()
    {
        return $this->hasMany(OrderTrucking::class,'kendaraan_id');
    }

    public function laporanRit($bulan, $thn = 2023){
        return $this->orders()->whereMonth('tgl_muat',sprintf('%02d',$bulan))->whereYear('tgl_muat',$thn)->count();
    }

    public function laporanMargin($bulan, $thn = 2023){
        return $this->orders()->whereMonth('tgl_muat',sprintf('%02d',$bulan))->whereYear('tgl_muat',$thn)->sum('margin');
    }
}
