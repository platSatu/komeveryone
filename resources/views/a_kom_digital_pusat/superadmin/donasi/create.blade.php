@extends('layouts.KomDigital')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Tambah Donasi</h4>
                </div>

                <div class="card-body">

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('kom.super_persembahan.store') }}" method="POST">
                        @csrf

                     <div class="mb-3">
    <label for="user_id" class="form-label">User</label>
    <select name="user_id" id="user_id" class="form-select form-control" required>
        <option value="">-- Pilih User --</option>
        @foreach($users as $user)
            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                {{ $user->name ?? 'User ID: ' . $user->id }}
            </option>
        @endforeach
    </select>
</div>



                      <div class="mb-3">
    <label for="userclasstutorial_id" class="form-label">User Class Tutorial</label>
    <select name="userclasstutorial_id" id="userclasstutorial_id" class="form-select form-control">
        <option value="">-- Pilih User Class Tutorial --</option>
        @foreach($userClassTutorials as $uct)
            <option value="{{ $uct->id }}" {{ old('userclasstutorial_id') == $uct->id ? 'selected' : '' }}>
                {{ optional($uct->user)->name ?? 'Tanpa User' }} - 
                {{ optional($uct->modul)->title ?? 'Tanpa Modul' }} - 
                {{ optional($uct->kelas)->name ?? 'Tanpa Kelas' }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label for="modul_id" class="form-label">Modul</label>
    <select name="modul_id" id="modul_id" class="form-select form-control">
        <option value="">-- Pilih Modul --</option>
        @foreach($moduls as $modul)
            <option value="{{ $modul->id }}" {{ old('modul_id') == $modul->id ? 'selected' : '' }}>
                {{ $modul->name }}
            </option>
        @endforeach
    </select>
</div>



                       <div class="mb-3">
    <label for="class_id" class="form-label">Kelas</label>
    <select name="class_id" id="class_id" class="form-select form-control">
        <option value="">-- Pilih Kelas --</option>
        @foreach($classes as $class)
            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                {{ $class->name ?? 'Kelas ID: ' . $class->id }}
            </option>
        @endforeach
    </select>
</div>


                       <div class="mb-3">
    <label for="cabang_id" class="form-label">Cabang</label>
    <select name="cabang_id" id="cabang_id" class="form-select form-control">
        <option value="">-- Pilih Cabang --</option>
        @foreach($cabangs as $cabang)
            <option value="{{ $cabang->id }}" {{ old('cabang_id') == $cabang->id ? 'selected' : '' }}>
                {{ $cabang->name ?? 'Cabang ID: ' . $cabang->id }}
            </option>
        @endforeach
    </select>
</div>


                      
                        <div class="mb-3">
                            <label for="amount" class="form-label">Jumlah Donasi (Rp)</label>
                            <input type="number" step="0.01" name="amount" id="amount" class="form-control" value="{{ old('amount') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="fee_payment_gateway" class="form-label">Biaya Payment Gateway (Rp)</label>
                            <input type="number" step="0.01" name="fee_payment_gateway" id="fee_payment_gateway" class="form-control" value="{{ old('fee_payment_gateway') }}">
                        </div>

                        <div class="mb-3">
                            <label for="payment_type" class="form-label">Tipe Pembayaran</label>
                            <select name="payment_type" id="payment_type" class="form-select form-control" required>
                                <option value="">-- Pilih Tipe Pembayaran --</option>
                                <option value="bank_transfer" {{ old('payment_type') == 'bank_transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                <option value="credit_card" {{ old('payment_type') == 'credit_card' ? 'selected' : '' }}>Kartu Kredit</option>
                                <option value="gopay" {{ old('payment_type') == 'gopay' ? 'selected' : '' }}>GoPay</option>
                                <option value="shopeepay" {{ old('payment_type') == 'shopeepay' ? 'selected' : '' }}>ShopeePay</option>
                                <option value="qris" {{ old('payment_type') == 'qris' ? 'selected' : '' }}>QRIS</option>
                                <option value="indomaret" {{ old('payment_type') == 'indomaret' ? 'selected' : '' }}>Indomaret</option>
                                <option value="alfamart" {{ old('payment_type') == 'alfamart' ? 'selected' : '' }}>Alfamart</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="transaction_time" class="form-label">Waktu Transaksi</label>
                            <input type="datetime-local" name="transaction_time" id="transaction_time" class="form-control" value="{{ old('transaction_time') }}">
                        </div>

                        <div class="mb-3">
                            <label for="transaction_status" class="form-label">Status Transaksi</label>
                            <input type="text" name="transaction_status" id="transaction_status" class="form-control" value="{{ old('transaction_status') }}">
                        </div>

                        

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="failed" {{ old('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Donasi</button>
                        <a href="{{ route('kom.super_persembahan.index') }}" class="btn btn-secondary">Kembali</a>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>	

    <!-- Select2 CSS -->


<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('#user_id').select2({
            placeholder: '-- Pilih User --',
            allowClear: true
        });
    });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	
@endsection
