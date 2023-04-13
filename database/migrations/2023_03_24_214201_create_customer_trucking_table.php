<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_trucking', function (Blueprint $table) {
            $table->id();
            $table->boolean('pph_23')->default(true);
            $table->string('nama');
            $table->text('alamat')->nullable();
            $table->string('hp')->nullable();
            $table->string('nik')->nullable();
            $table->string('npwp')->nullable();
            $table->string('nama_npwp')->nullable();
            $table->text('alamat_npwp')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_trucking');
    }
};
