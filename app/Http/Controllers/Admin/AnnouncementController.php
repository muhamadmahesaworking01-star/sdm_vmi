<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        return view('admin.announcements.index', [
            'announcements' => Announcement::latest('published_at')->latest()->paginate(10),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'target_role' => ['nullable', 'in:semua,karyawan,pengajar'],
        ]);

        Announcement::create($validated + ['target_role' => $validated['target_role'] ?? 'semua',
            'published_at' => now(),
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil diterbitkan.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil dihapus.');
    }
}
