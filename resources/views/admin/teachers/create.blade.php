@extends('layouts.app')

@section('title', 'Tambah Pengajar - SDM Villa Merah')
@section('page_title', 'Tambah Pengajar')

@section('content')
    @include('shared.srs-placeholder', [
        'description' => 'Form input profil pengajar baru untuk tim akademik.',
        'items' => [
            ['title' => 'Profil akademik', 'body' => 'Divisi akademik, kampus asal, pelatihan, dan sertifikat.'],
            ['title' => 'Identitas', 'body' => 'NIP, nama lengkap, KTP, KK, status pernikahan, dan tanggal masuk.'],
        ],
    ])
@endsection
