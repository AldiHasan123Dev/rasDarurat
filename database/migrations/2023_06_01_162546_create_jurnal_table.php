<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coa_id')->constrained('coa');
            $table->foreignId('order_id')->nullable()->constrained('order');
            $table->foreignId('jurnal_balik')->nullable()->constrained('jurnal');
            $table->string('nomor')->nullable();
            $table->string('nama');
            $table->double('debit')->default(0);
            $table->double('credit')->default(0);
            $table->enum('tipe',['JNL','BBK','BBM','BKK','BKM'])->default('JNL');
            $table->integer('no')->default(0);
            $table->integer('is_balik')->default(0);
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal');
    }
};
