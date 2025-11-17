<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // PERUBAHAN: 'sekolah' -> 'sekolahs'
        Schema::create('sekolahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            // PERUBAHAN: ->constrained('kelurahans')
            $table->foreignId('kelurahan_id')->constrained('kelurahans');
            $table->string('npsn');
            $table->string('nama_sekolah');
            $table->string('alamat');
            $table->string('jenis_sekolah', 50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sekolahs');
    }
};