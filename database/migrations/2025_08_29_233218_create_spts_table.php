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
        Schema::create('spts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('head_division_id')->nullable()->constrained('head_divisions')->nullOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('templates')->nullOnDelete();
            $table->string('nomor_surat', 40);
            $table->date('tanggal_surat');
            $table->string('keperluan', 100);

            $table->string('nip_kepala', 18);
            $table->string('gelar_depan_kepala', 7)->nullable();
            $table->string('nama_kepala', 50);
            $table->string('gelar_belakang_kepala', 15)->nullable();
            $table->string('jabatan_kepala', 50);
            $table->string('pangkat_kepala', 25);
            $table->string('golongan_kepala', 5);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spts');
    }
};
