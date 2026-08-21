<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tabel_spesialisasi_pengajar') && ! Schema::hasColumn('tabel_spesialisasi_pengajar', 'nama_keahlian')) {
            Schema::table('tabel_spesialisasi_pengajar', function (Blueprint $table) {
                $table->string('nama_keahlian', 100)->after('nip_pengajar');
            });
        }

        if (! Schema::hasTable('teacher_portfolios')) {
            Schema::create('teacher_portfolios', function (Blueprint $table) {
                $table->id();
                $table->string('nip_pengajar');
                $table->string('judul', 150);
                $table->text('deskripsi')->nullable();
                $table->string('tautan', 500)->nullable();
                $table->string('file_path')->nullable();
                $table->timestamps();
                $table->foreign('nip_pengajar')->references('nip')->on('employees')->cascadeOnUpdate()->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_portfolios');
    }
};
