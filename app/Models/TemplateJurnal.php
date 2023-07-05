<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemplateJurnal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'template_jurnal';
    protected $fillable = [
        'nama',
        'tipe',
    ];

    public function template_items()
    {
        return $this->hasMany(TemplateJurnalItem::class,'template_jurnal_id');
    }
}
