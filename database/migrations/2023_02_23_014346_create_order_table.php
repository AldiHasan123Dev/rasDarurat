<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order', function (Blueprint $table) {
            $table->id();
            $table->integer('no');
            $table->string('invoice')->nullable();
            $table->string('job');
            $table->string('no_job');
            $table->foreignId('tarif_id')->nullable()->constrained('tarif')->nullOnDelete();
            $table->foreignId('pengirim_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('penerima_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('penerima_bl_id')->nullable()->constrained('customers')->nullOnDelete();      
            $table->foreignId('barang_id')->nullable()->constrained('barang');
            $table->date('ba_kirim')->nullable();
            $table->date('stuffing')->nullable();
            $table->string('stuffing_type')->nullable();
            $table->date('full')->nullable();
            $table->date('barang_diantar')->nullable();
            $table->date('ba_kembali')->nullable();
            $table->string('resi')->nullable();
            $table->string('nopol')->nullable();
            $table->string('container')->nullable();
            $table->string('seal')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('no_bl')->nullable();
            $table->string('trucking')->nullable();
            $table->integer('status')->default(0);
            $table->integer('asuransi')->default(0)->nullable();
            $table->integer('agen')->default(0)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order');
    }
};
