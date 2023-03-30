<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    ];

    public function customer()
    {
        return $this->belongsTo(CustomerTrucking::class,'customer_id');
    }

    public function tujuan()
    {
        return $this->belongsTo(SanguSopir::class,'tujuan_id');
    }
}
