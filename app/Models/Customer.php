<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'nik',
        'nama_npwp',
        'alamat_npwp',
        'npwp',
        'nama',
        'marketing_id',
        'cs_id',
        'pic',
        'alamat',
        'kota',
        'telp',
        'hp',
        'fax',
        'email',
        'tipe',
        'no_bl',
        'top',
        'all_in',
        'ba_kembali',
    ];

    public function marketing()
    {
        return $this->belongsTo(User::class,'marketing_id');
    }

    public function cs()
    {
        return $this->belongsTo(User::class,'cs_id');
    }

    public function laporan20Fit($bulan, $thn = 2023)
    {
        $order = Order::join('jadwal_kapal','jadwal_kapal.id','=','order.jadwal_kapal_id')
                    ->join('tarif','tarif.id','=','order.tarif_id')
                    ->join('shipments','shipments.id','=','tarif.shipment')
                    ->where('shipments.nama','LIKE','%2%')
                    ->where('tarif.customer_id',$this->id)
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
                    ->where('tarif.customer_id',$this->id)
                    ->whereMonth('order.created_at',sprintf('%02d',$bulan))
                    ->whereYear('order.created_at',$thn)
                    ->select('order.id')
                    ->count();
        return $order;
    }

    public function tarif()
    {
        return $this->hasMany(Tarif::class,'customer_id');
    }
}
