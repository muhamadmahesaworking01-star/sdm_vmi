@extends('layouts.app')
@section('title', 'Kalender SDM') @section('page_title', 'Kalender SDM')
@section('content')
<div class="card border-0 shadow-sm"><div class="card-header bg-white d-flex justify-content-between align-items-center"><div><h5 class="mb-1">Kalender SDM</h5><small class="text-muted">Tanggal masuk kerja dan agenda dasar setiap user.</small></div><form method="GET"><input type="month" name="month" value="{{ $month->format('Y-m') }}" class="form-control" onchange="this.form.submit()"></form></div><div class="card-body"><div class="row g-3">@forelse($events as $event)<div class="col-md-6 col-xl-4"><div class="border rounded p-3"><strong>{{ $event->nama }}</strong><div class="text-muted small">{{ ucfirst($event->peran) }} · {{ $event->nip }}</div><div class="text-primary mt-2">{{ $event->tanggal_masuk?->translatedFormat('d F Y') }}</div></div></div>@empty<div class="col-12 text-muted">Belum ada agenda user pada bulan ini.</div>@endforelse</div></div></div>
@endsection
