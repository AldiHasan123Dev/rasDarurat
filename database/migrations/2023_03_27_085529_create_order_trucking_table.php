<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_trucking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained('order')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customer_trucking')->nullOnDelete();
            $table->foreignId('tarif_id')->nullable()->constrained('tarif_trucking')->nullOnDelete();
            $table->foreignId('sopir_id')->nullable()->constrained('sopir')->nullOnDelete();
            $table->foreignId('kendaraan_id')->nullable()->constrained('kendaraan')->nullOnDelete();
            $table->string('container')->nullable();
            $table->string('seal')->nullable();
            $table->string('tujuan')->nullable();
            $table->string('tipe')->nullable();
            $table->double('sangu')->default(0);
            $table->double('simpanan')->default(0);
            $table->double('tagihan')->default(0);
            $table->double('kuli')->default(0);
            $table->date('sj_kembali')->nullable();
            $table->date('sj_kembali_fa')->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('ambil_empty_tambak_langon')->default(0);
            $table->boolean('ambil_empty_teluk_langon')->default(0);
            $table->boolean('bongkar_full_teluk_langon')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_trucking');
    }
};
