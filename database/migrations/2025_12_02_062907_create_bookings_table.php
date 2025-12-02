<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::create('bookings', function (Blueprint $table) {
        $table->id();
        $table->foreignId('ruangan_id')->constrained()->onDelete('cascade');
        $table->date('tanggal');
        $table->time('jam_mulai');
        $table->time('jam_selesai');
        $table->string('kegiatan');
        $table->string('pemesan_nama');
        $table->string('pemesan_email');
        $table->string('pemesan_no_hp');
        $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
        $table->timestamps();
    });
}

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};