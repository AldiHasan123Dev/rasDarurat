<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'nik',
        'nama_npwp',
        'alamat_npwp',
        'npwp',
        'nama',
        'marketing_id',
        'cs_id',
        'pic',
        'alamat',
        'kota',
        'telp',
        'hp',
        'fax',
        'email',
        'tipe',
        'no_bl',
        'top',
        'all_in',
        'ba_kembali',
    ];

    public function marketing()
    {
        return $this->belongsTo(User::class,'marketing_id');
    }

    public function cs()
    {
        return $this->belongsTo(User::class,'cs_id');
    }
}
