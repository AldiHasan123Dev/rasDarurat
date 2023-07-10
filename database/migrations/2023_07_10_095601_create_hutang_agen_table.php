<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hutang_agen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tarif_agen_id')->constrained('tarif_agen');
            $table->foreignId('order_id')->constrained('order');
            $table->double('jumlah');
            $table->boolean('status')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hutang_agen');
    }
};
