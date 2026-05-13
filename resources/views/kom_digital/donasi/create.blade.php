@extends('layouts.kom')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h2>Tambah Donasi</h2>

                    <form action="{{ route('kom.donasi.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="partner_service_id">Partner Service ID</label>
                            <input type="text" name="partner_service_id" id="partner_service_id" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="customer_no">Customer No</label>
                            <input type="text" name="customer_no" id="customer_no" class="form-control" required>
                        </div>

                        <!-- Add other form fields as needed -->

                        <div class="form-group">
                            <button type="submit" class="btn btn-success">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
