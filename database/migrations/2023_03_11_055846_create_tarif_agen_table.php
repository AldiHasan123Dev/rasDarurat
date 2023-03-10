<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarif_agen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agen_id')->constrained('agen');
            $table->date('tanggal');
            $table->foreignId('dari')->constrained('lokasi');
            $table->foreignId('tujuan')->constrained('lokasi');
            $table->string('tipe');
            $table->double('tarif');
            $table->double('kubikasi');
            $table->string('keterangan')->nullable();
            $table->boolean('is_active')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarif_agen');
    }
};
