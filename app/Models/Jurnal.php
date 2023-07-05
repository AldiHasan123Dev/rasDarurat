<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Jurnal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jurnal';
    protected $fillable = [
        'coa_id',
        'order_id',
        'jurnal_balik',
        'nomor',
        'nama',
        'debit',
        'credit',
        'tipe',
        'no',
        'is_balik',
        'created_at',
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

    public function coa()
    {
        return $this->belongsTo(COA::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
