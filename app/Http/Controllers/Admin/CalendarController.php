<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->date('month')?->startOfMonth() ?? now()->startOfMonth();
        $events = Employee::whereBetween('tanggal_masuk', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])->orderBy('tanggal_masuk')->get();
        return view('admin.calendar.index', compact('month', 'events'));
    }
}
