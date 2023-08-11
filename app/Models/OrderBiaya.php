<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class OrderBiaya extends Model
{
    use HasFactory;

    protected $table = 'order_biaya';
    protected $fillable = [
        'order_id',
        'tgl_dcf',
        'tgl_opt',
        'tgl_truk',
        'tgl_kuli',
        'nominal_do',
        'nominal_cleaning',
        'nominal_fee',
        'nominal_opt',
        'nominal_truk',
        'nominal_kuli',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
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
