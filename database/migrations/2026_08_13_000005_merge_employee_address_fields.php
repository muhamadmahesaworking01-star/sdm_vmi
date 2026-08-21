<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $columns = ['jalan', 'dusun', 'kelurahan', 'kecamatan', 'kota', 'provinsi', 'rt', 'rw', 'kode_pos'];

    public function up(): void
    {
        DB::table('employees')->orderBy('id')->eachById(function ($employee) {
            $parts = [];
            foreach ($this->columns as $column) {
                if (filled($employee->{$column} ?? null)) {
                    $label = match ($column) {
                        'jalan' => 'Jalan', 'dusun' => 'Dusun', 'kelurahan' => 'Kelurahan',
                        'kecamatan' => 'Kecamatan', 'kota' => 'Kota', 'provinsi' => 'Provinsi',
                        'rt' => 'RT', 'rw' => 'RW', 'kode_pos' => 'Kode Pos',
                    };
                    $parts[] = $label.': '.$employee->{$column};
                }
            }

            if ($parts) {
                $current = trim((string) $employee->alamat);
                $merged = $current !== '' ? $current.'\n'.implode(', ', $parts) : implode(', ', $parts);
                DB::table('employees')->where('id', $employee->id)->update(['alamat' => $merged]);
            }
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['jalan', 'dusun', 'kelurahan', 'kecamatan', 'kota', 'provinsi', 'rt', 'rw', 'kode_pos']);
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('jalan')->nullable();
            $table->string('dusun')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kota')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('rt', 10)->nullable();
            $table->string('rw', 10)->nullable();
            $table->string('kode_pos', 10)->nullable();
        });
    }
};
