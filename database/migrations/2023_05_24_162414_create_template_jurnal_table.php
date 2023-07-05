<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_jurnal', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('tipe',['JNL','BBK','BBM','BKK','BKM']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_jurnal');
    }
};
