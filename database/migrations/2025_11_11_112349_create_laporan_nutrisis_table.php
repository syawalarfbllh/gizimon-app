<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // PERUBAHAN: 'laporan_nutrisi' -> 'laporan_nutrisis'
        Schema::create('laporan_nutrisis', function (Blueprint $table) {
            $table->id();
            // PERUBAHAN: ->constrained('distribusis')
            $table->foreignId('distribusi_id')->constrained('distribusis');
            $table->foreignId('user_id')->constrained('users');
            $table->float('lemak');
            $table->float('protein');
            $table->float('karbohidrat');
            $table->text('catatan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_nutrisis');
    }
};