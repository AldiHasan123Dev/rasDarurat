<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nsfp', function (Blueprint $table) {
            $table->id();
            $table->string('nomor');
            $table->text('keterangan')->nullable();
            $table->boolean('available')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nsfp');
    }
};
