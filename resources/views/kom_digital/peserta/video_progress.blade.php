@extends('layouts.kom')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Progress Video Saya</h3>
            </div>

            <div class="card-body">
                @if($progress->isEmpty())
                    <div class="alert alert-info mb-0">
                        Belum ada progress video yang tercatat.
                    </div>
                @else
                <div class="table-responsive b-0" data-pattern="priority-columns">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Judul Video</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($progress as $index => $item)
                                <tr>
                                    <td>{{ $index + $progress->firstItem() }}</td>
                                    <td>{{ $item->video->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $item->is_locked ? 'secondary' : 'success' }}">
                                            {{ $item->is_locked ? 'Terkunci' : 'Terbuka' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                   
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
