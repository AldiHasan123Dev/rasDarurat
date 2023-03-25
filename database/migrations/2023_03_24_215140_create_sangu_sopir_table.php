<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sangu_sopir', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tujuan')->constrained('lokasi');
            $table->string('ukuran');
            $table->double('sangu');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sangu_sopir');
    }
};
