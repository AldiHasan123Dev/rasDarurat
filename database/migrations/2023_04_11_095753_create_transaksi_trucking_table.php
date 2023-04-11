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
        Schema::create('transaksi_trucking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customer_trucking');
            $table->date('tgl_invoice');
            $table->string('invoice');
            $table->foreignId('order_trucking_id')->constrained('order_trucking');
            $table->string('order_id');
            $table->integer('rit');
            $table->string('tipe');
            $table->double('lain_lain');
            $table->double('pph');
            $table->double('total');
            $table->integer('order');
            $table->foreignId('submited_by')->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_trucking');
    }
};
