<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('ruangans', function (Blueprint $table) {
        $table->id();
        $table->string('nama');
        $table->string('kode')->unique();
        $table->string('gedung');
        $table->integer('lantai');
        $table->integer('kapasitas');
        $table->json('fasilitas')->nullable();
        $table->text('deskripsi')->nullable();
        $table->enum('status', ['tersedia', 'tidak_tersedia'])->default('tersedia');
        $table->timestamps();
    });
}

    public function down()
    {
        Schema::dropIfExists('ruangans');
    }
};