<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // PERUBAHAN: 'kelurahan' -> 'kelurahans'
        Schema::create('kelurahans', function (Blueprint $table) {
            $table->id();
            // PERUBAHAN: ->constrained('kecamatans')
            $table->foreignId('kecamatan_id')->constrained('kecamatans');
            $table->string('nama_kelurahan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelurahans');
    }
};