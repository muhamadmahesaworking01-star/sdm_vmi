<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Illuminate\Database\QueryException;

class NipGenerator
{
    public function generate(Employee $employee, ?string $role = null): string
    {
        $entryDate = $employee->tanggal_masuk?->format('Y')
            ?? $employee->created_at?->format('Y')
            ?? now()->format('Y');
        $birthDate = $employee->tanggal_lahir?->format('ymd')
            ?? $employee->created_at?->format('ymd')
            ?? now()->format('ymd');

        return $this->generateFromParts($this->categoryCode($employee, $role), $entryDate, $birthDate);
    }

    public function generateFromParts(string $categoryCode, string $entryYear, string $birthDate): string
    {
        $categoryCode = str_pad($categoryCode, 2, '0', STR_PAD_LEFT);
        $entryYear = substr((string) $entryYear, 0, 4);
        $birthDate = preg_replace('/[^0-9]/', '', $birthDate);

        if (! in_array($categoryCode, ['01', '02', '03', '04'], true)
            || ! preg_match('/^\d{4}$/', $entryYear)
            || ! preg_match('/^\d{6}$/', $birthDate)) {
            throw new InvalidArgumentException('Komponen NIP tidak valid.');
        }

        $sequence = DB::transaction(function () use ($categoryCode, $entryYear, $birthDate) {
            $record = DB::table('nip_sequences')
                ->where('category_code', $categoryCode)
                ->where('entry_year', $entryYear)
                ->where('birth_date', $birthDate)
                ->lockForUpdate()
                ->first();

            if ($record) {
                $next = $record->last_number + 1;
                DB::table('nip_sequences')->where('id', $record->id)->update([
                    'last_number' => $next,
                    'updated_at' => now(),
                ]);
            } else {
                $next = 1;
                try {
                    DB::table('nip_sequences')->insert([
                        'category_code' => $categoryCode,
                        'entry_year' => $entryYear,
                        'birth_date' => $birthDate,
                        'last_number' => $next,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (QueryException $exception) {
                    // Jika dua pendaftaran membuat kombinasi pertama bersamaan,
                    // ambil row yang baru dibuat lalu naikkan sequence dengan lock.
                    if (! str_contains($exception->getMessage(), 'Duplicate')) {
                        throw $exception;
                    }
                    $record = DB::table('nip_sequences')
                        ->where('category_code', $categoryCode)
                        ->where('entry_year', $entryYear)
                        ->where('birth_date', $birthDate)
                        ->lockForUpdate()
                        ->first();
                    $next = $record->last_number + 1;
                    DB::table('nip_sequences')->where('id', $record->id)->update([
                        'last_number' => $next,
                        'updated_at' => now(),
                    ]);
                }
            }

            return $next;
        });

        if ($sequence > 999) {
            throw new InvalidArgumentException('Nomor urut NIP untuk kombinasi ini sudah mencapai batas 999.');
        }

        return $categoryCode.$entryYear.$birthDate.str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
    }

    private function categoryCode(Employee $employee, ?string $role = null): string
    {
        $role ??= $employee->user?->role;

        return match ($role) {
            'direksi' => '01',
            'pengajar' => '03',
            'karyawan_pengajar' => '04',
            default => $employee->peran === 'pengajar' ? '03' : '02',
        };
    }
}
