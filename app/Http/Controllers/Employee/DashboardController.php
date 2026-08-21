<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();
        $employee = $this->employeeFor($user);
        $profileCompletion = $employee ? $this->profileCompletion($employee) : 0;

        $latestContract = $employee?->contractHistories()->latest('tanggal_mulai')->first();

        return view('employee.dashboard', [
            'employee' => $employee,
            'division' => $employee?->jabatan_divisi ?? 'Belum diatur',
            'contractStatus' => $employee?->status_aktif === 'aktif' ? 'Pegawai_Tetap' : 'Belum aktif',
            'profileCompletion' => $profileCompletion,
            'documentsCount' => $employee?->documents()->where('jenis_dokumen', '!=', 'Kontrak_Kerja')->count() ?? 0,
            'latestContract' => $latestContract,
            'announcements' => Announcement::query()->whereIn('target_role', ['semua', 'karyawan'])
                ->latest('published_at')
                ->take(5)
                ->get(),
        ]);
    }

    private function employeeFor($user): ?Employee
    {
        return Employee::query()
            ->where(function ($query) use ($user): void {
                $query->where('email', $user->email)
                    ->orWhere('nip', $user->login_id)
                    ->orWhere('nama', $user->name);
            })
            ->where('peran', 'karyawan')
            ->first()
            ?? Employee::query()
                ->where(function ($query) use ($user): void {
                    $query->where('email', $user->email)
                        ->orWhere('nip', $user->login_id)
                        ->orWhere('nama', $user->name);
                })
                ->first();
    }

    private function profileCompletion(Employee $employee): int
    {
        $fields = ['nip', 'nama', 'email', 'telepon', 'alamat', 'ktp', 'kk', 'npwp', 'tempat_lahir', 'tanggal_lahir', 'agama', 'jenis_kelamin', 'jabatan_divisi'];
        $filled = collect($fields)->filter(fn ($field) => filled($employee->{$field}))->count();

        return (int) round(($filled / count($fields)) * 100);
    }
}
