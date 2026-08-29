<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContractHistory;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContractManagementController extends Controller
{
    public function index()
    {
        return $this->renderPage('overview', 'Ringkasan Manajemen Kontrak', 'Pantau status kontrak, batas akhir, dan riwayat kerja sama secara terpusat.');
    }

    public function data()
    {
        return $this->renderPage('data', 'Data Kontrak', 'Daftar pegawai dan status kontrak saat ini yang perlu dipantau tim admin.');
    }

    public function monitoring()
    {
        return $this->renderPage('monitoring', 'Monitoring Kontrak', 'Lihat indikator kontrak yang sedang berjalan dan yang butuh perhatian.');
    }

    public function expiring()
    {
        return $this->renderPage('expiring', 'Kontrak Berakhir', 'Daftar kontrak yang akan segera berakhir dalam rentang waktu dekat.');
    }

    public function history()
    {
        return $this->renderPage('history', 'Riwayat Kontrak', 'Riwayat seluruh kontrak yang pernah berjalan untuk setiap pegawai.');
    }

    public function show(string $nip)
    {
        $employee = Employee::where('nip', $nip)->with('contractHistories')->firstOrFail();
        return view('admin.contracts.show', compact('employee'));
    }

    public function extend(Request $request, string $nip)
    {
        $validated = $request->validate([
            'tipe_kontrak' => ['required', Rule::in(['Magang', 'Kontrak_Tahunan', 'Pegawai_Tetap'])],
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'nullable|string',
        ]);

        $employee = Employee::where('nip', $nip)->firstOrFail();
        $latest = $employee->contractHistories()->latest('tanggal_mulai')->first();

        if ($latest && $this->isActiveExtension($latest)) {
            return back()->with('error', 'Pegawai ini masih memiliki perpanjangan kontrak aktif.');
        }

        $history = ContractHistory::create([
            'employee_id' => $employee->id,
            'tipe_kontrak' => $validated['tipe_kontrak'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'keterangan' => $validated['keterangan'] ?? 'Perpanjangan oleh admin',
        ]);

        return redirect()->route('admin.contracts.index')->with('success', 'Perpanjangan kontrak berhasil ditambahkan.');
    }

    public function updateExtension(Request $request, ContractHistory $contract)
    {
        abort_unless($this->isExtension($contract), 404);

        $validated = $request->validate([
            'tipe_kontrak' => ['required', Rule::in(['Magang', 'Kontrak_Tahunan', 'Pegawai_Tetap'])],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $contract->update([
            'tipe_kontrak' => $validated['tipe_kontrak'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'keterangan' => $validated['keterangan'] ?: 'Perpanjangan oleh admin',
        ]);

        return back()->with('success', 'Jangka waktu perpanjangan berhasil diperbarui.');
    }

    public function cancelExtension(ContractHistory $contract)
    {
        abort_unless($this->isExtension($contract), 404);

        $contract->delete();

        return back()->with('success', 'Perpanjangan kontrak berhasil dibatalkan.');
    }

    public function exportAll(): StreamedResponse
    {
        $rows = Employee::with('contractHistories')->get()->map(function ($e) {
            $latest = $e->contractHistories->sortByDesc('tanggal_mulai')->first();
            return [
                'nama' => $e->nama,
                'nip' => $e->nip,
                'jabatan' => $e->jabatan_divisi,
                'status' => $latest ? 'Aktif' : 'Belum',
                'tipe' => $latest?->tipe_kontrak ?? '-',
                'mulai' => $latest?->tanggal_mulai?->toDateString() ?? '-',
                'selesai' => $latest?->tanggal_selesai?->toDateString() ?? '-',
            ];
        })->toArray();

        $response = new StreamedResponse(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, array_keys($rows[0] ?? ['nama','nip','jabatan','status','tipe','mulai','selesai']));
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="kontrak_all.csv"');
        return $response;
    }

    public function exportEmployee(string $nip): StreamedResponse
    {
        $employee = Employee::where('nip', $nip)->with('contractHistories')->firstOrFail();
        $rows = $employee->contractHistories->map(fn ($h) => [
            'tipe_kontrak' => $h->tipe_kontrak,
            'tanggal_mulai' => $h->tanggal_mulai?->toDateString(),
            'tanggal_selesai' => $h->tanggal_selesai?->toDateString(),
            'keterangan' => $h->keterangan,
        ])->toArray();

        $response = new StreamedResponse(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, array_keys($rows[0] ?? ['tipe_kontrak','tanggal_mulai','tanggal_selesai','keterangan']));
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="kontrak_'.$employee->nip.'.csv"');
        return $response;
    }

    /**
     * API: data lengkap untuk Super Admin (CRUD & export)
     */
    public function apiAdmin()
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->isSuperAdmin(), 403);

        $employees = Employee::with('contractHistories')->orderBy('nama')->get()->map(function ($e) {
            $latest = $e->contractHistories->sortByDesc('tanggal_mulai')->first();
            return [
                'nama' => $e->nama,
                'nip' => $e->nip,
                'email' => $e->email,
                'jabatan' => $e->jabatan_divisi,
                'status_akun' => $e->status_aktif,
                'kontrak_terakhir' => $latest ? [
                    'tipe' => $latest->tipe_kontrak,
                    'mulai' => $latest->tanggal_mulai?->toDateString(),
                    'selesai' => $latest->tanggal_selesai?->toDateString(),
                    'keterangan' => $latest->keterangan,
                ] : null,
                'riwayat_count' => $e->contractHistories->count(),
            ];
        });

        return response()->json(['data' => $employees]);
    }

    /**
     * API: ringkasan untuk Direksi (read-only, indikator utama)
     */
    public function apiDireksi()
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('direksi'), 403);

        $total = Employee::count();
        $withContracts = Employee::whereHas('contractHistories')->count();
        $expiringSoon = ContractHistory::whereNotNull('tanggal_selesai')->whereBetween('tanggal_selesai', [now()->toDateString(), now()->addDays(30)->toDateString()])->count();

        return response()->json([
            'total_pegawai' => $total,
            'dengan_kontrak' => $withContracts,
            'kontrak_berakhir_30hari' => $expiringSoon,
        ]);
    }

    /**
     * API: data kontrak untuk pegawai saat ini (karyawan/pengajar/karyawan_pengajar)
     */
    public function apiMe()
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $employee = $user->employee?->load('contractHistories');
        if (! $employee) {
            return response()->json(['data' => null]);
        }

        $latest = $employee->contractHistories->sortByDesc('tanggal_mulai')->first();

        return response()->json([
            'nama' => $employee->nama,
            'nip' => $employee->nip,
            'jabatan' => $employee->jabatan_divisi,
            'kontrak_terakhir' => $latest ? [
                'tipe' => $latest->tipe_kontrak,
                'mulai' => $latest->tanggal_mulai?->toDateString(),
                'selesai' => $latest->tanggal_selesai?->toDateString(),
                'keterangan' => $latest->keterangan,
            ] : null,
            'riwayat' => $employee->contractHistories->map(fn ($h) => [
                'tipe' => $h->tipe_kontrak,
                'mulai' => $h->tanggal_mulai?->toDateString(),
                'selesai' => $h->tanggal_selesai?->toDateString(),
                'keterangan' => $h->keterangan,
            ]),
        ]);
    }

    public function direksiContracts()
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('direksi'), 403);

        $today = Carbon::now()->toDateString();
        $nextThirtyDays = Carbon::now()->addDays(30)->toDateString();

        $totalPegawai = Employee::count();
        $withContracts = Employee::whereHas('contractHistories')->count();
        $contractDocuments = EmployeeDocument::where('jenis_dokumen', 'Kontrak_Kerja')->count();
        $biodataComplete = Employee::whereNotNull('ktp')
            ->whereNotNull('kk')
            ->whereNotNull('npwp')
            ->whereNotNull('alamat')
            ->count();
        $ktpComplete = Employee::whereNotNull('ktp')->count();
        $npwpComplete = Employee::whereNotNull('npwp')->count();
        $sertifikatComplete = Employee::whereNotNull('nomor_sertifikat')->orWhereNotNull('dokumen_pelatihan')->count();

        $summary = [
            'total_pegawai' => $totalPegawai,
            'dengan_kontrak' => $withContracts,
            'kontrak_berakhir_30hari' => ContractHistory::whereNotNull('tanggal_selesai')
                ->whereBetween('tanggal_selesai', [$today, $nextThirtyDays])
                ->count(),
            'contract_documents' => $contractDocuments,
            'contract_documents_percent' => $totalPegawai ? round($contractDocuments / $totalPegawai * 100) : 0,
            'biodata_lengkap' => $biodataComplete,
            'ktp_percent' => $totalPegawai ? round($ktpComplete / $totalPegawai * 100) : 0,
            'npwp_percent' => $totalPegawai ? round($npwpComplete / $totalPegawai * 100) : 0,
            'sertifikat_percent' => $totalPegawai ? round($sertifikatComplete / $totalPegawai * 100) : 0,
        ];

        $expiringSoon = ContractHistory::with('employee')
            ->whereNotNull('tanggal_selesai')
            ->whereBetween('tanggal_selesai', [$today, $nextThirtyDays])
            ->orderBy('tanggal_selesai')
            ->get();

        return view('direksi.contracts.index', compact('summary', 'expiringSoon'));
    }

    public function employeeContracts()
    {
        return $this->renderPersonalContractPage('employee.contracts.index', 'Kontrak Saya', 'Informasi kontrak untuk karyawan saat ini.');
    }

    public function teacherContracts()
    {
        return $this->renderPersonalContractPage('teacher.contracts.index', 'Kontrak Saya', 'Informasi kontrak untuk pengajar saat ini.');
    }

    public function doubleRoleContracts()
    {
        return $this->renderPersonalContractPage('double-role.contracts.index', 'Kontrak Saya', 'Informasi kontrak untuk peran ganda Anda.');
    }

    private function renderPersonalContractPage(string $view, string $title, string $description)
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $employee = Employee::query()
            ->where(function ($query) use ($user) {
                $query->where('email', $user->email)
                    ->orWhere('nip', $user->login_id)
                    ->orWhere('nama', $user->name);
            })
            ->with('contractHistories')
            ->first();

        $latest = $employee?->contractHistories->sortByDesc('tanggal_mulai')->first();
        $history = $employee?->contractHistories->sortByDesc('tanggal_mulai') ?? collect();

        return view($view, compact('employee', 'latest', 'history', 'title', 'description'));
    }

    private function renderPage(string $section, string $title, string $description)
    {
        $today = Carbon::now()->toDateString();
        $nextThirtyDays = Carbon::now()->addDays(30)->toDateString();

        $search = request('search');
        $status = request('status');
        $jabatan = request('jabatan');
        $tipe = request('tipe');

        $employeesQuery = Employee::query()->with('contractHistories');

        if ($search) {
            $employeesQuery->where(function ($query) use ($search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('jabatan_divisi', 'like', "%{$search}%");
            });
        }

        if ($jabatan) {
            $employeesQuery->where('jabatan_divisi', 'like', "%{$jabatan}%");
        }

        if ($status) {
            $employeesQuery->whereHas('contractHistories', fn ($query) => $query->whereNotNull('tanggal_mulai'));
            if ($status === 'belum') {
                $employeesQuery = Employee::query()->with('contractHistories')->whereDoesntHave('contractHistories');
            }
        }

        if ($tipe) {
            $employeesQuery->whereHas('contractHistories', fn ($query) => $query->where('tipe_kontrak', 'like', "%{$tipe}%"));
        }

        $employees = $employeesQuery->orderBy('nama')->get();

        $activeContracts = $employees->filter(fn ($employee) => $employee->contractHistories->isNotEmpty())->values();
        $employeesForData = $employees->reject(function ($employee) {
            $latest = $employee->contractHistories->sortByDesc('tanggal_mulai')->first();
            return $latest && $this->isActiveExtension($latest);
        });
        $extensionCandidates = $employeesForData->filter(function ($employee) use ($nextThirtyDays) {
            $latest = $employee->contractHistories->sortByDesc('tanggal_mulai')->first();

            return ! $latest || ! $latest->tanggal_selesai || $latest->tanggal_selesai->lte($nextThirtyDays);
        })->values();
        $expiringSoon = ContractHistory::query()
            ->whereNotNull('tanggal_selesai')
            ->whereBetween('tanggal_selesai', [$today, $nextThirtyDays])
            ->with('employee')
            ->orderBy('tanggal_selesai')
            ->get();

        $history = ContractHistory::query()
            ->with('employee')
            ->latest('tanggal_mulai')
            ->take(12)
            ->get();

        $summary = [
            'total_pegawai' => $employees->count(),
            'kontrak_aktif' => $activeContracts->count(),
            'kontrak_akan_berakhir' => $expiringSoon->count(),
            'riwayat_kontrak' => $history->count(),
        ];

        $dataContracts = $employeesForData->map(function ($employee) {
            $latest = $employee->contractHistories->sortByDesc('tanggal_mulai')->first();

            return [
                'nama' => $employee->nama,
                'nip' => $employee->nip,
                'jabatan' => $employee->jabatan_divisi ?? 'Belum ditentukan',
                'status' => ! $latest ? 'Belum ada' : ($latest->tanggal_selesai?->isPast() ? 'Berakhir' : 'Aktif'),
                'tipe' => $latest?->tipe_kontrak ?? '-',
                'mulai' => $latest?->tanggal_mulai?->translatedFormat('d F Y') ?? '-',
                'selesai' => $latest?->tanggal_selesai?->translatedFormat('d F Y') ?? '-',
                'contract_id' => $latest?->getKey(),
            ];
        });

        $monitoringList = $activeContracts->map(function ($employee) {
            $latest = $employee->contractHistories->sortByDesc('tanggal_mulai')->first();

            return [
                'nama' => $employee->nama,
                'nip' => $employee->nip,
                'tipe' => $latest?->tipe_kontrak ?? '-',
                'selesai' => $latest?->tanggal_selesai?->translatedFormat('d F Y') ?? '-',
                'keterangan' => $latest?->keterangan ?? 'Kontrak berjalan',
            ];
        });

        return view('admin.contracts.index', compact('section', 'title', 'description', 'summary', 'employees', 'expiringSoon', 'history', 'dataContracts', 'monitoringList', 'extensionCandidates'));
    }

    private function isExtension(ContractHistory $contract): bool
    {
        return Str::contains(Str::lower($contract->keterangan ?? ''), 'perpanjang');
    }

    private function isActiveExtension(ContractHistory $contract): bool
    {
        return $this->isExtension($contract)
            && (! $contract->tanggal_selesai || ! $contract->tanggal_selesai->isPast());
    }
}
