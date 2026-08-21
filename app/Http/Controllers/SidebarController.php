<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class SidebarController extends Controller
{
    /**
     * Get sidebar menu items based on user role.
     */
    public function getMenuItems(): array
    {
        $user = Auth::user();
        $role = $user->role ?? null;

        return match ($role) {
            'super_admin' => $this->superAdminMenu(),
            'direksi' => $this->direksiMenu(),
            'karyawan' => $this->karyawanMenu(),
            'pengajar' => $this->pengajarMenu(),
            'karyawan_pengajar' => $this->karyawanPengajarMenu(),
            default => [],
        };
    }

    /**
     * Super Admin: CRUD penuh untuk data akun, karyawan, pengajar, dan spesialisasi.
     */
    private function superAdminMenu(): array
    {
        return [
            [
                'title' => 'Dashboard',
                'icon' => 'bi-speedometer2',
                'route' => 'admin.dashboard',
                'children' => [],
            ],
            ['title' => 'Profil', 'icon' => 'bi-person-badge', 'route' => 'admin.profile.edit', 'children' => []],
            [
                'title' => 'Manajemen Akun',
                'icon' => 'bi-people',
                'route' => 'admin.users.index',
                'children' => [],
            ],
            [
                'title' => 'Karyawan',
                'icon' => 'bi-person-lines-fill',
                'route' => 'admin.employees.index',
                'children' => [],
            ],
            [
                'title' => 'Pengajar',
                'icon' => 'bi-mortarboard',
                'route' => 'admin.teachers.index',
                'children' => [],
            ],
            [
                'title' => 'Spesialisasi',
                'icon' => 'bi-stars',
                'route' => 'admin.specializations.index',
                'children' => [],
            ],
            [
                'title' => 'Pengumuman',
                'icon' => 'bi-megaphone',
                'route' => 'admin.announcements.index',
                'children' => [],
            ],
            [
                'title' => 'Kontrak',
                'icon' => 'bi-file-earmark-check',
                'route' => 'admin.contracts.index',
                'children' => [],
            ],
            [
                'title' => 'Log Aktivitas',
                'icon' => 'bi-activity',
                'route' => 'activity.logs',
                'children' => [],
            ],
            [
                'title' => 'Backup Data',
                'icon' => 'bi-database-down',
                'route' => 'admin.backup',
                'children' => [],
            ],
        ];
    }

    /**
     * Direksi: akses monitoring read-only.
     */
    private function direksiMenu(): array
    {
        return [
            ['title' => 'Profil', 'icon' => 'bi-person-badge', 'route' => 'direksi.profile', 'children' => []],
            [
                'title' => 'Dashboard',
                'icon' => 'bi-graph-up-arrow',
                'route' => 'direksi.dashboard',
                'children' => [],
            ],
            [
                'title' => 'Kontrak',
                'icon' => 'bi-file-earmark-text',
                'route' => 'direksi.contracts.index',
                'children' => [],
            ],
            [
                'title' => 'Karyawan',
                'icon' => 'bi-person-lines-fill',
                'route' => 'direksi.employees',
                'children' => [],
            ],
            [
                'title' => 'Pengajar',
                'icon' => 'bi-mortarboard',
                'route' => 'direksi.teachers',
                'children' => [],
            ],
        ];
    }

    /**
     * Karyawan: akses mandiri untuk dashboard, biodata kantor, dan informasi tim.
     */
    private function karyawanMenu(): array
    {
        return [
            ['title' => 'Dashboard', 'icon' => 'bi-speedometer2', 'route' => 'employee.dashboard', 'children' => []],
            [
                'title' => 'Profil',
                'icon' => 'bi-person-circle',
                'route' => 'employee.profile',
                'children' => [],
            ],
            [
                'title' => 'Kontrak',
                'icon' => 'bi-file-earmark-text',
                'route' => 'employee.contracts.index',
                'children' => [],
            ],
            [
                'title' => 'Dokumen',
                'icon' => 'bi-folder2-open',
                'route' => 'employee.documents.index',
                'children' => [],
            ],
        ];
    }

    /**
     * Pengajar: akses mandiri untuk dashboard, profil akademik, dan kompetensi.
     */
    private function pengajarMenu(): array
    {
        return [
            [
                'title' => 'Dashboard',
                'icon' => 'bi-speedometer2',
                'route' => 'teacher.dashboard',
                'children' => [],
            ],
            [
                'title' => 'Profil',
                'icon' => 'bi-person-circle',
                'route' => 'teacher.profile',
                'children' => [],
            ],
            [
                'title' => 'Kontrak',
                'icon' => 'bi-file-earmark-text',
                'route' => 'teacher.contracts.index',
                'children' => [],
            ],
            [
                'title' => 'Dokumen',
                'icon' => 'bi-folder2-open',
                'route' => 'teacher.documents.index',
                'children' => [],
            ],
        ];
    }

    /**
     * Karyawan & Pengajar: akses hibrida untuk profil administrasi dan akademik.
     */
    private function karyawanPengajarMenu(): array
    {
        return [
            [
                'title' => 'Dashboard',
                'icon' => 'bi-speedometer2',
                'route' => 'double-role.dashboard',
                'children' => [],
            ],
            [
                'title' => 'Profil',
                'icon' => 'bi-person-vcard',
                'route' => 'double-role.profile',
                'children' => [],
            ],
            [
                'title' => 'Profil Akademik',
                'icon' => 'bi-mortarboard',
                'route' => 'double-role.profile.academic',
                'children' => [],
            ],
            [
                'title' => 'Kontrak',
                'icon' => 'bi-file-earmark-text',
                'route' => 'double-role.contracts.index',
                'children' => [],
            ],
        ];
    }
}
