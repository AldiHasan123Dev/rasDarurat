<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_kapal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kapal_id')->constrained('kapal');
            $table->string('voyage');
            $table->foreignId('pelayaran_id')->constrained('pelayaran');
            $table->string('rute');
            $table->date('closing')->nullable();
            $table->date('etd')->nullable();
            $table->date('td')->nullable();
            $table->date('ba_kirim')->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_kapal');
    }
};
