<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TarifPelayaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tarif_pelayaran';
    protected $fillable = [
        'pelayaran_id',
        'tanggal',
        'dari',
        'tujuan',
        'tipe',
        'tarif',
        'kubikasi',
        'keterangan',
        'is_active',
    ];

    public function pelayaran()
    {
        return $this->belongsTo(Pelayaran::class,'pelayaran_id');
    }
}
