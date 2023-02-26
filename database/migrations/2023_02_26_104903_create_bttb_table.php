<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bttb', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained('order')->nullOnDelete();
            $table->foreignId('barang_id')->nullable()->constrained('barang')->nullOnDelete();
            $table->string('no_gudang');
            $table->double('qty');
            $table->foreignId('satuan_id')->nullable()->constrained('satuan')->nullOnDelete();
            $table->date('tgl_masuk');
            $table->foreignId('pengirim_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->text('keterangan')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bttb');
    }
};