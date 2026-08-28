<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use App\Models\Announcement;
use App\Models\ActivityLog;
use App\Models\ContractHistory;
use App\Models\EmployeeDocument;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $roles = [User::ROLE_KARYAWAN, User::ROLE_PENGAJAR, User::ROLE_KARYAWAN_PENGAJAR, User::ROLE_DIREKSI];
        $totalEmployees = Employee::whereHas('user', fn ($query) => $query->whereIn('role', $roles))->count();
        $totalUsers = User::count();
        $totalStaff = Employee::whereHas('user', fn ($query) => $query->where('role', User::ROLE_KARYAWAN))->count();
        $totalTeachers = Employee::whereHas('user', fn ($query) => $query->where('role', User::ROLE_PENGAJAR))->count();
        $totalDoubleRole = Employee::whereHas('user', fn ($query) => $query->where('role', User::ROLE_KARYAWAN_PENGAJAR))->count();
        $totalDireksi = Employee::whereHas('user', fn ($query) => $query->where('role', User::ROLE_DIREKSI))->count();
        $totalActive = Employee::whereHas('user', fn ($query) => $query->whereIn('role', $roles))->where('status_aktif', 'aktif')->count();
        $totalInactive = Employee::whereHas('user', fn ($query) => $query->whereIn('role', $roles))->where('status_aktif', 'nonaktif')->count();

        $employeePercentage = $totalEmployees > 0 ? round(($totalStaff / $totalEmployees) * 100, 2) : 0;
        $teacherPercentage = $totalEmployees > 0 ? round(($totalTeachers / $totalEmployees) * 100, 2) : 0;
        $belumFinalisasi = Employee::whereHas('user', fn ($query) => $query->whereIn('role', $roles))->get()->filter(fn ($employee) => $this->profileCompletion($employee) < 90)->count();
        $persenBelumFinalisasi = $totalEmployees > 0 ? round(($belumFinalisasi / $totalEmployees) * 100, 2) : 0;
        $finalisasiAkun = $totalEmployees - $belumFinalisasi;
        $divisiAkademikDistribusi = Employee::whereHas('user', fn ($query) => $query->whereIn('role', $roles))->get()->groupBy(fn ($employee) => $employee->divisi_akademik ?: 'Belum diisi')->map(fn ($items, $name) => ['name' => $name, 'count' => $items->count()])->values();
        $kampusAsalDistribusi = Employee::whereHas('user', fn ($query) => $query->whereIn('role', $roles))->get()->groupBy(fn ($employee) => $employee->kampus_asal ?: 'Belum diisi')->map(fn ($items, $name) => ['name' => $name, 'count' => $items->count()])->values();
        $monthlyUpdates = $this->monthlyUpdates($roles);
        $today = now()->startOfDay();
        $contracts = ContractHistory::whereHas('employee.user', fn ($query) => $query->whereIn('role', $roles))->get();
        $contractSummary = [
            'aktif' => $contracts->filter(fn ($contract) => $contract->tanggal_mulai?->lte($today) && (!$contract->tanggal_selesai || $contract->tanggal_selesai->gte($today)))->count(),
            'akan_berakhir' => $contracts->filter(fn ($contract) => $contract->tanggal_selesai?->between($today, now()->addDays(30)))->count(),
            'berakhir' => $contracts->filter(fn ($contract) => $contract->tanggal_selesai?->lt($today))->count(),
        ];
        $announcements = Announcement::latest('published_at')->latest()->take(3)->get();
        $activities = ActivityLog::with('user')->latest()->take(4)->get();
        $accountStatus = [
            'aktif' => User::where('status_akun', 'aktif')->with('employee')->get(),
            'nonaktif' => User::where('status_akun', '!=', 'aktif')->with('employee')->get(),
            'belum_terhubung' => User::whereNull('employee_id')->get(),
        ];
        $employeeDetails = Employee::with(['user', 'documents', 'contractHistories'])
            ->whereHas('user', fn ($query) => $query->whereIn('role', $roles))->get();
        $incompleteEmployees = $employeeDetails->filter(fn ($employee) => $this->profileCompletion($employee) < 90)->values();

        return view('admin.dashboard', [
            'totalEmployees' => $totalEmployees,
            'totalUsers' => $totalUsers,
            'totalStaff' => $totalStaff,
            'totalTeachers' => $totalTeachers,
            'totalDoubleRole' => $totalDoubleRole,
            'totalDireksi' => $totalDireksi,
            'totalActive' => $totalActive,
            'totalInactive' => $totalInactive,
            'employeePercentage' => $employeePercentage,
            'teacherPercentage' => $teacherPercentage,
            'belumFinalisasi' => $belumFinalisasi,
            'persenBelumFinalisasi' => $persenBelumFinalisasi,
            'finalisasiAkun' => $finalisasiAkun,
            'divisiAkademikDistribusi' => $divisiAkademikDistribusi,
            'kampusAsalDistribusi' => $kampusAsalDistribusi,
            'divisiAkademikMax' => $divisiAkademikDistribusi->max('count') ?: 1,
            'kampusAsalMax' => $kampusAsalDistribusi->max('count') ?: 1,
            'monthlyUpdates' => $monthlyUpdates,
            'contractSummary' => $contractSummary,
            'documentCount' => EmployeeDocument::whereHas('employee.user', fn ($query) => $query->whereIn('role', $roles))->count(),
            'announcements' => $announcements,
            'activities' => $activities,
            'accountStatus' => $accountStatus,
            'employeeDetails' => $employeeDetails,
            'incompleteEmployees' => $incompleteEmployees,
        ]);
    }

    private function monthlyUpdates(array $roles): array
    {
        $employees = Employee::with('user')
            ->whereHas('user', fn ($query) => $query->whereIn('role', $roles))
            ->get();
        $categories = [User::ROLE_KARYAWAN => 'Karyawan', User::ROLE_PENGAJAR => 'Pengajar', User::ROLE_KARYAWAN_PENGAJAR => 'Double Role'];
        $months = collect(range(2, 0))->map(function (int $ago) use ($employees, $categories): array {
            $month = now()->startOfMonth()->subMonths($ago);
            $items = collect($categories)->mapWithKeys(function (string $label, string $role) use ($employees, $month): array {
                return [$label => $employees->filter(fn ($employee) => $employee->user?->role === $role && $employee->updated_at?->between($month, $month->copy()->endOfMonth()))->count()];
            })->all();
            $daily = collect(range(1, $month->daysInMonth))->mapWithKeys(function (int $day) use ($employees, $categories, $month): array {
                $date = $month->copy()->day($day);
                $count = $employees->filter(fn ($employee) => $employee->updated_at?->isSameDay($date))->filter(function ($employee) use ($categories) {
                    return array_key_exists($employee->user?->role, $categories);
                })->count();

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
