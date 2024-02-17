<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalBalik extends Model
{
    use HasFactory;

    protected $table = 'jurnal_balik';
    protected $fillable = [
        'tanggal',
        'bulan',
        'tahun',
        'nomor',
        'no',
        'tipe',
    ];
}
