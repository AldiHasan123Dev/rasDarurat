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
        Schema::create('order_biaya_truck', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_trucking_id')->constrained('order_trucking');
            $table->double('nominal_sangu_kuli')->default(0);
            $table->double('nominal_sangu_kuli1')->default(0);
            $table->double('nominal_sangu_kuli2')->default(0);
            $table->double('nominal_sangu_kuli3')->default(0);
            $table->double('nominal_tb_tl')->default(0);
            $table->double('nominal_tb_tl1')->default(0);
            $table->double('nominal_stappel')->default(0);
            $table->double('nominal_stappel1')->default(0);
            $table->date('tgl_sangu_kuli1')->nullable();
            $table->date('tgl_sangu_kuli2')->nullable();
            $table->date('tgl_sangu_kuli3')->nullable();
            $table->date('tgl_tb_tl')->nullable();
            $table->date('tgl_stappel')->nullable();            
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
        Schema::dropIfExists('order_biaya_truck');
    }
};
