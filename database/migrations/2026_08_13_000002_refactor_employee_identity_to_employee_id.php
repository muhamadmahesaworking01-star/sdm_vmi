<?php

use App\Services\NipGenerator;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $childTables = [
        'tabel_dokumen_karyawan' => 'nip_pemilik',
        'tabel_komponen_gaji' => 'nip_pegawai',
        'tabel_payroll_bulanan' => 'nip_pegawai',
        'tabel_riwayat_kontrak' => 'nip_pegawai',
        'tabel_spesialisasi_pengajar' => 'nip_pengajar',
        'teacher_portfolios' => 'nip_pengajar',
    ];

    private array $childPrimaryKeys = [
        'tabel_dokumen_karyawan' => 'id_dokumen',
        'tabel_komponen_gaji' => 'id_komponen',
        'tabel_payroll_bulanan' => 'id_payroll',
        'tabel_riwayat_kontrak' => 'id_kontrak',
        'tabel_spesialisasi_pengajar' => 'id_spesialisasi',
        'teacher_portfolios' => 'id',
    ];

    public function up(): void
    {
        foreach (array_keys($this->childTables) as $tableName) {
            if (Schema::hasTable($tableName) && ! Schema::hasColumn($tableName, 'employee_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('employee_id')->nullable();
                });
            }
        }

        if (! Schema::hasColumn('users', 'employee_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('employee_id')->nullable()->unique()->after('id');
            });
        }

        // MySQL commits DDL statements implicitly, so the schema/data transition
        // is made resumable instead of wrapping ALTER TABLE in a transaction.
        {
            $employees = DB::table('employees')->orderBy('id')->get();
            $employeeIdsByNip = $employees->mapWithKeys(fn ($employee) => [$employee->nip => $employee->id]);
            $alreadyMapped = $this->allChildRowsHaveEmployeeId();

            foreach ($this->childTables as $tableName => $legacyColumn) {
                if (! Schema::hasTable($tableName)) {
                    continue;
                }

                if ($alreadyMapped) {
                    continue;
                }

                $unmapped = DB::table($tableName)
                    ->whereNull('employee_id')
                    ->whereNotNull($legacyColumn)
                    ->get([$this->childPrimaryKeys[$tableName], $legacyColumn])
                    ->filter(fn ($row) => ! isset($employeeIdsByNip[$row->{$legacyColumn}]));

                if ($unmapped->isNotEmpty()) {
                    $primaryKey = $this->childPrimaryKeys[$tableName];
                    $details = $unmapped->map(fn ($row) => $tableName.'.'.$row->{$primaryKey}.'='.$row->{$legacyColumn})->implode(', ');
                    throw new RuntimeException("Gagal memetakan data pegawai: {$details}");
                }

                foreach ($employeeIdsByNip as $nip => $employeeId) {
                    DB::table($tableName)->where($legacyColumn, $nip)->update(['employee_id' => $employeeId]);
                }
            }

            DB::table('users')->whereNull('employee_id')->orderBy('id')->eachById(function ($user) use ($employeeIdsByNip) {
                $employeeId = $employeeIdsByNip[$user->login_id] ?? DB::table('employees')->where('email', $user->email)->value('id');
                if ($employeeId) {
                    DB::table('users')->where('id', $user->id)->update(['employee_id' => $employeeId]);
                }
            });

            // Beberapa akun lama sudah ada sebelum master pegawai dibuat. Pertahankan
            // akun tersebut dengan membuat master pegawai minimal yang kemudian akan
            // mendapatkan NIP baru seperti data pegawai lainnya.
            DB::table('users')
                ->whereIn('role', ['karyawan', 'pengajar', 'karyawan_pengajar'])
                ->whereNull('employee_id')
                ->orderBy('id')
                ->eachById(function ($user) {
                    $peran = $user->role === 'pengajar' ? 'pengajar' : 'karyawan';
                    $employeeId = DB::table('employees')->insertGetId([
                        'nip' => '__PENDING_USER_'.$user->id,
                        'nama' => $user->name,
                        'peran' => $peran,
                        'email' => $user->email,
                        'status_aktif' => 'aktif',
                        'created_at' => $user->created_at ?: now(),
                        'updated_at' => now(),
                    ]);
                    DB::table('users')->where('id', $user->id)->update(['employee_id' => $employeeId]);
                });

            $employees = DB::table('employees')->orderBy('id')->get();
            $employeeIdsByNip = $employees->mapWithKeys(fn ($employee) => [$employee->nip => $employee->id]);

            $missingUsers = DB::table('users')
                ->whereIn('role', ['karyawan', 'pengajar', 'karyawan_pengajar'])
                ->whereNull('employee_id')
                ->pluck('id');
            if ($missingUsers->isNotEmpty()) {
                throw new RuntimeException('Akun pegawai tidak berhasil dipetakan: '.$missingUsers->implode(', '));
            }

            if (! $employees->every(fn ($employee) => preg_match('/^\d{15}$/', $employee->nip))) {
                // Pakai nilai sementara agar UNIQUE employees.nip tidak bentrok saat semua NIP diganti.
                foreach ($employees as $employee) {
                    DB::table('employees')->where('id', $employee->id)->update(['nip' => '__MIGRATING_'.$employee->id]);
                }

                $generator = app(NipGenerator::class);
                foreach ($employees as $employee) {
                    $role = DB::table('users')->where('employee_id', $employee->id)->value('role');
                    $entryYear = substr((string) ($employee->tanggal_masuk ?: substr((string) $employee->created_at, 0, 4) ?: now()->format('Y')), 0, 4);
                    $birthDate = $employee->tanggal_lahir
                        ? date('ymd', strtotime($employee->tanggal_lahir))
                        : date('ymd', strtotime($employee->created_at ?: now()));
                    $newNip = $generator->generateFromParts($role === 'direksi' ? '01' : ($role === 'pengajar' ? '03' : ($role === 'karyawan_pengajar' ? '04' : ($employee->peran === 'pengajar' ? '03' : '02'))), $entryYear, $birthDate);
                    DB::table('employees')->where('id', $employee->id)->update(['nip' => $newNip]);
                }
            }

            DB::table('users')->whereNotNull('employee_id')->orderBy('id')->eachById(function ($user) {
                $nip = DB::table('employees')->where('id', $user->employee_id)->value('nip');
                if ($nip && $user->login_id !== $nip) {
                    DB::table('users')->where('id', $user->id)->update(['login_id' => $nip]);
                }
            });

            $this->replaceLegacyForeignKeys();
        }
    }

    private function replaceLegacyForeignKeys(): void
    {
            if (DB::getDriverName() !== 'mysql') {
            Schema::withoutForeignKeyConstraints(function () {
                foreach ($this->childTables as $tableName => $legacyColumn) {
                    if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, $legacyColumn)) {
                        Schema::table($tableName, function (Blueprint $table) use ($legacyColumn) {
                            $table->dropForeign([$legacyColumn]);
                            if ($legacyColumn === 'nip_pegawai' && $table->getTable() === 'tabel_komponen_gaji') {
                                $table->dropUnique([$legacyColumn]);
                                $table->unique('employee_id');
                            }
                            $table->dropColumn($legacyColumn);
                        });
                    }
                }
            });

            return;
        }

        foreach ($this->childTables as $tableName => $legacyColumn) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            $foreignKeys = DB::select(
                'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL',
                [$tableName, $legacyColumn]
            );
            foreach ($foreignKeys as $foreignKey) {
                DB::statement('ALTER TABLE `'.$tableName.'` DROP FOREIGN KEY `'.$foreignKey->CONSTRAINT_NAME.'`');
            }

            if (Schema::hasColumn($tableName, $legacyColumn)) {
                $indexes = DB::select(
                    'SELECT DISTINCT INDEX_NAME FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ? AND NON_UNIQUE = 0 AND INDEX_NAME <> "PRIMARY"',
                    [$tableName, $legacyColumn]
                );
                foreach ($indexes as $index) {
                    DB::statement('ALTER TABLE `'.$tableName.'` DROP INDEX `'.$index->INDEX_NAME.'`');
                }
            }

            if ($tableName === 'tabel_komponen_gaji') {
                $employeeIndex = DB::selectOne(
                    'SELECT INDEX_NAME FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = "employee_id" AND NON_UNIQUE = 0 LIMIT 1',
                    [$tableName]
                );
                if (! $employeeIndex) {
                    DB::statement('ALTER TABLE `'.$tableName.'` ADD UNIQUE `uk_employee_gaji` (`employee_id`)');
                }
            }

            $newForeignKey = DB::selectOne(
                'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME = "employees" LIMIT 1',
                [$tableName, 'employee_id']
            );
            if (! $newForeignKey) {
                DB::statement('ALTER TABLE `'.$tableName.'` ADD CONSTRAINT `fk_'.$tableName.'_employee_id` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE');
            }

            if (Schema::hasColumn($tableName, $legacyColumn)) {
                DB::statement('ALTER TABLE `'.$tableName.'` DROP COLUMN `'.$legacyColumn.'`');
            }
        }

        $userForeignKey = DB::selectOne(
            'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = "users" AND COLUMN_NAME = "employee_id" AND REFERENCED_TABLE_NAME = "employees" LIMIT 1'
        );
        if (! $userForeignKey) {
            DB::statement('ALTER TABLE `users` ADD CONSTRAINT `fk_users_employees` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL');
        }
    }

    private function allChildRowsHaveEmployeeId(): bool
    {
        foreach (array_keys($this->childTables) as $tableName) {
            if (Schema::hasTable($tableName) && DB::table($tableName)->whereNull('employee_id')->exists()) {
                return false;
            }
        }

        return true;
    }

    public function down(): void
    {
        // Data migration is intentionally irreversible: restoring old NIP foreign keys
        // would reintroduce the identity coupling this migration removes.
    }
};
