<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LapPelayaran extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $table = 'lap_pelayaran';

    protected $fillable = [
        'tujuan',            // foreign key ke tabel lokasi
        'comodity',
        'sales',
        'pelayaran_id',      // foreign key ke tabel pelayaran
        'keterangan',
        'jadwal_kapal_id',   // foreign key ke tabel jadwal kapal
        'tgl_info',
        'status',
        'shipments',
        'harga',
        'kondisi',
    ];

    // Relasi ke tabel Lokasi
   public function lokasi()
{
    return $this->belongsTo(Lokasi::class, 'tujuan');
}

public function shipment()
{
    return $this->belongsTo(Shipment::class, 'shipments');
}

public function kondisi1()
{
    return $this->belongsTo(Kondisi::class, 'kondisi');
}

public function pelayaran()
{
    return $this->belongsTo(Pelayaran::class, 'pelayaran_id');
}

public function jadwalKapal()
{
    return $this->belongsTo(JadwalKapal::class, 'jadwal_kapal_id');
}

}
