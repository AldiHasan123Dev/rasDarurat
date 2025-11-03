<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

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
        'pph',
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

    public function lapPelayaran()
{
    return $this->hasMany(LapPelayaran::class, 'pelayaran_id');
}


    public function laporan20Fit($bulan, $thn = 2023)
    {
        $job = $thn.sprintf('%02d',$bulan).'%';
        $order = Order::join('jadwal_kapal','jadwal_kapal.id','=','order.jadwal_kapal_id')
                    ->join('tarif','tarif.id','=','order.tarif_id')
                    ->join('shipments','shipments.id','=','tarif.shipment')
                    ->where('shipments.nama','LIKE','%2%')
                    ->where('jadwal_kapal.pelayaran_id',$this->id)
                    ->where('order.job','LIKE',$job)
                    ->select('order.id')
                    ->count();
        return $order;
    }
    public function laporan40Fit($bulan, $thn = 2023)
    {
        $job = $thn.sprintf('%02d',$bulan).'%';
        $order = Order::join('jadwal_kapal','jadwal_kapal.id','=','order.jadwal_kapal_id')
                    ->join('tarif','tarif.id','=','order.tarif_id')
                    ->join('shipments','shipments.id','=','tarif.shipment')
                    ->where('shipments.nama','LIKE','%4%')
                    ->where('jadwal_kapal.pelayaran_id',$this->id)
                    ->where('order.job','LIKE',$job)
                    ->select('order.id')
                    ->count();
        return $order;
    }

    public function hutang_pelayaran()
    {
        return $this->hasMany(HutangPelayaran::class,'pelayaran_id');
    }

    public function bg()
    {
        // dd($this->hutang_pelayaran()->get());
        $bgs = array();
        foreach ($this->hutang_pelayaran()->get() as $bg) {
            if(!is_null($bg->no_bg_opp)){
                array_push($bgs,$bg->no_bg_opp);
            }
            if(!is_null($bg->no_bg_opt)){
                array_push($bgs,$bg->no_bg_opt);
            }
            if(!is_null($bg->no_bg_ut)){
                array_push($bgs,$bg->no_bg_ut);
            }
        }
        $bgs = array_unique($bgs);
        return $bgs;
    }

    public function jurnals($month,$year,$coa_id)
    {
        $c = new Carbon($year.'-'.sprintf('%02d',$month).'-01');
        $now = $c->startOfMonth()->format('Y-m-d');
        $last = $c->endOfMonth()->format('Y-m-d');
        $start = '2022-12-01';
        $query = Jurnal::query();
        $query->join('coa','coa.id','=','jurnal.coa_id');
        $query->select('jurnal.*');
        $query->where('jurnal.coa_id',$coa_id);
        $query->whereIn('jurnal.no_bg',$this->bg());
        $query->whereBetween('jurnal.created_at',[$start,$last]);
        $query->orderBy('created_at');
        $jurnals = $query->get();
        // dd($this->bg());
        return $jurnals;
    }
}
