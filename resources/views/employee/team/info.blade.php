@extends('layouts.app')

@section('title', 'Informasi Atasan & Tim - SDM Villa Merah')
@section('page_title', 'Informasi Atasan & Tim')

@section('content')
    @include('shared.srs-placeholder', [
        'description' => 'Kontak cepat rekan se-divisi dan profil manajer langsung.',
        'items' => [
            ['title' => 'Atasan langsung', 'body' => 'Data diambil dari relasi id atasan pada profil karyawan.'],
            ['title' => 'Rekan tim', 'body' => 'Menampilkan daftar rekan dalam divisi yang sama.'],
        ],
    ])
@endsection
