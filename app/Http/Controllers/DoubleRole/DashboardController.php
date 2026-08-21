<?php

namespace App\Http\Controllers\DoubleRole;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();
        $employee = $user->employee;

        $division = $employee?->jabatan_divisi ?? 'Belum diatur';
        $academicDivision = $employee?->divisi_akademik ?? 'Belum diatur';

        return view('double-role.dashboard', [
            'employee' => $employee,
            'division' => $division,
            'academicDivision' => $academicDivision,
            'profileCompletion' => $employee ? $this->profileCompletion($employee) : 0,
            'announcements' => Announcement::query()
                ->latest('published_at')
                ->take(5)
                ->get(),
        ]);
    }

    private function profileCompletion(Employee $employee): int
    {
        $fields = ['nip', 'nama', 'email', 'telepon', 'alamat', 'ktp', 'kk', 'npwp', 'tempat_lahir', 'tanggal_lahir', 'agama', 'jenis_kelamin', 'jabatan_divisi', 'divisi_akademik', 'kampus_asal'];
        $filled = collect($fields)->filter(fn ($field) => filled($employee->{$field}))->count();

        return (int) round(($filled / count($fields)) * 100);
    }
}
