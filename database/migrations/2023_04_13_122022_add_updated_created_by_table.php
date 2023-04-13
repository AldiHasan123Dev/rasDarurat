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
        Schema::table('agen', function($table) {
            $table->foreignId('created_by')->after('updated_at')->nullable()->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users');
        });
        Schema::table('asuransi', function($table) {
            $table->foreignId('created_by')->after('updated_at')->nullable()->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users');
        });
        Schema::table('barang', function($table) {
            $table->foreignId('created_by')->after('updated_at')->nullable()->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users');
        });
        Schema::table('bttb', function($table) {
            $table->foreignId('created_by')->after('updated_at')->nullable()->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users');
        });
        Schema::table('customers', function($table) {
            $table->foreignId('created_by')->after('updated_at')->nullable()->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users');
        });
        Schema::table('customer_trucking', function($table) {
            $table->foreignId('created_by')->after('updated_at')->nullable()->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users');
        });
        Schema::table('jadwal_kapal', function($table) {
            $table->foreignId('created_by')->after('updated_at')->nullable()->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users');
        });
        Schema::table('kapal', function($table) {
            $table->foreignId('created_by')->after('updated_at')->nullable()->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users');
        });
        Schema::table('kendaraan', function($table) {
            $table->foreignId('created_by')->after('updated_at')->nullable()->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users');
        });
        Schema::table('kondisi', function($table) {
            $table->foreignId('created_by')->after('updated_at')->nullable()->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users');
        });
        Schema::table('lokasi', function($table) {
            $table->foreignId('created_by')->after('updated_at')->nullable()->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users');
        });
        Schema::table('nsfp', function($table) {
            $table->foreignId('created_by')->after('updated_at')->nullable()->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users');
        });
        Schema::table('order', function($table) {
            $table->foreignId('created_by')->after('updated_at')->nullable()->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users');
        });
        Schema::table('order_trucking', function($table) {
            $table->foreignId('created_by')->after('updated_at')->nullable()->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users');
        });
        Schema::table('pelayaran', function($table) {
            $table->foreignId('created_by')->after('updated_at')->nullable()->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users');
        });
        Schema::table('pengirim', function($table) {
            $table->foreignId('created_by')->after('updated_at')->nullable()->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users');
        });
        Schema::table('sangu_sopir', function($table) {
            $table->foreignId('created_by')->after('updated_at')->nullable()->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users');
        });
        Schema::table('satuan', function($table) {
            $table->foreignId('created_by')->after('updated_at')->nullable()->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users');
        });
        Schema::table('shipments', function($table) {
            $table->foreignId('created_by')->after('updated_at')->nullable()->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users');
        });
        Schema::table('sopir', function($table) {
            $table->foreignId('created_by')->after('updated_at')->nullable()->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users');
        });
        Schema::table('tagihan', function($table) {
            $table->foreignId('created_by')->after('updated_at')->nullable()->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users');
        });
        Schema::table('tarif', function($table) {
            $table->foreignId('created_by')->after('updated_at')->nullable()->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users');
        });
        Schema::table('tarif_agen', function($table) {
            $table->foreignId('created_by')->after('updated_at')->nullable()->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users');
        });
        Schema::table('tarif_pelayaran', function($table) {
            $table->foreignId('created_by')->after('updated_at')->nullable()->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users');
        });
        Schema::table('tarif_trucking', function($table) {
            $table->foreignId('created_by')->after('updated_at')->nullable()->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users');
        });
        Schema::table('tarif_truk', function($table) {
            $table->foreignId('created_by')->after('updated_at')->nullable()->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users');
        });
        Schema::table('transaksi', function($table) {
            $table->foreignId('created_by')->after('updated_at')->nullable()->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users');
        });
        Schema::table('transaksi_sopir', function($table) {
            $table->foreignId('created_by')->after('updated_at')->nullable()->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users');
        });
        Schema::table('transaksi_trucking', function($table) {
            $table->foreignId('created_by')->after('updated_at')->nullable()->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users');
        });
        Schema::table('truk', function($table) {
            $table->foreignId('created_by')->after('updated_at')->nullable()->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users');
        });
        Schema::table('users', function($table) {
            $table->foreignId('created_by')->after('updated_at')->nullable()->constrained('users');
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
