<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LapPelayaranSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil id referensi dari tabel lain
        $tujuans = DB::table('lokasi')->pluck('id')->toArray();
        $pelayaranIds = DB::table('pelayaran')->pluck('id')->toArray();
        $jadwalKapalIds = DB::table('jadwal_kapal')->pluck('id')->toArray();
        $kondisiIds = DB::table('kondisi')->pluck('id')->toArray();
        $shipmentsIds = DB::table('shipments')->pluck('id')->toArray();

        // Validasi agar ada data referensi
        if (empty($tujuans) || empty($pelayaranIds) || empty($jadwalKapalIds)) {
            $this->command->warn('Pastikan tabel lokasi, pelayaran, dan jadwal_kapal sudah memiliki data terlebih dahulu.');
            return;
        }

        // Buat 10 record dengan data acak
        foreach (range(1, 10) as $i) {
            DB::table('lap_pelayaran')->insert([
                'tujuan' => $tujuans[array_rand($tujuans)],
                'comodity' => 'Komoditas ' . $i,
                'sales' => 'Sales ' . $i,
                'shipments' => $shipmentsIds[array_rand($shipmentsIds)],
                'kondisi' => $kondisiIds[array_rand($kondisiIds)],
                'pelayaran_id' => $pelayaranIds[array_rand($pelayaranIds)],
                'keterangan' => 'Ini adalah keterangan lap pelayaran ke-' . $i,
                'jadwal_kapal_id' => $jadwalKapalIds[array_rand($jadwalKapalIds)],
                'tgl_info' => Carbon::now()->subDays(rand(0, 30))->format('Y-m-d'),
                'status' => rand(0, 1) ? 'aktif' : 'non aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Ambil 1 pelayaran dan lokasi yang sama untuk 5 data berikutnya
        $samePelayaranId = $pelayaranIds[array_rand($pelayaranIds)];
        $sameLokasiId = $tujuans[array_rand($tujuans)];

        // Buat 5 data lagi dengan pelayaran, lokasi, dan shipment yang sama
        foreach (range(11, 15) as $i) {
            DB::table('lap_pelayaran')->insert([
                'tujuan' => $sameLokasiId,
                'comodity' => 'Komoditas ' . $i,
                'sales' => 'Sales ' . $i,
                'shipments' => $shipmentsIds[array_rand($shipmentsIds)],
                'kondisi' => $kondisiIds[array_rand($kondisiIds)],
                'pelayaran_id' => $samePelayaranId,
                'keterangan' => 'Data pelayaran dan lokasi sama ke-' . $i,
                'jadwal_kapal_id' => $jadwalKapalIds[array_rand($jadwalKapalIds)],
                'tgl_info' => Carbon::now()->subDays(rand(0, 30))->format('Y-m-d'),
                'status' => rand(0, 1) ? 'aktif' : 'non aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Seeder lap_pelayaran selesai: 15 record ditambahkan.');
    }
}
