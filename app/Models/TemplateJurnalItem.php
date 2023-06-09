<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemplateJurnalItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'template_jurnal_id',
        'coa_debit_id',
        'coa_credit_id',
        'keterangan',
    ];
}
