@extends('layouts.app')

@section('title', 'Kompetensi Seni - SDM Villa Merah')
@section('page_title', 'Kompetensi Seni')

@section('content')
    @include('shared.srs-placeholder', [
        'description' => 'Kontrol mandiri untuk melihat beban klaster mengajar yang diampu.',
        'items' => [
            ['title' => 'Klaster seni', 'body' => 'Bidang kompetensi yang sedang diampu pegawai.'],
            ['title' => 'Beban mengajar', 'body' => 'Ringkasan kelas atau studio terkait kompetensi tersebut.'],
        ],
    ])
@endsection
