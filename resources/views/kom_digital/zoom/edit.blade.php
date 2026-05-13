@extends('layouts.kom')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header"><strong>Edit Data Zoom</strong></div>
            <div class="card-body">

                <form action="{{ route('kom.zoom.update', $zoom->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @include('kom_digital.zoom._form', ['button' => 'Update'])

                </form>
            </div>
        </div>
    </div>
</div>
@endsection
