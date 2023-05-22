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
        'kode',
        'nama',
        'keterangan',
        'is_active',
    ];

    public function coa()
    {
        return $this->belongsTo(COA::class);
    }
}
