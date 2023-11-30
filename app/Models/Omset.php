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
        'j_opp',
        'j_opt',
        'j_ut',
        'j_bl',
        'j_apbs',
        'j_cleaning',
        'j_lss',
        'j_storage',
        'j_jasa_door',
        'j_asuransi',
        'j_ops',
        'j_segel',
        'j_buruh',
        'j_checker',
        'j_karantina',
        'j_demmurage',
        'j_kirim_dokumen',
        'j_biaya_lain',
        'j_flexibag',
        'j_rc',
        'j_biaya',
        'j_biaya_lain',
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

