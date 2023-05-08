<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class LSS extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lss';
    protected $fillable = [
        'lokasi_id',
        'cont_20',
        'cont_40',
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

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class);
    }
}
