@extends('layouts.kom')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Buku KOM</h3>
                <div class="card-tools">
                    <a href="{{ route('kom.kode_buku.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
                </div>
            </div>

            <div class="card-body">
                <form action="{{ route('kom.kode_buku.update', $book->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="kom_user_id">Pilih User</label>
                        <select name="kom_user_id" class="form-control" required>
                            @foreach($komUsers as $komUser)
                                <option value="{{ $komUser->id }}" {{ $book->kom_user_id == $komUser->id ? 'selected' : '' }}>
                                    {{ $komUser->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="kom_class_id">Pilih Kelas</label>
                        <select name="kom_class_id" class="form-control" required>
                            @foreach($komClasses as $komClass)
                                <option value="{{ $komClass->id }}" {{ $book->kom_class_id == $komClass->id ? 'selected' : '' }}>
                                    {{ $komClass->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="kom_certificate_id">Pilih Sertifikat</label>
                        <select name="kom_certificate_id" class="form-control" required>
                            @foreach($komCertificates as $komCertificate)
                                <option value="{{ $komCertificate->id }}" {{ $book->kom_certificate_id == $komCertificate->id ? 'selected' : '' }}>
                                    {{ $komCertificate->no_sertifikat }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="kode_buku">Kode Buku</label>
                        <input type="text" name="kode_buku" class="form-control" value="{{ $book->kode_buku }}" required>
                    </div>

                    <div class="form-group">
                        <label for="tgl_aktifasi">Tanggal Aktivasi</label>
                        <input type="date" name="tgl_aktifasi" class="form-control" value="{{ \Carbon\Carbon::parse($book->tgl_aktifasi)->format('Y-m-d') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" class="form-control" required>
                            <option value="terpakai" {{ $book->status ? 'selected' : '' }}>Aktif</option>
                            <option value="belum_terpakai" {{ !$book->status ? 'selected' : '' }}>Belum Aktif</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
