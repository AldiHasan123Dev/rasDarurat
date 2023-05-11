<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jasa_kirim', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_kapal_id')->constrained('lokasi');
            $table->foreignId('lokasi_id')->constrained('lokasi');
            $table->string('no_dooring');
            $table->string('barcode')->nullable();
            $table->date('tgl_kirim')->nullable();
            $table->date('tgl_terima')->nullable();
            $table->integer('nominal')->nullable();
            $table->string('ekspedisi')->nullable();
            $table->integer('no');
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jasa_kirim');
    }
};
