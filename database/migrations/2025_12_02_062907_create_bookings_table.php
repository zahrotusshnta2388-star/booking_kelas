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
            $table->string('nama_peminjam');  // Ubah dari 'pemesan_nama'
            $table->string('nim')->nullable();  // Langsung nullable di sini
            $table->string('no_hp');  // Ubah dari 'pemesan_no_hp'
            $table->string('keperluan');  // Ubah dari 'kegiatan'
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->integer('jumlah_peserta')->nullable();
            $table->string('pemesan_email')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};