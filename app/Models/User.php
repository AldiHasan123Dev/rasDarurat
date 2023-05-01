<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role_id',
        'name',
        'email',
        'phone',
        'address',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
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

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function cs()
    {
        return $this->hasMany(Customer::class,'cs_id');
    }

    public function marketing()
    {
        return $this->hasMany(Customer::class,'marketing_id');
    }

    

    public function laporanCs20Fit($bulan, $thn = 2023)
    {
        $order = Order::join('jadwal_kapal','jadwal_kapal.id','=','order.jadwal_kapal_id')
                    ->join('tarif','tarif.id','=','order.tarif_id')
                    ->join('customers','customers.id','=','tarif.customer_id')
                    ->leftJoin('users','users.id','=','customers.marketing_id')
                    ->leftJoin('users as cs','cs.id','=','customers.cs_id')
                    ->join('shipments','shipments.id','=','tarif.shipment')
                    ->where('shipments.nama','LIKE','%2%')
                    // ->orWhere('customers.marketing_id',$this->id)
                    ->where('customers.cs_id',$this->id)
                    ->whereMonth('order.created_at',sprintf('%02d',$bulan))
                    ->whereYear('order.created_at',$thn)
                    ->select('order.id')
                    ->count();
        return $order;
    }
    public function laporanCs40Fit($bulan, $thn = 2023)
    {
        $order = Order::join('jadwal_kapal','jadwal_kapal.id','=','order.jadwal_kapal_id')
                    ->join('tarif','tarif.id','=','order.tarif_id')
                    ->join('customers','customers.id','=','tarif.customer_id')
                    ->leftJoin('users','users.id','=','customers.marketing_id')
                    ->leftJoin('users as cs','cs.id','=','customers.cs_id')
                    ->join('shipments','shipments.id','=','tarif.shipment')
                    ->where('shipments.nama','LIKE','%4%')
                    // ->orWhere('customers.marketing_id',$this->id)
                    ->where('customers.cs_id',$this->id)
                    ->whereMonth('order.created_at',sprintf('%02d',$bulan))
                    ->whereYear('order.created_at',$thn)
                    ->select('order.id')
                    ->count();
        return $order;
    }
    public function laporanMarketing20Fit($bulan, $thn = 2023)
    {
        $order = Order::join('jadwal_kapal','jadwal_kapal.id','=','order.jadwal_kapal_id')
                    ->join('tarif','tarif.id','=','order.tarif_id')
                    ->join('customers','customers.id','=','tarif.customer_id')
                    ->leftJoin('users','users.id','=','customers.marketing_id')
                    ->leftJoin('users as cs','cs.id','=','customers.cs_id')
                    ->join('shipments','shipments.id','=','tarif.shipment')
                    ->where('shipments.nama','LIKE','%2%')
                    ->where('customers.marketing_id',$this->id)
                    // ->where('customers.cs_id',$this->id)
                    ->whereMonth('order.created_at',sprintf('%02d',$bulan))
                    ->whereYear('order.created_at',$thn)
                    ->select('order.id')
                    ->count();
        return $order;
    }
    public function laporanMarketing40Fit($bulan, $thn = 2023)
    {
        $order = Order::join('jadwal_kapal','jadwal_kapal.id','=','order.jadwal_kapal_id')
                    ->join('tarif','tarif.id','=','order.tarif_id')
                    ->join('customers','customers.id','=','tarif.customer_id')
                    ->leftJoin('users','users.id','=','customers.marketing_id')
                    ->leftJoin('users as cs','cs.id','=','customers.cs_id')
                    ->join('shipments','shipments.id','=','tarif.shipment')
                    ->where('shipments.nama','LIKE','%4%')
                    ->where('customers.marketing_id',$this->id)
                    // ->where('customers.cs_id',$this->id)
                    ->whereMonth('order.created_at',sprintf('%02d',$bulan))
                    ->whereYear('order.created_at',$thn)
                    ->select('order.id')
                    ->count();
        return $order;
    }
}
