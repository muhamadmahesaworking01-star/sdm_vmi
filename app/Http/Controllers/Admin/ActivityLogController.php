<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function index()
    {
        // Bersihkan log lama setiap kali halaman dibuka sebagai fallback
        // apabila scheduler belum berjalan di lingkungan lokal.
        ActivityLog::cleanupOlderThan(3);

        $recap = ActivityLog::getRecapLastDays();
        
        // Get current logs with pagination
        $logs = ActivityLog::with('user')->latest()->paginate(30)->withQueryString();
        
        return view('admin.activity-logs.index', compact('logs', 'recap'));
    }
}
