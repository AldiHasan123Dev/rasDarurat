<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TarifAgen extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tarif_agen';
    protected $fillable = [
        'agen_id',
        'tanggal',
        'dari',
        'tujuan',
        'tipe',
        'tarif',
        'kubikasi',
        'keterangan',
        'is_active',
    ];

    public function agen()
    {
        return $this->belongsTo(Agen::class,'agen_id');
    }
}
