<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // PERUBAHAN: 'menu_mbg' -> 'menu_mbgs'
        Schema::create('menu_mbgs', function (Blueprint $table) {
            $table->id();
            // PERUBAHAN: ->constrained('penyedias')
            $table->foreignId('penyedia_id')->constrained('penyedias');
            $table->string('nama_menu');
            $table->text('deskripsi');
            $table->date('tanggal_menu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_mbgs');
    }
};