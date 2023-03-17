<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('order');
            $table->foreignId('pembayar_id')->constrained('customers');
            $table->string('tipe_invoice');
            $table->string('job');
            $table->string('invoice');
            $table->string('nsfp');
            $table->string('keterangan');
            $table->string('tujuan');
            $table->double('sub_total')->default(0);
            $table->double('tagihan')->default(0);
            $table->double('ppn')->default(0);
            $table->double('asuransi')->default(0);
            $table->double('admin')->default(0);
            $table->double('total')->default(0);
            $table->double('pph')->default(0);
            $table->integer('order');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
