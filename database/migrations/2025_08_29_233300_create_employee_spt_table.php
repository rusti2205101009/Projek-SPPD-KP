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
        Schema::create('employee_spt', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('spt_id')->constrained('spts')->onDelete('cascade');

            $table->string('gelar_depan', 7)->nullable();
            $table->string('nama_pegawai', 50);
            $table->string('gelar_belakang', 15)->nullable();
            $table->string('nip_nipppk', 18)->nullable();
            $table->string('jabatan', 50)->nullable();
            $table->string('pangkat', 25)->nullable();
            $table->string('golongan', 5)->nullable();
            $table->string('bidang', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_spt');
    }
};
