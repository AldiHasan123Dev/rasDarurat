<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaksi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transaksi';
    protected $fillable = [
        'pembayar_id',
        'order_id',
        'tipe_invoice',
        'invoice',
        'nsfp',
        'keterangan',
        'tujuan',
        'sub_total',
        'tagihan',
        'ppn',
        'asuransi',
        'admin',
        'total',
        'pph',
        'job',
        'order',
        'tanggal_kirim',
        'created_at',
    ];

    public function pembayar()
    {
        return $this->belongsTo(Customer::class,'pembayar_id');
    }

    public function no_job()
    {
        $orders = Order::where('job',$this->job)->get();
        $job = '';
        foreach ($orders as $item ) {
            $job .= $item->job.'-'.sprintf('%02d',$item->no_job).'; ';
        }
        return $job;
    }

    public function jobs()
    {
        return $this->hasMany(Order::class,'job','job');
    }
}
