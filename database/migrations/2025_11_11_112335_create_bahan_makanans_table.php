<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // PERUBAHAN: 'bahan_makanan' -> 'bahan_makanans'
        Schema::create('bahan_makanans', function (Blueprint $table) {
            $table->id();
            // PERUBAHAN: ->constrained('menu_mbgs')
            $table->foreignId('menu_id')->constrained('menu_mbgs');
            $table->string('nama_bahan');
            $table->string('jumlah');
            $table->string('satuan', 50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bahan_makanans');
    }
};