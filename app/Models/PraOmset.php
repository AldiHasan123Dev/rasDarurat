<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class PraOmset extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'pra_omset';
    protected $fillable = [
        'order_id',
        'trucking',
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
        'ops_seal',
        'ops_seal_cleaning',
        'buruh',
        'checker',
        'karantina',
        'demmurage',
        'job_slip_pod',
        'lolo_pod',
        'cleaning_pod',
        'ops_pod',
        'opt_pod',
        'truck_pod',
        'kuli_pod',
        'storage_pod',
        'kirim_dokumen',
        'biaya_lain',
        'flexibag',
        'rc',
        'biaya',
        'none',
        'j_none',
        'j_trucking',
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
        'j_ops_seal',
        'j_ops_seal_cleaning',
        'j_buruh',
        'j_checker',
        'j_karantina',
        'j_demmurage',
        'j_job_slip_pod',
        'j_lolo_pod',
        'j_cleaning_pod',
        'j_ops_pod',
        'j_opt_pod',
        'j_truck_pod',
        'j_kuli_pod',
        'j_storage_pod',
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
