<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Sopir extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sopir';
    protected $fillable = [
        'nama',
        'alamat',
        'hp',
        'milik',
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

    public function orders()
    {
        return $this->hasMany(OrderTrucking::class,'sopir_id');
    }

    public function laporanRit($bulan, $thn = 2023){
        return $this->orders()->whereMonth('tgl_muat',sprintf('%02d',$bulan))->whereYear('tgl_muat',$thn)->count();
    }

    public function laporanSangu($bulan, $thn = 2023){
        return $this->orders()->whereMonth('tgl_muat',sprintf('%02d',$bulan))->whereYear('tgl_muat',$thn)->sum('borongan');
    }
}
