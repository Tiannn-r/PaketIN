<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Dashboard Admin</title></head>
<body>
    @extends('layout.app')
    @section('judul', 'Dashboard Admin')
    @section('isi')
        <h1>Dashboard Admin</h1>
        <p>Halo, {{ request()->user()->nama }}</p>
    @endsection

</body>
</html>