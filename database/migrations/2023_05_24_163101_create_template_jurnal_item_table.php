<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_jurnal_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_jurnal_id')->constrained('template_jurnal');
            $table->foreignId('coa_id')->constrained('coa');
            $table->string('tipe');
            $table->string('no')->nullable();
            $table->string('deskripsi')->nullable();
            $table->double('jumlah')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_jurnal_items');
    }
};
