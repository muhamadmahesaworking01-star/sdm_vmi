@extends('layouts.app')
@section('title', 'Notifikasi')
@section('page_title', 'Notifikasi')
@section('content')
<div class="notification-page"><div class="panel">
<div class="notification-heading"><div><h3>Notifikasi</h3><p>Pengumuman terbaru dari Admin.</p></div><i class="bi bi-bell-fill"></i></div>
<h5>Pengumuman Baru</h5>
@forelse($announcements as $announcement)
<article class="notification-item"><i class="bi bi-megaphone-fill"></i><div><b>{{ $announcement->title }}</b><p>{{ $announcement->content }}</p><small>{{ optional($announcement->published_at)->translatedFormat('d M Y, H:i') ?: 'Terbaru' }}</small></div></article>
@empty
<p class="empty">Belum ada pengumuman baru.</p>
@endforelse
@if(auth()->user()->role === 'super_admin')
<h5 class="mt-4">Pengajuan Call Center</h5>
@forelse($supportRequests as $item)
<article class="notification-item"><i class="bi bi-headset"></i><div class="flex-grow-1"><b>{{ $item->user?->name ?? 'User' }} - {{ $item->target }}</b><p>{{ $item->message }}</p><small>{{ $item->created_at->translatedFormat('d M Y, H:i') }} - {{ ucfirst($item->status) }}</small></div>
@if($item->status !== 'selesai')<form method="POST" action="{{ route('admin.notifications.support.resolve', $item) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-outline-success">Selesaikan</button></form>@endif
</article>
@empty
<p class="empty">Belum ada pengajuan Call Center.</p>
@endforelse
{{ $supportRequests->links() }}
@else
<h5 class="mt-4">Status Call Center Anda</h5>
@forelse($supportRequests as $item)
<article class="notification-item"><i class="bi bi-headset"></i><div><b>{{ $item->target }}</b><p>{{ $item->message }}</p><small>{{ $item->created_at->translatedFormat('d M Y, H:i') }} - <span class="{{ $item->status === 'selesai' ? 'text-success fw-semibold' : 'text-warning fw-semibold' }}">{{ $item->status === 'selesai' ? 'Selesai' : 'Menunggu tindak lanjut' }}</span>@if($item->resolved_at) - diselesaikan {{ $item->resolved_at->translatedFormat('d M Y, H:i') }}@endif</small></div></article>
@empty
<p class="empty">Belum ada pengajuan Call Center.</p>
@endforelse
@endif
</div></div>
@endsection
@push('styles')
<style>.notification-page{max-width:920px}.notification-page .panel{padding:1.5rem;border:1px solid #e6edf2;border-radius:12px;background:#fff}.notification-heading{display:flex;justify-content:space-between;align-items:flex-start;border-bottom:1px solid #edf1f4;padding-bottom:1rem;margin-bottom:1.25rem}.notification-heading h3{margin:0;font-size:1.25rem}.notification-heading p{margin:.25rem 0 0;color:#728194;font-size:.75rem}.notification-heading>i{font-size:1.4rem;color:#ff5258}.notification-item{display:flex;gap:.75rem;padding:.8rem 0;border-top:1px solid #edf1f4}.notification-item>i{width:32px;height:32px;display:grid;place-items:center;border-radius:9px;color:#1769dc;background:#eaf2ff;flex:none}.notification-item b,.notification-item p,.notification-item small{display:block}.notification-item b{font-size:.8rem}.notification-item p{margin:.2rem 0;color:#4d6078;font-size:.72rem;line-height:1.5}.notification-item small{color:#8492a2;font-size:.65rem}.notification-page h5{font-size:.88rem;margin-bottom:.7rem}.empty{color:#8492a2;font-size:.75rem}</style>
@endpush
