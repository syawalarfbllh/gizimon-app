<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // PERUBAHAN: 'distribusi' -> 'distribusis'
        Schema::create('distribusis', function (Blueprint $table) {
            $table->id();
            // PERUBAHAN: foreign keys
            $table->foreignId('penyedia_id')->constrained('penyedias');
            $table->foreignId('sekolah_id')->constrained('sekolahs');
            $table->foreignId('menu_id')->constrained('menu_mbgs');
            $table->date('tanggal_distribusi');
            $table->integer('jumlah_paket');
            $table->enum('status', ['disiapkan', 'dikirim', 'diterima', 'dibatalkan']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribusis');
    }
};