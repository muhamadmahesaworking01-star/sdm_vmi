<?php

namespace App\Http\Controllers\Direksi;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use App\Models\Announcement;
use App\Models\EmployeeDocument;
use App\Models\ActivityLog;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function __invoke()
    {
        /** @var User $authenticatedUser */
        $authenticatedUser = request()->user();
        abort_unless($authenticatedUser instanceof User, 403);
        $roles = [User::ROLE_KARYAWAN, User::ROLE_PENGAJAR, User::ROLE_KARYAWAN_PENGAJAR];
        /** @var Collection<int, Employee> $employees */
        $employees = Employee::with(['contractHistories', 'documents'])
            ->whereHas('user', fn ($query) => $query->whereIn('role', $roles))
            ->get();
        $totalEmployees = $employees->count();
        $totalStaff = $employees->filter(fn ($employee) => $employee->user?->role === User::ROLE_KARYAWAN)->count();
        $totalTeachers = $employees->filter(fn ($employee) => $employee->user?->role === User::ROLE_PENGAJAR)->count();
        $totalDoubleRole = $employees->filter(fn ($employee) => $employee->user?->role === User::ROLE_KARYAWAN_PENGAJAR)->count();
        $direksiEmployees = Employee::with(['user', 'contractHistories', 'documents'])
            ->whereHas('user', fn ($query) => $query->where('role', User::ROLE_DIREKSI))
            ->get();
        $totalDireksi = $direksiEmployees->count();
        $totalActive = $employees->where('status_aktif', 'aktif')->count();
        $totalInactive = $employees->where('status_aktif', 'nonaktif')->count();

        $belumFinalisasi = $employees->filter(fn ($employee) => $this->profileCompletion($employee) < 90)->count();
        $finalisasiAkun = $totalEmployees - $belumFinalisasi;
        $divisionDistribution = $employees->groupBy(fn ($employee) => $employee->divisi_akademik ?: 'Belum diisi')->map->count()->sortDesc();
        $campusDistribution = $employees->groupBy(fn ($employee) => $employee->kampus_asal ?: 'Belum diisi')->map->count()->sortDesc();
        $monthlyUpdates = $this->monthlyUpdates($employees);
        $approvalEmployees = $employees->sortBy('nama')->take(4)->values();
        $documentCount = EmployeeDocument::whereIn('employee_id', $employees->pluck('id'))->count();
        $documentReadyEmployees = $employees->filter(fn ($employee) => $employee->documents()->exists())->count();
        $turnoverRate = $totalEmployees > 0 ? round(($totalInactive / $totalEmployees) * 100, 1) : 0;

        return view('direksi.dashboard', [
            'employees' => $employees,
            'direksiEmployees' => $direksiEmployees,
            'totalEmployees' => $totalEmployees,
            'totalStaff' => $totalStaff,
            'totalTeachers' => $totalTeachers,
            'totalDoubleRole' => $totalDoubleRole,
            'totalDireksi' => $totalDireksi,
            'totalActive' => $totalActive,
            'totalInactive' => $totalInactive,
            'user' => $authenticatedUser,
            'belumFinalisasi' => $belumFinalisasi,
            'finalisasiAkun' => $finalisasiAkun,
            'divisionDistribution' => $divisionDistribution,
            'campusDistribution' => $campusDistribution,
            'divisionMax' => $divisionDistribution->max() ?: 1,
            'campusMax' => $campusDistribution->max() ?: 1,
            'monthlyUpdates' => $monthlyUpdates,
            'approvalEmployees' => $approvalEmployees,
            'announcements' => Announcement::query()->latest('published_at')->take(3)->get(),
            'activities' => ActivityLog::with('user')->latest()->take(50)->get(),
            'documentCount' => $documentCount,
            'documentReadyEmployees' => $documentReadyEmployees,
            'documentCompletionRate' => $totalEmployees > 0 ? round(($documentReadyEmployees / $totalEmployees) * 100, 1) : 0,
            'turnoverRate' => $turnoverRate,
        ]);
    }

    /** @param Collection<int, Employee> $employees */
    private function monthlyUpdates(Collection $employees): array
    {
        $categories = [User::ROLE_KARYAWAN => 'Karyawan', User::ROLE_PENGAJAR => 'Pengajar', User::ROLE_KARYAWAN_PENGAJAR => 'Double Role'];
        $months = collect(range(2, 0))->map(function (int $ago) use ($employees, $categories): array {
            $month = now()->startOfMonth()->subMonths($ago);
            $items = collect($categories)->mapWithKeys(function (string $label, string $role) use ($employees, $month): array {
                return [$label => $employees->filter(fn ($employee) => $employee->user?->role === $role && $employee->updated_at?->between($month, $month->copy()->endOfMonth()))->count()];
            })->all();
            $daily = collect(range(1, $month->daysInMonth))->mapWithKeys(function (int $day) use ($employees, $categories, $month): array {
                $date = $month->copy()->day($day);
                $count = $employees->filter(fn ($employee) => $employee->updated_at?->isSameDay($date))
                    ->filter(fn ($employee) => array_key_exists($employee->user?->role, $categories))
                    ->count();

                return [(string) $day => $count];
            })->all();

            return [
                'key' => $month->format('Y-m'),
                'label' => $month->translatedFormat('M'),
                'full_label' => $month->translatedFormat('F Y'),
                'days_in_month' => $month->daysInMonth,
                'items' => $items,
                'daily' => $daily,
                'total' => array_sum($items),
            ];
        })->values()->all();
        $previous = $months[1]['total'] ?? 0;
        $current = $months[2]['total'] ?? 0;
        return ['months' => $months, 'categories' => array_values($categories), 'max' => max(collect($months)->max('total') ?: 0, 1), 'current_percentage' => $previous > 0 ? round((($current - $previous) / $previous) * 100, 1) : ($current > 0 ? 100 : 0)];
    }

    private function profileCompletion(Employee $employee): int
    {
        $fields = ['nama', 'email', 'telepon', 'alamat', 'ktp', 'kk', 'tempat_lahir', 'tanggal_lahir', 'agama', 'jenis_kelamin', 'status_pernikahan'];
        return (int) round((collect($fields)->filter(fn ($field) => filled($employee->{$field}))->count() / count($fields)) * 100);
    }
}
