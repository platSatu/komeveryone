@extends('layouts.kom')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Jadwal Kelas</h4>
            </div>
            <div class="card-body">
                <form id="editScheduleForm" action="{{ route('kom.schedules.update', $schedule->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Pengajar --}}
                    <div class="form-group">
                        <label for="pengajar_id">Pengajar</label>
                        <select name="pengajar_id" id="pengajar_id" class="form-control" required>
                            <option value="">-- Pilih Pengajar --</option>
                            @foreach ($komUsers as $user)
                                <option value="{{ $user->id }}" {{ $schedule->pengajar_id == $user->id ? 'selected' : '' }}>
                                    {{ $user->username }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Rayon --}}
                    <div class="form-group">
                        <label for="rayon_name">Rayon</label>
                        <input type="text" id="rayon_name" class="form-control" value="{{ $rayons->where('id', $schedule->rayon_id)->first()->name ?? '' }}" readonly>
                    </div>

                    {{-- Kelas --}}
                    <div class="form-group">
                        <label for="kom_class_id">Kelas</label>
                        <select name="kom_class_id" id="kom_class_id" class="form-control" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($komClasses as $class)
                                <option value="{{ $class->id }}" {{ $schedule->kom_class_id == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Modul --}}
                    <div class="form-group">
                        <label for="kom_modul_id">Modul</label>
                        <select name="kom_modul_id" id="kom_modul_id" class="form-control" required>
                            <option value="">-- Pilih Modul --</option>
                            @foreach ($komModuls as $modul)
                                <option value="{{ $modul->id }}" {{ $schedule->kom_modul_id == $modul->id ? 'selected' : '' }}>
                                    {{ $modul->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tanggal & Waktu --}}
                    <div class="form-group">
                        <label for="datetime">Tanggal & Waktu</label>
                        <input type="datetime-local" name="datetime" id="datetime" class="form-control" value="{{ \Carbon\Carbon::parse($schedule->datetime)->format('Y-m-d\TH:i') }}" required>
                    </div>

                    {{-- Kuota --}}
                    <div class="form-group">
                        <label for="quota">Kuota</label>
                        <input type="number" name="quota" id="quota" class="form-control" value="{{ $schedule->quota }}" min="1" required>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea name="description" id="description" class="form-control" rows="3">{{ $schedule->description }}</textarea>
                    </div>

                    {{-- Status --}}
                    <div class="form-group">
                        <label for="status_kelas">Status</label>
                        <select name="status" id="status_kelas" class="form-control" required>
                            <option value="aktif" {{ $schedule->status === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ $schedule->status === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success">Update</button>
                    <a href="{{ route('kom.schedules.index') }}" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Tambahkan script ini --}}

<script>
document.getElementById('status_kelas').addEventListener('change', function() {
    if (this.value === 'nonaktif') {
        const confirmed = confirm('Yakin Anda akan menghapus kelas ini?');
        if (!confirmed) {
            // Kalau batal, kembalikan pilihan ke 'aktif'
            this.value = 'aktif';
        }
    }
});
</script>



@endsection
