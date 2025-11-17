<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // PERUBAHAN: 'laporan_monitoring' -> 'laporan_monitorings'
        Schema::create('laporan_monitorings', function (Blueprint $table) {
            $table->id();
            // PERUBAHAN: ->constrained('distribusis')
            $table->foreignId('distribusi_id')->constrained('distribusis');
            $table->foreignId('user_id')->constrained('users');
            $table->text('hasil_monitoring');
            $table->date('tanggal_monitoring');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_monitorings');
    }
};