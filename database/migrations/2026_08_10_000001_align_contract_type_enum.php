<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tabel_riwayat_kontrak') && DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE tabel_riwayat_kontrak MODIFY tipe_kontrak ENUM('Magang', 'Kontrak_Tahunan', 'Pegawai_Tetap') NOT NULL");
        }
    }

    public function down(): void
    {
        // The original contract enum already contains these supported values.
    }
};
