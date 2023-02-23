<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarif', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('jadwal_kapal_id')->constrained('jadwal_kapal');
            $table->foreignId('dari')->constrained('lokasi');
            $table->foreignId('tujuan')->constrained('lokasi');
            $table->foreignId('shipment')->constrained('shipments');
            $table->foreignId('kondisi')->constrained('kondisi');
            $table->foreignId('satuan')->constrained('satuan');
            $table->integer('tarif')->default(0);
            $table->text('keterangan')->nullable();
            $table->string('unit')->nullable();
            $table->string('min_qty');
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarif');
    }
};
