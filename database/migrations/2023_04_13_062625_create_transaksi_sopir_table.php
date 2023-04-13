<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksi_sopir', function (Blueprint $table) {
            $table->id();
            $table->date('tgl_invoice');
            $table->string('invoice');
            $table->foreignId('sopir_id')->constrained('sopir');
            $table->string('order_id');
            $table->foreignId('order_trucking_id')->constrained('order_trucking');
            $table->double('total');
            $table->integer('order');
            $table->foreignId('submited_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_sopir');
    }
};
