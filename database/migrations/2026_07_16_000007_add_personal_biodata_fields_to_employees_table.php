<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('npwp', 32)->nullable()->after('kk');
            $table->string('tempat_lahir')->nullable()->after('npwp');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->string('agama', 32)->nullable()->after('tanggal_lahir');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable()->after('agama');
            $table->unsignedSmallInteger('berat_badan')->nullable()->after('jenis_kelamin');
            $table->unsignedSmallInteger('tinggi_badan')->nullable()->after('berat_badan');
            $table->enum('ukuran_baju', ['S', 'M', 'L', 'XL', 'XXL', 'XXXL'])->nullable()->after('tinggi_badan');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['npwp', 'tempat_lahir', 'tanggal_lahir', 'agama', 'jenis_kelamin', 'berat_badan', 'tinggi_badan', 'ukuran_baju']);
        });
    }
};
