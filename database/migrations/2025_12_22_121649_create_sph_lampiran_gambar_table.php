<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sph_lampiran_gambar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sph_id')->constrained('surat_penawarans')->onDelete('cascade');
            $table->string('path_gambar');
            $table->string('nama_file');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sph_lampiran_gambar');
    }
};
