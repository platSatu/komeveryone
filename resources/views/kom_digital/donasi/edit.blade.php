@extends('layouts.kom')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h2>Edit Donasi</h2>

                    <form action="{{ route('kom.donasi.update', $donasi->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="partner_service_id">Partner Service ID</label>
                            <input type="text" name="partner_service_id" id="partner_service_id" class="form-control" value="{{ $donasi->partner_service_id }}" required>
                        </div>

                        <!-- Add other form fields as needed -->

                        <div class="form-group">
                            <button type="submit" class="btn btn-success">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
