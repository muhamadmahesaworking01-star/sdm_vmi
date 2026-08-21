<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
return new class extends Migration { public function up(): void { if (DB::getDriverName() === 'mysql') DB::statement("ALTER TABLE tabel_dokumen_karyawan MODIFY jenis_dokumen VARCHAR(60) NOT NULL"); } public function down(): void {} };
