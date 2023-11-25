<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Omset extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'omset';
    protected $fillable = [
        'order_id',
        'opp',
        'opt',
        'ut',
        'bl',
        'apbs',
        'cleaning',
        'lss',
        'storage',
        'jasa_door',
        'asuransi',
        'ops',
        'segel',
        'buruh',
        'checker',
        'karantina',
        'demmurage',
        'kirim_dokumen',
        'biaya_lain',
        'flexibag',
        'rc',
        'biaya',
        'tarif',
        'laba_kotor',
        'margin',
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
}

