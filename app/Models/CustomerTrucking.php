<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerTrucking extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customer_trucking';
    protected $fillable = [
        'pph_23',
        'nama',
        'pic',
        'alamat',
        'hp',
        'nik',
        'npwp',
        'nama_npwp',
        'alamat_npwp',
    ];
}
