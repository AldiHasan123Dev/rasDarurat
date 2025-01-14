<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class JurnalSample extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jurnal_sample';
    protected $fillable = [
        'coa_id',
        'order_id',
        'order_trucking_id',
        'jurnal_balik',
        'nomor',
        'nama',
        'invoice',
        'invoice_external',
        'nopol',
        'container',
        'debit',
        'credit',
        'tipe',
        'no',
        'is_balik',
        'created_at',
        'input',
        'no_bg',
        'tgl_bg',
        'nominal_bg',
        'invoice_trucking',
        'invoice_agen',
        'relasi',
        'invoice_vendor',
    ];

    protected $searchable = [
        'nomor',
        'invoice',
        'nopol',
        'container',
        'nama',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->created_by = Auth::id();
            $model->input = date('Y-m-d H:i:s');
            $model->updated_by = Auth::id();
        });
        static::saving(function ($model) {
            $model->updated_by = Auth::id();
        });
    }

    public function coa()
    {
        return $this->belongsTo(COA::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function order_trucking()
    {
        return $this->belongsTo(OrderTrucking::class);
    }

    public function is_balance()
    {
        $debit = Jurnal::where('nomor',$this->nomor)->sum('debit');
        $credit = Jurnal::where('nomor',$this->nomor)->sum('credit');
        if($debit!=$credit){
            return false;
        }else{
            return true;
        }
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(
            fn ($query) => $query->where('nomor', 'like', '%'.$term.'%')
                ->orWhere('invoice', 'like', '%'.$term.'%')
                ->orWhere('nopol', 'like', '%'.$term.'%')
                ->orWhere('container', 'like', '%'.$term.'%')
                ->orWhere('nama', 'like', '%'.$term.'%')
                ->orWhereHas('order', function($q) use($term){
                    $q->where('job','like','%'.$term.'%');
                })
                ->orWhereHas('coa', function($q) use($term){
                    $q->where('kode','like','%'.$term.'%');
                })
        );
    }

    public function jurnal_balik_data()
    {
        return $this->hasMany(Jurnal::class,'jurnal_balik');
    }

    public function bg()
    {
        $data = HutangPelayaran::orWhere('no_bg_opp', $this->no_bg)
                ->orWhere('no_bg_opt', $this->no_bg)
                ->orWhere('no_bg_ut', $this->no_bg)
                ->pluck('order_id')
                ->toArray();
        $order_id = array_unique($data);
        $res = [];
        $data = Order::whereIn('id',$order_id)->get();
        foreach ($data as $item) {
            array_push($res,$item->job.'-'.sprintf('%02d',$item->no_job));
        }
        return $res;
    }

    public function bg_pelayaran()
    {
        $data = HutangPelayaran::orWhere('no_bg_opp', $this->no_bg)
                ->orWhere('no_bg_opt', $this->no_bg)
                ->orWhere('no_bg_ut', $this->no_bg)
                ->first()
                ->pelayaran
                ->nama;
        return $data;
    }

    public function bg_tgl()
    {
        if($this->no_bg){
            $hp = HutangPelayaran::where('no_bg_opp',$this->no_bg)->first();
            if($hp){
                return $hp->tgl_bg_opp;
            }else{
                $hp = HutangPelayaran::where('no_bg_opt',$this->no_bg)->first();
                if($hp){
                    return $hp->tgl_bg_opt;
                }else{
                    $hp = HutangPelayaran::where('no_bg_ut',$this->no_bg)->first();
                    if($hp){
                        return $hp->tgl_bg_ut;
                    }else{
                        return '-';
                    }
                }
            }
        }
        return '-';
    }

    public function transaksi()
    {
        $trx = Transaksi::where('invoice',$this->invoice)->first();
        if(!$trx){
            $trx = TransaksiTrucking::where('invoice',$this->invoice)->first();
        }
        return $trx;
    }
}
