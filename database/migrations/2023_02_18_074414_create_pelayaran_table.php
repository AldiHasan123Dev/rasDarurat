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
        Schema::create('pelayaran', function (Blueprint $table) {
            $table->id();
            $table->string('kode',)->nullable();
            $table->string('nama',)->nullable();
            $table->string('pic',)->nullable();
            $table->string('alamat',)->nullable();
            $table->string('kota',)->nullable();
            $table->string('telp',)->nullable();
            $table->string('fax',)->nullable();
            $table->string('email',)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelayaran');
    }
};
