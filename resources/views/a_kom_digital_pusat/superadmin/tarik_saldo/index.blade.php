@extends('layouts.KomDigital')
@section('content')
    <div class="row">
        <div class="col-md-12">
            @foreach (['success' => 'success', 'error' => 'danger'] as $kunci => $warna)
                @if (session($kunci))
                    <div class="alert alert-{{ $warna }}">{{ session($kunci) }}</div>
                @endif
            @endforeach
            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif
            @unless ($payoutAktif)
                <div class="alert alert-info">Tarik dana lewat Bizbos belum aktif — pengajuan diproses manual. Aktifkan di menu Payment Gateway.</div>
            @endunless
            {{-- Card filter & info --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                    <form action="" method="GET" class="form-inline mb-2 mb-md-0 d-flex align-items-center flex-wrap">
                        <label for="status" class="mr-2 mb-0">Filter Status:</label>
                        <select name="status" id="status" class="form-control mr-2" style="min-width: 180px;">
                            <option value="">-- Semua Status --</option>
                            <option value="pending" {{ ($filters['status'] ?? '') == 'pending' ? 'selected' : '' }}>
                                Menunggu Persetujuan</option>
                            <option value="approved" {{ ($filters['status'] ?? '') == 'approved' ? 'selected' : '' }}>
                                Disetujui</option>
                            <option value="completed" {{ ($filters['status'] ?? '') == 'completed' ? 'selected' : '' }}>
                                Selesai</option>
                            <option value="rejected" {{ ($filters['status'] ?? '') == 'rejected' ? 'selected' : '' }}>
                                Ditolak</option>
                            <option value="failed" {{ ($filters['status'] ?? '') == 'failed' ? 'selected' : '' }}>
                                Gagal (saldo kembali)</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">Tampilkan</button>
                    </form>

                    <div>
                        <strong>Total Pengajuan:</strong> {{ $tarikSaldos->total() }}
                    </div>
                </div>
            </div>

            {{-- Card tabel data --}}
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Pengajuan Penarikan Saldo</h5>
                </div>

                <div class="card-body">
                    @if ($tarikSaldos->count() > 0)
                        <div class="table-responsive b-0" data-pattern="priority-columns">
                            <table id="tech-companies-1" class="table table-striped text-truncate">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Diajukan Oleh</th>
                                        <th>Cabang</th>
                                        <th>Kelas Tutorial</th>
                                        <th>Jumlah</th>
                                        <th>Tanggal Penarikan</th>
                                        <th>Status</th>
                                        <th>Catatan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tarikSaldos as $index => $item)
                                        <tr>
                                            <td>{{ $tarikSaldos->firstItem() + $index }}</td>
                                            <td>{{ $item->user->name ?? '-' }}</td>
                                            <td>{{ $item->cabang->name ?? '-' }}</td>
                                            <td>{{ optional($item->userClassTutorial->classTutorial ?? null)->topic ?? '-' }}
                                            </td>
                                            <td>{{ $item->formatted_jumlah }}</td>
                                            <td>
                                                {{ optional($item->tanggal_penarikan)->format('d M Y H:i') ?? '-' }}
                                            </td>
                                            <td>
                                                <span class="badge {{ $item->status_badge_class }} text-white">
                                                    {{ $item->status_label }}
                                                </span>
                                                @if ($item->bizbos_status)
                                                    <small class="d-block text-muted">Bizbos: {{ $item->bizbos_status === 'mengirim' ? 'belum pasti, klik Cek Status' : $item->bizbos_status }}</small>
                                                @endif
                                                @if ($item->alasan || ($item->status === 'pending' && $item->gagal_alasan))
                                                    <small class="d-block text-danger text-wrap" style="max-width:220px">{{ $item->alasan ?? $item->gagal_alasan }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $item->catatan ?? '-' }}</td>
                                            <td>
                                                @php $kunciId = Crypt::encrypt($item->id); @endphp
                                                @if ($item->status === 'pending' && $payoutAktif)
                                                    <form action="{{ route('kom.super_tarik-saldo.kirim', $kunciId) }}" method="POST" class="d-inline"
                                                        data-konfirmasi="Kirim {{ $item->formatted_jumlah }} ke rekening {{ $item->user->name ?? '-' }} lewat Bizbos?"
                                                        onsubmit="if (!confirm(this.dataset.konfirmasi)) return false; this.querySelector('button').disabled = true; return true;">
                                                        @csrf
                                                        <button class="btn btn-sm btn-success" type="submit">Setujui &amp; Kirim</button>
                                                    </form>
                                                @endif
                                                @if ($item->sudahKeBizbos() && ! in_array($item->status, ['completed', 'failed', 'rejected'], true))
                                                    <form action="{{ route('kom.super_tarik-saldo.cek', $kunciId) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button class="btn btn-sm btn-info" type="submit">Cek Status</button>
                                                    </form>
                                                @endif
                                                @if ($item->status === 'pending' && ! $item->sudahKeBizbos())
                                                    <form action="{{ route('kom.super_tarik-saldo.tolak', $kunciId) }}" method="POST" class="d-inline"
                                                        onsubmit="var a = prompt('Alasan penolakan:'); if (!a) return false; this.alasan.value = a; return true;">
                                                        @csrf
                                                        <input type="hidden" name="alasan">
                                                        <button class="btn btn-sm btn-warning" type="submit">Tolak</button>
                                                    </form>
                                                @endif
                                                @unless ($item->sudahKeBizbos())
                                                <form action="{{ route('kom.super_tarik-saldo.destroy', $kunciId) }}"
                                                    method="POST" onsubmit="return confirm('Yakin ingin hapus pengajuan ini?')"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                                                </form>
                                                @endunless
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="d-flex justify-content-center mt-3">
                            {{ $tarikSaldos->appends($filters)->links('pagination::bootstrap-4') }}
                        </div>
                    @else
                        <p class="text-center">Data tidak ditemukan.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
