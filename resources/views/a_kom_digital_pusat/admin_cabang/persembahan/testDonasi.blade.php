@extends('layouts.KomDigital')

@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h2>Data Donasi Mentah (Tanpa Filter)</h2>

                @if (isset($error))
                    <div class="alert alert-danger">{{ $error }}</div>
                @endif
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User ID</th>
                                <th>UserClassTutorial ID</th>
                                <th>Modul ID</th>
                                <th>Class ID</th>
                                <th>Cabang ID</th>
                                <th>Order ID</th>
                                <th>Amount</th>
                                <th>Fee PG</th>
                                <th>Payment Type</th>
                                <th>Transaction Time</th>
                                <th>Transaction Status</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($donasiData as $donasi)
                                <tr>
                                    <td>{{ $donasi->id }}</td>
                                    <td>{{ $donasi->user->name ?? '-' }}</td>
                                    <td>{{ $donasi->userClassTutorial->class_tutorial_id ?? '-' }}</td>
                                    
                                    <td>{{ $donasi->modul->name ?? '-' }}</td>
                                    <td>{{ $donasi->kelas->name ?? '-' }}</td>
                                    <td>{{ $donasi->cabang->name ?? '-' }}</td>
                                    <td>{{ $donasi->order_id }}</td>
                                    <td>{{ $donasi->amount }}</td>
                                    <td>{{ $donasi->fee_payment_gateway }}</td>
                                    <td>{{ $donasi->payment_type }}</td>
                                    <td>{{ $donasi->transaction_time }}</td>
                                    <td>{{ $donasi->transaction_status }}</td>
                                    <td>{{ $donasi->status }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13">Tidak ada data ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
