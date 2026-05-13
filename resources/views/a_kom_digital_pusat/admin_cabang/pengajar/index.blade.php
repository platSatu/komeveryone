@extends('layouts.KomDigital')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                @include('a_kom_digital_pusat.admin_cabang.user.components._header')
                <div class="card-body">
                    <!-- Form Filter dan Pencarian -->
                    @include('a_kom_digital_pusat.admin_cabang.user.components._searching')
                    <!-- Tabel Daftar Pengguna -->
                    <div class="table-responsive">
                        @include('a_kom_digital_pusat.admin_cabang.user.components._dataTable')
                    </div>
                    <!-- Pagination -->
                    @include('a_kom_digital_pusat.admin_cabang.user.components._pagination')
                </div>
            </div>
        </div>
    </div>
@endsection
