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
        Schema::create('costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spt_id')->nullable()->constrained('spts')->onDelete('set null');
            $table->foreignId('sppd_id')->nullable()->constrained('sppds')->onDelete('set null');
            $table->foreignId('employee_spt_id')->nullable()->constrained('employee_spt')->onDelete('set null');

            $table->decimal('uang_perhari', 10, 2)->nullable();
            $table->integer('lama_hari')->nullable();
            $table->decimal('total_uang_harian', 10, 2)->nullable();
            $table->decimal('uang_representasi', 10, 2)->nullable(); // tidak wajib di isi untuk pegawai tertentu saja
            $table->string('nama_hotel')->nullable();
            $table->decimal('biaya_akomodasi', 10, 2)->nullable();
            $table->decimal('biaya_kontribusi', 10, 2)->nullable();
            $table->decimal('biaya_tiket_berangkat', 10, 2)->nullable();
            $table->decimal('biaya_tiket_kembali', 10, 2)->nullable();
            $table->decimal('biaya_bantuan_transport', 10, 2)->nullable();
            $table->decimal('biaya_taxi_berangkat', 10, 2)->nullable();
            $table->decimal('biaya_taxi_kembali', 10, 2)->nullable();
            $table->decimal('biaya_travel', 10, 2)->nullable();
            $table->decimal('biaya_tidak_menginap', 10, 2)->nullable();
            $table->decimal('total_biaya', 10, 2)->nullable();
            $table->string('bukti_file')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('costs');
    }
};
