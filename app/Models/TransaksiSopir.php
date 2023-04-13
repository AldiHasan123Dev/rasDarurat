<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiSopir extends Model
{
    use HasFactory;
    protected $table = 'transaksi_sopir';
    protected $fillable = [
        'tgl_invoice',
        'invoice',
        'sopir_id',
        'order_id',
        'order_trucking_id',
        'total',
        'order',
        'submited_by',
    ];

    public function sopir()
    {
        return $this->belongsTo(Sopir::class,'sopir_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'submited_by');
    }
}
