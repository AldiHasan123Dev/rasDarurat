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
            $table->foreignId('coa_debit_id')->nullable()->constrained('coa');
            $table->foreignId('coa_credit_id')->nullable()->constrained('coa');
            $table->string('keterangan')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_jurnal_items');
    }
};
