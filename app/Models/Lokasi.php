<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Lokasi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lokasi';
    protected $fillable = [
        'nama',
        'publis_rate',
        'diskon',
        'harga',
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

    

    public function laporan20Fit($bulan, $thn = 2023, $port_id = null)
{
    $job = $thn . sprintf('%02d', $bulan) . '%';

    $order = Order::join('jadwal_kapal', 'jadwal_kapal.id', '=', 'order.jadwal_kapal_id')
        ->join('tarif', 'tarif.id', '=', 'order.tarif_id')
        ->join('shipments', 'shipments.id', '=', 'tarif.shipment')
        ->where('shipments.nama', 'LIKE', '%2%')
         ->when($port_id, function ($query) use ($port_id) {
            $query->where('order.port_id', $port_id);
        })
        ->where('tarif.tujuan',$this->id)
        ->where('order.job', 'LIKE', $job)
        ->count();

    return $order;
}

public function laporan40Fit($bulan, $thn = 2023, $port_id = null)
{
    $job = $thn . sprintf('%02d', $bulan) . '%';

    $order = Order::join('jadwal_kapal', 'jadwal_kapal.id', '=', 'order.jadwal_kapal_id')
        ->join('tarif', 'tarif.id', '=', 'order.tarif_id')
        ->join('shipments', 'shipments.id', '=', 'tarif.shipment')
        ->where('shipments.nama', 'LIKE', '%4%')
        ->when($port_id, function ($query) use ($port_id) {
            $query->where('order.port_id', $port_id);
        })
        ->where('tarif.tujuan',$this->id)
        ->where('order.job', 'LIKE', $job)
        ->count();

    return $order;
}

}
