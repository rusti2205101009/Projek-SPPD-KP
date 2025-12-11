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
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spt_id')->nullable()->constrained('spts')->onDelete('set null');
            $table->foreignId('departure_id')->nullable()->constrained('departures')->onDelete('set null');
            $table->foreignId('employee_spt_id')->nullable()->constrained('employee_spt')->onDelete('set null');
            $table->foreignId('sppd_id')->nullable()->constrained('sppds')->onDelete('set null');

            $table->date('tanggal_kembali_tiket')->nullable();
            $table->string('no_bukti', 50)->nullable();
            $table->date('tanggal_bukti')->nullable();
            $table->string('no_tiket_kembali', 20)->nullable();
            $table->string('nama_transportasi', 30)->nullable();
            $table->string('daerah_asal', 50)->nullable(); 
            $table->string('tempat_asal', 50)->nullable(); 
            $table->string('tempat_tujuan', 30)->nullable(); 
            $table->string('bukti_file_pulang')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
