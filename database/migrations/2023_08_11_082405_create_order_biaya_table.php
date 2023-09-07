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
        Schema::create('order_biaya', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('order');
            $table->date('tgl_dcf')->nullable();
            $table->date('tgl_opt')->nullable();
            $table->date('tgl_truk')->nullable();
            $table->date('tgl_kuli')->nullable();
            $table->date('tgl_jc')->nullable();
            $table->double('nominal_do')->default(0);
            $table->double('nominal_cleaning')->default(0);
            $table->double('nominal_fee')->default(0);
            $table->double('nominal_opt')->default(0);
            $table->double('nominal_truk')->default(0);
            $table->double('nominal_kuli')->default(0);
            $table->double('nominal_jc')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_biaya');
    }
};
