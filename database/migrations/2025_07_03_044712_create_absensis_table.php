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
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('tanggal');
            
            // Absen masuk
            $table->time('jam_masuk')->nullable();
            $table->string('lokasi_masuk_latitude')->nullable();
            $table->string('lokasi_masuk_longitude')->nullable();

            // Absen keluar
            $table->time('jam_keluar')->nullable();
            $table->string('lokasi_keluar_latitude')->nullable();
            $table->string('lokasi_keluar_longitude')->nullable();

            $table->enum('status', ['hadir', 'cuti', 'alfa']);
            $table->text('keterangan')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'tanggal']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
