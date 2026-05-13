@extends('layouts.kom')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Mutasi Donasi - Admin KOM</h5>
            </div>
            <div class="card-body">
                {{-- Filter Rayon --}}
                <form method="GET" class="mb-4">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-6">
                            <label for="rayon_id" class="form-label">Filter berdasarkan Rayon</label>
                            <select name="rayon_id" id="rayon_id" class="form-control">
                                <option value="">-- Semua Rayon --</option>
                                @foreach($rayons as $rayon)
                                    <option value="{{ $rayon->id }}" {{ $rayonId == $rayon->id ? 'selected' : '' }}>
                                        {{ $rayon->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary w-100" type="submit">Filter</button>
                        </div>
                    </div>
                </form>

                {{-- Ringkasan --}}
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card border-left-success shadow-sm mb-3">
                            <div class="card-body">
                                <strong>Total Donasi untuk Cabang:</strong><br>
                                Rp {{ number_format($totalDonate, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-left-info shadow-sm mb-3">
                            <div class="card-body">
                                <strong>Total Fee Admin KOM:</strong><br>
                                Rp {{ number_format($totalFee, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-left-warning shadow-sm mb-3">
                            <div class="card-body">
                                <strong>Total Transaksi (Gross):</strong><br>
                                <span class="h5">Rp {{ number_format($totalPaid, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tabel Riwayat --}}
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Riwayat Donasi</h6>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Modul</th>
                                    <th>Jadwal</th>
                                    <th>Donasi (Cabang)</th>
                                    <th>Fee</th>
                                    <th>Total Dibayar</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($donations as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->komUser->username ?? '-' }}</td>
                                        <td>{{ $item->komModul->title ?? '-' }}</td>
                                        <td>
                                            {{ optional($item->komSchedule)->datetime
                                                ? \Carbon\Carbon::parse($item->komSchedule->datetime)->format('d M Y H:i')
                                                : '-' }}
                                        </td>
                                        <td>Rp {{ number_format($item->donate, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($item->fee, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($item->paid_amount_value, 0, ',', '.') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Belum ada data donasi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div> {{-- End card-body --}}
            </div>
        </div>
    </div>
</div>
@endsection
