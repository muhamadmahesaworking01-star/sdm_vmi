<?php

use App\Services\NipGenerator;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE employees MODIFY peran ENUM('pengajar','karyawan','direksi') NOT NULL");
        }

        DB::table('users')->where('role', 'direksi')->whereNull('employee_id')->orderBy('id')->eachById(function ($user) {
            $employeeId = DB::table('employees')->where('email', $user->email)->value('id');
            if (! $employeeId) {
                $employee = new \App\Models\Employee([
                    'nama' => $user->name,
                    'email' => $user->email,
                    'peran' => 'direksi',
                    'status_aktif' => 'aktif',
                ]);
                $employee->nip = app(NipGenerator::class)->generate($employee, 'direksi');
                $employee->save();
                $employeeId = $employee->id;
            }

            $nip = DB::table('employees')->where('id', $employeeId)->value('nip');
            DB::table('users')->where('id', $user->id)->update([
                'employee_id' => $employeeId,
                'login_id' => $nip,
            ]);
        });
    }

    public function down(): void
    {
        // Director employee records are retained to avoid deleting identity data.
    }
};
