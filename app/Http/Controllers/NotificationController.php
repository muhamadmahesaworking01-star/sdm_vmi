<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\SupportRequest;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $announcements = $user->role === 'super_admin'
            ? Announcement::latest('published_at')->take(10)->get()
            : Announcement::whereIn('target_role', ['semua', $user->role])->latest('published_at')->take(10)->get();
        $supportRequests = $user->role === 'super_admin' ? SupportRequest::with('user', 'resolver')->latest()->paginate(15, ['*'], 'support_page') : SupportRequest::with('resolver')->where('user_id', $user->id)->latest()->get();
        return view('notifications.index', compact('announcements', 'supportRequests'));
    }

    public function storeSupport(Request $request) { $data=$request->validate(['target'=>'required|string|max:50','message'=>'required|string|max:5000']); $request->user()->supportRequests()->create($data); return response()->json(['message'=>'Pengajuan pembaruan sudah dikirim ke Admin.']); }
    public function resolveSupport(Request $request, SupportRequest $supportRequest) { $supportRequest->update(['status'=>'selesai','resolved_by'=>$request->user()->id,'resolved_at'=>now()]); return back()->with('success','Pengajuan Call Center ditandai selesai.'); }
}
