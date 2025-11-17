<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // PERUBAHAN: 'penyedia' -> 'penyedias'
        Schema::create('penyedias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('nama_perusahaan');
            $table->string('alamat');
            $table->string('no_telp', 50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penyedias');
    }
};