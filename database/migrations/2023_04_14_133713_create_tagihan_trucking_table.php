<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihan_trucking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('order_trucking');
            $table->string('nama');
            $table->integer('jumlah');
            $table->text('catatan')->nullable();
            $table->boolean('status')->default(1);
            $table->softDeletes();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan_trucking');
    }
};
