<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table): void {
            $table->string('bpjs_ketenagakerjaan')->nullable()->after('npwp');
            $table->string('nama_gadis_ibu_kandung')->nullable()->after('bpjs_ketenagakerjaan');
            $table->unsignedSmallInteger('jumlah_tanggungan')->nullable()->after('nama_gadis_ibu_kandung');
            $table->string('foto_ktp')->nullable()->after('jumlah_tanggungan');
            $table->unsignedSmallInteger('tahun')->nullable()->after('foto_ktp');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table): void {
            $table->dropColumn([
                'bpjs_ketenagakerjaan',
                'nama_gadis_ibu_kandung',
                'jumlah_tanggungan',
                'foto_ktp',
                'tahun',
            ]);
        });
    }
};