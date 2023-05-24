<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemplateJurnalItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'template_jurnal_item';
    protected $fillable = [
        'template_jurnal_id',
        'coa_id',
        'tipe',
        'no',
        'deskripsi',
        'jumlah',
    ];
}
