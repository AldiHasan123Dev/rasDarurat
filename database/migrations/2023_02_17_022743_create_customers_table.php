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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->foreignId('marketing_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cs_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('pic')->nullable();
            $table->text('alamat')->nullable();
            $table->string('kota')->nullable();
            $table->string('telp')->nullable();
            $table->string('hp')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->nullable();
            $table->string('tipe')->nullable();
            $table->string('no_bl')->nullable();
            $table->string('top')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
