@extends('layouts.kom')

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card">
                <div class="card-header"><strong>Tambah Data Zoom</strong></div>
                <div class="card-body">

                    <form action="{{ route('kom.zoom.store') }}" method="POST">
                        @csrf

                        @include('kom_digital.zoom._form', ['button' => 'Simpan'])

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
