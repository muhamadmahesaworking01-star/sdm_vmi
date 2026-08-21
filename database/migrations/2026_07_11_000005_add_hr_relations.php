<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (! Schema::hasColumn('employees', 'gol_darah')) {
                $table->enum('gol_darah', ['A', 'B', 'AB', 'O'])->nullable()->after('kk');
            }
            if (! Schema::hasColumn('employees', 'status_pernikahan')) {
                $table->enum('status_pernikahan', ['Menikah', 'Belum Menikah'])->default('Belum Menikah')->after('gol_darah');
            }
            if (! Schema::hasColumn('employees', 'tanggal_keluar')) {
                $table->date('tanggal_keluar')->nullable()->after('tanggal_masuk');
            }
        });

        if (! Schema::hasTable('tabel_dokumen_karyawan')) {
            Schema::create('tabel_dokumen_karyawan', function (Blueprint $table) {
                $table->increments('id_dokumen');
                $table->string('nip_pemilik');
                $table->enum('jenis_dokumen', ['KTP', 'KK', 'Ijazah', 'Sertifikat_Pelatihan', 'Kontrak_Kerja']);
                $table->string('nama_file_path');
                $table->timestamp('tanggal_upload')->useCurrent();
                $table->foreign('nip_pemilik')->references('nip')->on('employees')->cascadeOnUpdate()->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('tabel_komponen_gaji')) {
            Schema::create('tabel_komponen_gaji', function (Blueprint $table) {
                $table->increments('id_komponen');
                $table->string('nip_pegawai')->unique();
                $table->decimal('gaji_pokok', 12, 2)->default(0);
                $table->decimal('total_tunjangan_rutin', 12, 2)->default(0);
                $table->timestamp('tanggal_update')->useCurrent()->useCurrentOnUpdate();
                $table->foreign('nip_pegawai')->references('nip')->on('employees')->cascadeOnUpdate()->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('tabel_payroll_bulanan')) {
            Schema::create('tabel_payroll_bulanan', function (Blueprint $table) {
                $table->increments('id_payroll');
                $table->string('nip_pegawai');
                $table->string('no_slip', 50)->unique();
                $table->string('bulan_tahun', 7);
                $table->decimal('gaji_pokok_history', 12, 2);
                $table->decimal('tunjangan_history', 12, 2)->default(0);
                $table->decimal('bonus_closing', 12, 2)->default(0);
                $table->decimal('thr', 12, 2)->default(0);
                $table->decimal('bonus_akhir_tahun', 12, 2)->default(0);
                $table->decimal('total_gaji_clean', 12, 2);
                $table->dateTime('tanggal_transfer');
                $table->enum('status_pembayaran', ['Pending', 'Diproses', 'Lunas'])->default('Pending');
                $table->foreign('nip_pegawai')->references('nip')->on('employees')->cascadeOnUpdate()->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('tabel_riwayat_kontrak')) {
            Schema::create('tabel_riwayat_kontrak', function (Blueprint $table) {
                $table->increments('id_kontrak');
                $table->string('nip_pegawai');
                $table->enum('tipe_kontrak', ['Magang', 'Kontrak_Tahunan', 'Pegawai_Tetap']);
                $table->date('tanggal_mulai');
                $table->date('tanggal_selesai')->nullable();
                $table->text('keterangan')->nullable();
                $table->foreign('nip_pegawai')->references('nip')->on('employees')->cascadeOnUpdate()->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('tabel_spesialisasi_pengajar')) {
            Schema::create('tabel_spesialisasi_pengajar', function (Blueprint $table) {
                $table->increments('id_spesialisasi');
                $table->string('nip_pengajar');
                $table->string('nama_keahlian', 100);
                $table->foreign('nip_pengajar')->references('nip')->on('employees')->cascadeOnUpdate()->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tabel_spesialisasi_pengajar');
        Schema::dropIfExists('tabel_riwayat_kontrak');
        Schema::dropIfExists('tabel_payroll_bulanan');
        Schema::dropIfExists('tabel_komponen_gaji');
        Schema::dropIfExists('tabel_dokumen_karyawan');

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['gol_darah', 'status_pernikahan', 'tanggal_keluar']);
        });
    }
};
