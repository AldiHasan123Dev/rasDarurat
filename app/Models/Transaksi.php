<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Transaksi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transaksi';
    protected $fillable = [
        'jurnal_piutang',
        'jurnal_bupot',
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
        'no_bupot',
        'masa_bupot',
        'tanggal_bupot',
        'selisih_bupot',
        'bupot',
        'job',
        'order',
        'tanggal_kirim',
        'created_at',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->created_by = Auth::id();
            $model->updated_by = Auth::id();
        });
        static::saving(function ($model) {
            $model->updated_by = Auth::id();
        });
    }

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

    public function orderInfo()
    {
        return $this->belongsTo(Order::class,'order_id');
    }

    public function jurnal_piutang()
    {
        $arr = array_unique($this->jobs()->pluck('jurnal_piutang')->toArray());
        return implode('; ',$arr);
    }
}
