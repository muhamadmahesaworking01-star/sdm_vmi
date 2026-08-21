<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();
        $teacher = $user->employee;

        $academicDivision = $teacher?->divisi_akademik ?? 'Belum diatur';

        return view('teacher.dashboard', [
            'teacher' => $teacher,
            'academicDivision' => $academicDivision,
            'profileCompletion' => $teacher ? $this->profileCompletion($teacher) : 0,
            'announcements' => Announcement::query()->whereIn('target_role', ['semua', 'pengajar'])
                ->latest('published_at')
                ->take(5)
                ->get(),
        ]);
    }

    private function profileCompletion(Employee $employee): int
    {
        $fields = ['nip', 'nama', 'email', 'telepon', 'alamat', 'ktp', 'kk', 'npwp', 'tempat_lahir', 'tanggal_lahir', 'agama', 'jenis_kelamin', 'divisi_akademik', 'kampus_asal'];
        $filled = collect($fields)->filter(fn ($field) => filled($employee->{$field}))->count();

        return (int) round(($filled / count($fields)) * 100);
    }
}
