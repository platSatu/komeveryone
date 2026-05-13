@extends('layouts.kom')

@section('content')
    
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Total Saldo Donasi</h4>
                    </div>
                    <div class="card-body">
                    <div class="table-responsive b-0" data-pattern="priority-columns">
                        <table class="table table-bordered w-50">
                            <tbody>
                                <tr>
                                    <th>Nama Rayon</th>
                                    <td>{{ $rayonName }}</td>
                                </tr>
                                <tr>
                                    <th>Nama User</th>
                                    <td>{{ $userName }}</td>
                                </tr>
                                <tr>
                                    <th>Total Saldo</th>
                                    <td>Rp {{ number_format($totalSaldo, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                        </div>
                        <button class="btn btn-secondary mb-3" id="btnTarik">Tarik Saldo</button>

                        {{-- Form tarik saldo --}}
                        <form action="" method="POST" enctype="multipart/form-data" class="w-50" id="formTarik" style="display: none;">
                            @csrf
                            <div class="form-group">
                                <label>Nama Pemilik Rekening</label>
                                <input type="text" name="account_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>No Rekening</label>
                                <input type="text" name="account_number" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Nama Bank</label>
                                <input type="text" name="bank_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Jumlah Penarikan</label>
                                <input type="number" name="amount" class="form-control" max="{{ $totalSaldo }}" required>
                                <small class="text-muted">Maksimal: Rp {{ number_format($totalSaldo, 0, ',', '.') }}</small>
                            </div>

                            <button type="submit" class="btn btn-primary">Kirim Permintaan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    

    <script>
        document.getElementById('btnTarik').addEventListener('click', function () {
            const form = document.getElementById('formTarik');
            form.style.display = 'block';
            this.style.display = 'none'; // Sembunyikan tombol "Tarik Saldo"
        });
    </script>
@endsection
