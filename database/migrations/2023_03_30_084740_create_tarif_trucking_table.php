<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarif_trucking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customer_trucking');
            $table->foreignId('tujuan_id')->constrained('sangu_sopir');
            $table->string('tipe');
            $table->double('tarif');
            $table->boolean('is_active')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarif_trucking');
    }
};
