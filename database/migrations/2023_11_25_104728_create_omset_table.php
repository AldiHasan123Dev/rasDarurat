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
        Schema::create('omset', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained('order');
            $table->double('opp')->default(0);
            $table->double('opt')->default(0);
            $table->double('ut')->default(0);
            $table->double('bl')->default(0);
            $table->double('apbs')->default(0);
            $table->double('cleaning')->default(0);
            $table->double('lss')->default(0);
            $table->double('storage')->default(0);
            $table->double('jasa_door')->default(0);
            $table->double('asuransi')->default(0);
            $table->double('ops')->default(0);
            $table->double('segel')->default(0);
            $table->double('buruh')->default(0);
            $table->double('checker')->default(0);
            $table->double('karantina')->default(0);
            $table->double('demmurage')->default(0);
            $table->double('kirim_dokumen')->default(0);
            $table->double('biaya_lain')->default(0);
            $table->double('flexibag')->default(0);
            $table->double('rc')->default(0);
            $table->double('biaya')->default(0);
            $table->double('tarif')->default(0);
            $table->double('laba_kotor')->default(0);
            $table->double('margin')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('omsets');
    }
};
