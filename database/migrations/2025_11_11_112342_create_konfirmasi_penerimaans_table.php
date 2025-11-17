<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // PERUBAHAN: 'konfirmasi_penerimaan' -> 'konfirmasi_penerimaans'
        Schema::create('konfirmasi_penerimaans', function (Blueprint $table) {
            $table->id();
            // PERUBAHAN: ->constrained('distribusis')
            $table->foreignId('distribusi_id')->constrained('distribusis');
            $table->foreignId('user_id')->constrained('users');
            $table->dateTime('waktu_konfirmasi');
            $table->enum('status', ['diterima', 'ditolak']);
            $table->text('catatan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konfirmasi_penerimaans');
    }
};