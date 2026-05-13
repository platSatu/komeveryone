@extends('layouts.KomDigital')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">Edit Donasi</div>
            <div class="card-body">

                {{-- Alert --}}
               @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-warning">
                        <ul>
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('kom.super_persembahan.update', Crypt::encrypt($donasi->id)) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Amount --}}
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" step="0.01" class="form-control" name="amount" value="{{ old('amount', $donasi->amount) }}" required>
                    </div>

                    {{-- Fee --}}
                    <div class="mb-3">
                        <label for="fee_payment_gateway" class="form-label">Fee Payment Gateway</label>
                        <input type="number" step="0.01" class="form-control" name="fee_payment_gateway" value="{{ old('fee_payment_gateway', $donasi->fee_payment_gateway) }}" required>
                    </div>

                    {{-- Payment Type --}}
                    <div class="mb-3">
                        <label for="payment_type" class="form-label">Payment Type</label>
                        <select name="payment_type" class="form-control">
                            @php
                                $types = ['bank_transfer', 'credit_card', 'gopay', 'shopeepay', 'qris', 'indomaret', 'alfamart'];
                            @endphp
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ $donasi->payment_type === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Transaction Status --}}
                    <div class="mb-3">
                        <label for="transaction_status" class="form-label">Transaction Status</label>
                        <select name="transaction_status" class="form-control">
                            @foreach(['settlement', 'capture', 'expire', 'deny'] as $status)
                                <option value="{{ $status }}" {{ $donasi->transaction_status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="mb-3">
                        <label for="status" class="form-label">Status (Sistem)</label>
                        <input type="text" class="form-control" name="status" value="{{ old('status', $donasi->status) }}" required>
                    </div>

                    <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                    <a href="{{ route('kom.super_persembahan.index') }}" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
