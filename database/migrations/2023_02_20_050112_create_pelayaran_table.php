<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelayaran', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->string('nama');
            $table->string('pic')->nullable();
            $table->text('alamat')->nullable();
            $table->string('kota')->nullable();
            $table->string('telp')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->nullable();
            $table->string('hp')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelayaran');
    }
};
