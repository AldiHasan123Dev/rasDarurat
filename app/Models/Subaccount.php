<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subaccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'account_id',
        'kode',
        'nama',
        'keterangan',
        'is_active',
    ];
}
