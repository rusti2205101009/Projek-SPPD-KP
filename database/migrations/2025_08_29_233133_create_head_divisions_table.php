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
        Schema::create('head_divisions', function (Blueprint $table) {
            $table->id();
            $table->string('foto')->nullable();
            $table->string('nip', 18)->unique();
            $table->string('gelar_depan', 7)->nullable();
            $table->string('nama_kepala', 50);
            $table->string('gelar_belakang', 15)->nullable();
            $table->string('jabatan', 50);
            $table->string('pangkat', 25);
            $table->string('golongan', 5);
            $table->string('ttd')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('head_divisions');
    }
};
