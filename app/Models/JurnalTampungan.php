<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class JurnalTampungan extends Model
{
    use HasFactory;

    protected $table = 'jurnal_tampungan';
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
        );
    }
}
