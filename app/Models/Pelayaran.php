<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pelayaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pelayaran';
    protected $fillable = [
        'kode',
        'nama',
        'pic',
        'alamat',
        'kota',
        'telp',
        'fax',
        'email',
        'hp',
    ];

    public function laporan20Fit($bulan, $thn = 2023)
    {
        $order = Order::join('jadwal_kapal','jadwal_kapal.id','=','order.jadwal_kapal_id')
                    ->join('tarif','tarif.id','=','order.tarif_id')
                    ->join('shipments','shipments.id','=','tarif.shipment')
                    ->where('shipments.nama','LIKE','%2%')
                    ->where('jadwal_kapal.pelayaran_id',$this->id)
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
                    ->join('shipments','shipments.id','=','tarif.shipment')
                    ->where('shipments.nama','LIKE','%4%')
                    ->where('jadwal_kapal.pelayaran_id',$this->id)
                    ->whereMonth('order.created_at',sprintf('%02d',$bulan))
                    ->whereYear('order.created_at',$thn)
                    ->select('order.id')
                    ->count();
        return $order;
    }
}
