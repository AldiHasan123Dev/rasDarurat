<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lokasi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lokasi';
    protected $fillable = [
        'nama',
    ];

    public function laporan20Fit($bulan, $thn = 2023)
    {
        $order = Order::join('jadwal_kapal','jadwal_kapal.id','=','order.jadwal_kapal_id')
                    ->join('tarif','tarif.id','=','order.tarif_id')
                    ->where('tarif.shipment',[1,7,9,11])
                    ->where('tarif.tujuan',$this->id)
                    ->whereMonth('order.created_at',sprintf('%02d',$bulan))
                    ->whereYear('order.created_at',$thn)
                    ->select('order.id')
                    ->count();
        return $order;
    }
    public function laporan40Fit($bulan, $thn = 2023)
    {
        $order = Order::join('jadwal_kapal','jadwal_kapal.id','=','order.jadwal_kapal_id')
                    ->join('tarif','tarif.id','=','order.tarif_id')
                    ->where('tarif.shipment',[8,10,12])
                    ->where('tarif.tujuan',$this->id)
                    ->whereMonth('order.created_at',sprintf('%02d',$bulan))
                    ->whereYear('order.created_at',$thn)
                    ->select('order.id')
                    ->count();
        return $order;
    }
}
