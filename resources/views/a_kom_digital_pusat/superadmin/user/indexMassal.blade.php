@extends('layouts.KomDigital')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Daftar Input Massal</h4>
                    <div class="d-flex align-items-center gap-2">

                       
                        <!-- Tombol tambah pengguna -->
                        

                        <a href="{{ route('kom.inputMassal.create') }}" class="btn btn-secondary btn-sm mr-3">Input
                            Massal</a>
                       

                    </div>
                </div>
                <div class="card-body">

                </div>
            </div>
        </div>
    </div>
@endsection
