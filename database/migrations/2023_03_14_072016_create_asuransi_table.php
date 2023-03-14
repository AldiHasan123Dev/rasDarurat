<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asuransi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelayaran_id')->nullable()->constrained('pelayaran')->nullOnDelete();
            $table->string('nama');
            $table->double('rate')->default(0);
            $table->double('admin')->default(0);
            $table->double('min')->default(0);
            $table->double('max')->default(0);
            $table->text('keterangan')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asuransi');
    }
};
