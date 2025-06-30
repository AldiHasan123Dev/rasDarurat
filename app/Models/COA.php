<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class COA extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'coa';
    protected $fillable = [
        'id',
        'coa_id',
        'coa_ras',
        'kode',
        'no_kode',
        'nama',
        'kategori',
        'is_cont',
        'is_nopol',
        'is_nojob',
        'is_invoice',
        'is_invoice_trucking',
        'is_nobg',
        'is_nobupot',
        'is_tglbupot',
        'keterangan',
        'is_active',
        'is_invoice_agen',
        'is_invoice_vendor',
        'is_invoice_external',
    ];

    public function coa()
    {
        return $this->belongsTo(COA::class);
    }

    public function coas()
    {
        return $this->hasMany(COA::class,'coa_id');
    }

    public function jurnals()
    {
        return $this->hasMany(Jurnal::class,'coa_id');
    }
}
