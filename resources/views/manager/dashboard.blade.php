<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Dashboard Manager</title></head>
<body>
    @extends('layout.app')
    @section('judul', 'Dashboard Manager')
    @section('isi')
        <h1>Dashboard Manager</h1>
        <p>Halo, {{ request()->user()->nama }}</p>
    @endsection
</body>
</html>