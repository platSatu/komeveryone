@extends('layouts.KomDigital')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4>Tambah Peserta ke Zoom Meeting</h4>

                    {{-- Tampilkan notifikasi sukses atau error --}}
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    {{-- Form Tambah Peserta --}}
                    <form action="{{ route('kom.kelas_tutor_diklat.addPesertaZoom.submit', ['kelasId' => request('kelasId')]) }}" method="POST">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="name">Nama Peserta</label>
                            <input type="text" name="name" class="form-control" required placeholder="Contoh: Budi Santoso">
                        </div>

                        <div class="form-group mb-3">
                            <label for="email">Email Peserta</label>
                            <input type="email" name="email" class="form-control" required
                                placeholder="Contoh: budi@example.com">
                        </div>

                        <button type="submit" class="btn btn-primary">Tambah ke Zoom</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection