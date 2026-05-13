@extends('layouts.kom')
@section('content')

<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Donasi Sekarang</h4>
        </div>
        <div class="card-body">

            <p class="mb-4">Terima kasih telah bersedia berdonasi. Klik tombol di bawah ini untuk melanjutkan ke proses pembayaran.</p>

            <form method="POST" action="{{ route('kom.donasi.store') }}">
                @csrf
                <button type="submit" class="btn btn-success btn-lg w-100">Donasi Sekarang</button>
            </form>

        </div>
    </div>
</div>

@endsection
