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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('foto')->nullable();
            $table->string('nip_nipppk', 18)->unique()->nullable();
            $table->string('gelar_depan', 7)->nullable();
            $table->string('nama_pegawai', 50);
            $table->string('gelar_belakang', 15)->nullable();
            $table->string('jabatan', 50)->nullable();
            $table->string('pangkat', 25)->nullable();
            $table->string('golongan', 5)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
