<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class SanguSopir extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sangu_sopir';
    protected $fillable = [
        'tujuan',
        'ukuran_20',
        'ukuran_40',
        'ukuran_combo',
        'borongan_kuli_20',
        'borongan_kuli_40',
        'borongan_kuli_combo',
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

    public function tujuanInfo()
    {
        return $this->belongsTo(Lokasi::class,'tujuan');
    }

}
