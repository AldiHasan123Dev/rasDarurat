<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coa_id')->nullable()->constrained('coa')->nullOnDelete()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('kode');
            $table->string('nama');
            $table->string('keterangan')->nullable();
            $table->boolean('is_active')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coa');
    }
};
