<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\ReportRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DirectorDashboardController extends Controller
{
    /**
     * Show director dashboard with KPIs
     */
    public function index()
    {
        // Current month data
        $roles = [User::ROLE_KARYAWAN, User::ROLE_PENGAJAR, User::ROLE_KARYAWAN_PENGAJAR];
        $employeeQuery = fn () => Employee::whereHas('user', fn ($query) => $query->whereIn('role', $roles));
        $totalSDM = $employeeQuery()->count();
        $totalKaryawan = $employeeQuery()->whereHas('user', fn ($query) => $query->where('role', User::ROLE_KARYAWAN))->count();
        $totalPendidik = $employeeQuery()->whereHas('user', fn ($query) => $query->where('role', User::ROLE_PENGAJAR))->count();
        $totalDoubleRole = $employeeQuery()->whereHas('user', fn ($query) => $query->where('role', User::ROLE_KARYAWAN_PENGAJAR))->count();

        // Previous month data
        $prevMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $prevMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        $prevTotalSDM = $employeeQuery()->whereBetween('created_at', [$prevMonthStart, $prevMonthEnd])->count();

        // Calculate trend percentage
        $trendSDM = $this->calculateTrend($prevTotalSDM, $totalSDM);
        $trendKaryawan = $this->calculateTrend(
            $employeeQuery()->whereHas('user', fn ($query) => $query->where('role', User::ROLE_KARYAWAN))->whereBetween('created_at', [$prevMonthStart, $prevMonthEnd])->count(),
            $totalKaryawan
        );
        $trendPendidik = $this->calculateTrend(
            $employeeQuery()->whereHas('user', fn ($query) => $query->where('role', User::ROLE_PENGAJAR))->whereBetween('created_at', [$prevMonthStart, $prevMonthEnd])->count(),
            $totalPendidik
        );
        $trendDoubleRole = $this->calculateTrend(
            $employeeQuery()->whereHas('user', fn ($query) => $query->where('role', User::ROLE_KARYAWAN_PENGAJAR))->whereBetween('created_at', [$prevMonthStart, $prevMonthEnd])->count(),
            $totalDoubleRole
        );

        // Distribution data
        $divisiAkademikDistribusi = $employeeQuery()->selectRaw('divisi_akademik as name, COUNT(*) as count')
            ->whereNotNull('divisi_akademik')
            ->groupBy('divisi_akademik')
            ->get()
            ->toArray();
        $divisiAkademikMax = Employee::whereNotNull('divisi_akademik')->count() ?: 1;

        $kampusAsalDistribusi = $employeeQuery()->selectRaw('kampus_asal as name, COUNT(*) as count')
            ->whereNotNull('kampus_asal')
            ->groupBy('kampus_asal')
            ->get()
            ->toArray();
        $kampusAsalMax = Employee::whereNotNull('kampus_asal')->count() ?: 1;

        // Recent report requests
        $recentReports = ReportRequest::with('user')
            ->where('user_id', Auth::id())
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.director.dashboard', compact(
            'totalSDM',
            'totalKaryawan',
            'totalPendidik',
            'totalDoubleRole',
            'trendSDM',
            'trendKaryawan',
            'trendPendidik',
            'trendDoubleRole',
            'divisiAkademikDistribusi',
            'divisiAkademikMax',
            'kampusAsalDistribusi',
            'kampusAsalMax',
            'recentReports'
        ));
    }

    /**
     * Show report request form
     */
    public function reportRequest()
    {
        $divisiList = Employee::whereNotNull('divisi_akademik')
            ->distinct('divisi_akademik')
            ->pluck('divisi_akademik')
            ->sort();

        $kampusList = Employee::whereNotNull('kampus_asal')
            ->distinct('kampus_asal')
            ->pluck('kampus_asal')
            ->sort();

        $reportTypes = ReportRequest::REPORT_TYPES;
        $formats = ReportRequest::FORMATS;

        return view('admin.director.report-request', compact('reportTypes', 'formats', 'divisiList', 'kampusList'));
    }

    /**
     * Store new report request
     */
    public function storeReportRequest(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|in:' . implode(',', array_keys(ReportRequest::REPORT_TYPES)),
            'filter_divisi' => 'nullable|string',
            'filter_kampus' => 'nullable|string',
            'filter_date_from' => 'nullable|date',
            'filter_date_to' => 'nullable|date',
            'format' => 'required|in:' . implode(',', ReportRequest::FORMATS),
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = ReportRequest::STATUS_PENDING;

        $report = ReportRequest::create($validated);

        return redirect()->route('admin.director.report-history')
            ->with('success', 'Laporan berhasil diminta. Laporan akan diproses dan siap download dalam beberapa saat.');
    }

    /**
     * Show report history
     */
    public function reportHistory()
    {
        $reports = ReportRequest::with('user')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        $stats = [
            'ready' => ReportRequest::where('user_id', Auth::id())
                ->where('status', ReportRequest::STATUS_READY)
                ->count(),
            'processing' => ReportRequest::where('user_id', Auth::id())
                ->where('status', ReportRequest::STATUS_PROCESSING)
                ->count(),
            'failed' => ReportRequest::where('user_id', Auth::id())
                ->where('status', ReportRequest::STATUS_FAILED)
                ->count(),
        ];

        return view('admin.director.report-history', compact('reports', 'stats'));
    }

    /**
     * Download report
     */
    public function downloadReport(ReportRequest $report)
    {
        // Check authorization
        if ($report->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if (!$report->isReady()) {
            return back()->with('error', 'Laporan tidak siap diunduh.');
        }

        if (!file_exists($report->file_path)) {
            return back()->with('error', 'File laporan tidak ditemukan.');
        }

        return response()->download($report->file_path);
    }

    /**
     * Delete report request
     */
    public function deleteReport(ReportRequest $report)
    {
        // Check authorization
        if ($report->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Delete file if exists
        if ($report->file_path && file_exists($report->file_path)) {
            unlink($report->file_path);
        }

        $report->delete();

        return back()->with('success', 'Laporan berhasil dihapus.');
    }

    /**
     * Helper: Calculate trend percentage
     */
    private function calculateTrend(int $prev, int $current): float|int
    {
        if ($prev == 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $prev) / $prev) * 100, 1);
    }
}
