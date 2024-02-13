<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class JasaKirim extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jasa_kirim';
    protected $fillable = [
        'merger',
        'jadwal_kapal_id',
        'invoice',
        'tgl_invoice',
        'jurnal',
        'agen_id',
        'lokasi_id',
        'no_dooring',
        'barcode',
        'tgl_kirim',
        'tgl_terima',
        'nominal',
        'ekspedisi',
        'no',
        'no_draf',
        'status',
        'created_by',
        'updated_by',
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

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class);
    }

    public function jadwal_kapal()
    {
        return $this->belongsTo(JadwalKapal::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class,'jasa_kirim_id');
    }

    public function agen()
    {
        return $this->belongsTo(Agen::class);
    }

    public function kirim_dokumen()
    {
        return $this->hasMany(KirimDokumen::class);
    }

    public function order_name(){
        $name = '';
        foreach ($this->orders as $item ) {
            $name .= $item->job.'-'.sprintf('%02d',$item->no_job).'; ';
        }
        foreach ($this->kirim_dokumen as $item) {
            $name .= $item->nama.'; ';
        }
        return $name;
    }

    public function split_nominal()
    {
        $count = $this->orders->count() + $this->kirim_dokumen->count();
        return $this->nominal / $count;
    }
}
