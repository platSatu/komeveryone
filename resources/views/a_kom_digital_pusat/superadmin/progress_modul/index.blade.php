@extends('layouts.KomDigital')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Progress Modul Pengguna</h3>
                <div class="card-tools">
                    <a href="" class="btn btn-primary btn-sm">+ Tambah Progress</a>
                </div>
            </div>

            <div class="card-body">
               

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama User</th>
                            <th>Modul</th>
                            <th>Video Terakhir</th>
                            <th>Urutan Video</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection