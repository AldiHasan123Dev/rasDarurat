<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hutang_pelayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tarif_pelayaran_id')->constrained('tarif_pelayaran');
            $table->foreignId('order_id')->constrained('order');
            $table->double('jumlah');
            $table->boolean('status')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hutang_pelayaran');
    }
};
