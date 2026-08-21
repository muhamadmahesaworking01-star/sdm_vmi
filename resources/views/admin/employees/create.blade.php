@extends('layouts.app')

@section('title', 'Tambah Karyawan - SDM Villa Merah')
@section('page_title', 'Tambah Karyawan')

@section('content')
    @include('shared.srs-placeholder', [
        'description' => 'Form input profil karyawan baru untuk staf operasional kantor.',
        'items' => [
            ['title' => 'Identitas', 'body' => 'NIP, nama lengkap, KTP, KK, golongan darah, dan status pernikahan.'],
            ['title' => 'Kepegawaian', 'body' => 'Jabatan atau divisi, atasan, tanggal masuk, dan status aktif.'],
        ],
    ])
@endsection
