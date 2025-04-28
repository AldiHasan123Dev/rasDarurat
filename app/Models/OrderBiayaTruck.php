<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class OrderBiayaTruck extends Model
{
    use HasFactory;

    protected $table = 'order_biaya_truck';
    protected $fillable = [
        'order_trucking_id',
        'nominal_sangu_kuli1',
        'nominal_sangu_kuli2',
        'nominal_sangu_kuli3',
        'tgl_sangu_kuli1',
        'tgl_sangu_kuli2',
        'tgl_sangu_kuli3',
        'nominal_tb_tl1',
        'nominal_tb_tl2',
        'tgl_tb_tl',
        'tgl_tb_tl1',
        'nominal_stappel1',
        'tgl_stappel',
    ];

    public function orderTruck()
    {
        return $this->belongsTo(OrderTrucking::class,'order_trucking_id');
    }
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
