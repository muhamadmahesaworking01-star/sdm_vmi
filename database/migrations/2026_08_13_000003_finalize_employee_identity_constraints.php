<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tabel_komponen_gaji')) {
            $hasUnique = DB::getDriverName() !== 'mysql' || DB::table('information_schema.STATISTICS')
                ->whereRaw('TABLE_SCHEMA = DATABASE()')
                ->where('TABLE_NAME', 'tabel_komponen_gaji')
                ->where('COLUMN_NAME', 'employee_id')
                ->where('NON_UNIQUE', 0)
                ->exists();
            if (! $hasUnique) {
                Schema::table('tabel_komponen_gaji', function (Blueprint $table) {
                    $table->unique('employee_id', 'uk_employee_gaji');
                });
            }
        }

        if (DB::getDriverName() === 'mysql') {
            $foreignKeys = DB::select(
                'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = "users" AND COLUMN_NAME = "employee_id" AND REFERENCED_TABLE_NAME = "employees"'
            );
            $kept = false;
            foreach ($foreignKeys as $foreignKey) {
                if (! $kept && $foreignKey->CONSTRAINT_NAME === 'fk_users_employees') {
                    $kept = true;
                    continue;
                }
                if ($foreignKey->CONSTRAINT_NAME !== 'fk_users_employees' || ! $kept) {
                    DB::statement('ALTER TABLE `users` DROP FOREIGN KEY `'.$foreignKey->CONSTRAINT_NAME.'`');
                }
            }
        }
    }

    public function down(): void
    {
        // Constraint repair is intentionally kept in place for forward compatibility.
    }
};
