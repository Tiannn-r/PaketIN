@extends('layout.app')

@section('judul', 'Dashboard Manager')

@section('isi')
    <h1>Dashboard Manager</h1>
    <p>Halo, {{ request()->user()->nama }}</p>
@endsection