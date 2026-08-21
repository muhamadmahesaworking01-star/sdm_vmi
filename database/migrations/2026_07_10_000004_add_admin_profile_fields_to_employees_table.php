<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('jabatan_divisi')->nullable()->after('status_aktif');
            $table->string('id_atasan')->nullable()->after('jabatan_divisi');
            $table->string('divisi_akademik')->nullable()->after('id_atasan');
            $table->string('kampus_asal')->nullable()->after('divisi_akademik');
            $table->string('ktp')->nullable()->after('alamat');
            $table->string('kk')->nullable()->after('ktp');
            $table->date('tanggal_masuk')->nullable()->after('kk');
            $table->string('dokumen_pelatihan')->nullable()->after('tanggal_masuk');
            $table->string('nomor_sertifikat')->nullable()->after('dokumen_pelatihan');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'jabatan_divisi',
                'id_atasan',
                'divisi_akademik',
                'kampus_asal',
                'ktp',
                'kk',
                'tanggal_masuk',
                'dokumen_pelatihan',
                'nomor_sertifikat',
            ]);
        });
    }
};
