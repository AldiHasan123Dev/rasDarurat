<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class TarifTrucking extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tarif_trucking';
    protected $fillable = [
        'customer_id',
        'tujuan_id',
        'tipe',
        'tarif',
        'is_active',
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

    public function customer()
    {
        return $this->belongsTo(CustomerTrucking::class,'customer_id');
    }

    public function tujuan()
    {
        return $this->belongsTo(SanguSopir::class,'tujuan_id');
    }
}
