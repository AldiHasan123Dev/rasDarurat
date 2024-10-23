<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'setting';
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'fax',
        'logo',
        'type_job_year',
        'short_name',
        'ppn',
        'pph',
        'invoice_name',
        'bank',
        'bank_name',
        'no_rek',
    ];
}
