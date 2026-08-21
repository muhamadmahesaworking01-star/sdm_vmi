<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            foreach (['jalan', 'dusun', 'kelurahan', 'kecamatan', 'kota', 'provinsi'] as $column) {
                $table->string($column)->nullable();
            }
            $table->string('rt', 10)->nullable();
            $table->string('rw', 10)->nullable();
            $table->string('kode_pos', 10)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'jalan', 'dusun', 'kelurahan', 'kecamatan', 'kota', 'provinsi',
                'rt', 'rw', 'kode_pos',
            ]);
        });
    }
};
